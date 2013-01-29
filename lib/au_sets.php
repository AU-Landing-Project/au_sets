<?php


function au_sets_get_icon($set, $size = "medium") {

  if (!elgg_instanceof($set, 'object', 'au_set')) {
	header("HTTP/1.1 404 Not Found");
	exit;
  }

  // If is the same ETag, content didn't changed.
  $etag = $set->icontime . $set->getGUID();
  if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == "\"$etag\"") {
	header("HTTP/1.1 304 Not Modified");
	exit;
  }
  
  if (!in_array($size, array('large', 'medium', 'small', 'tiny', 'master', 'topbar'))) {
	$size = "medium";
  }

  $filehandler = new ElggFile();
  $filehandler->owner_guid = $set->owner_guid;
  $filehandler->setFilename("sets/" . $set->guid . $size . ".jpg");

  $success = false;
  if ($filehandler->open("read")) {
	if ($contents = $filehandler->read($filehandler->size())) {
		$success = true;
	}
  }

  if (!$success) {
	$location = elgg_get_plugins_path() . "au_sets/graphics/default{$size}.gif";
	$contents = @file_get_contents($location);
  }

  header("Content-type: image/jpeg");
  header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime("+10 days")), true);
  header("Pragma: public");
  header("Cache-Control: public");
  header("Content-Length: " . strlen($contents));
  header("ETag: \"$etag\"");
  echo $contents;
}

/**
 * Get page components to view a set.
 *
 * @param int $guid GUID of a set entity.
 * @return array
 */
function au_sets_get_page_content_read($guid = NULL) {

	$params = array();

	$set = get_entity($guid);

	// no header or tabs for viewing an individual blog
	$params['filter'] = '';

	if (!elgg_instanceof($set, 'object', 'au_set')) {
		register_error(elgg_echo('noaccess'));
		$_SESSION['last_forward_from'] = current_page_url();
		forward('');
	}

	elgg_set_page_owner_guid($set->guid);
	$params['title'] = $set->title;

	$container = $set->getContainerEntity();
	$crumbs_title = $container->name;
	if (elgg_instanceof($container, 'group')) {
		elgg_push_breadcrumb($crumbs_title, "sets/group/$container->guid/all");
	} else {
		elgg_push_breadcrumb($crumbs_title, "sets/owner/$container->username");
	}

	elgg_push_breadcrumb($set->title);
	$entity_view = elgg_view_entity($set, array('full_view' => true));
	$content = elgg_view_layout('widgets', array('content' => $entity_view, 'exact_match' => true));
	$menu = elgg_view_menu('entity', array(
	  'entity' => $set,
	  'handler' => 'au_set',
	  'sort_by' => 'priority',
	  'class' => 'elgg-menu-hz au-set-title-menu',
	));
	$params['content'] = $menu . '<div class="au-set-widgets-wrapper">' . $content . '</div>';
	$params['class'] = 'au-set';

	elgg_set_context('sets');
	$body = elgg_view_layout('one_column', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
}

/**
 * Get page components to list a user's or all sets.
 *
 * @param int $container_guid The GUID of the page owner or NULL for all sets
 * @return array
 */
function au_sets_get_page_content_list($container_guid = NULL) {

	$return = array();

	$return['filter_context'] = $container_guid ? 'mine' : 'all';

	$options = array(
		'type' => 'object',
		'subtype' => 'au_set',
		'full_view' => false,
	);

	$current_user = elgg_get_logged_in_user_entity();

	if ($container_guid) {
		// access check for closed groups
		group_gatekeeper();

		$options['container_guid'] = $container_guid;
		$container = get_entity($container_guid);
		if (!$container) {
		  // @TODO
		  // what do we do if we don't have a container?
		}
		$return['title'] = elgg_echo('au_sets:title:user_sets', array($container->name));

		$crumbs_title = $container->name;
		elgg_push_breadcrumb($crumbs_title);

		if ($current_user && ($container_guid == $current_user->guid)) {
			$return['filter_context'] = 'mine';
		} else if (elgg_instanceof($container, 'group')) {
			$return['filter'] = false;
		} else {
			// do not show button or select a tab when viewing someone else's posts
			$return['filter_context'] = 'none';
		}
	} else {
		$return['filter_context'] = 'all';
		$return['title'] = elgg_echo('au_sets:title:all_sets');
		elgg_pop_breadcrumb();
		elgg_push_breadcrumb(elgg_echo('au_sets:sets'));
	}

	elgg_register_title_button();

	$list = elgg_list_entities_from_metadata($options);
	if (!$list) {
		$return['content'] = elgg_echo('au_sets:none');
	} else {
		$return['content'] = $list;
	}

	return $return;
}

/**
 * Get page components to list of the user's friends' sets.
 *
 * @param int $user_guid
 * @return array
 */
function au_sets_get_page_content_friends($user_guid) {

	$user = get_user($user_guid);
	if (!$user) {
		forward('sets/all');
	}

	$return = array();

	$return['filter_context'] = 'friends';
	$return['title'] = elgg_echo('au_sets:title:friends');

	$crumbs_title = $user->name;
	elgg_push_breadcrumb($crumbs_title, "sets/owner/{$user->username}");
	elgg_push_breadcrumb(elgg_echo('friends'));

	elgg_register_title_button();

	if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
		$return['content'] .= elgg_echo('friends:none:you');
		return $return;
	} else {
		$options = array(
			'type' => 'object',
			'subtype' => 'au_set',
			'full_view' => FALSE,
		);

		foreach ($friends as $friend) {
			$options['container_guids'][] = $friend->getGUID();
		}

		$list = elgg_list_entities($options);
		if (!$list) {
			$return['content'] = elgg_echo('au_sets:none');
		} else {
			$return['content'] = $list;
		}
	}

	return $return;
}


/**
 * Get page components to edit/create a set.
 *
 * @param string  $page     'edit' or 'new'
 * @param int     $guid     GUID of set or container
 * @param int     $revision Annotation id for revision to edit (optional)
 * @return array
 */
function au_sets_get_page_content_edit($page, $guid = 0) {

	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'set-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$set = get_entity((int)$guid);

		$title = elgg_echo('au_sets:edit');

		if (elgg_instanceof($set, 'object', 'au_set') && $set->canEdit()) {
		  
			$return['filter'] = elgg_view('au_sets/navigation/edit', array('entity' => $set));
			$vars['entity'] = $set;

			$title .= ": \"$set->title\"";

			$body_vars = au_sets_prepare_form_vars($set);
			$form_vars = array('enctype' => 'multipart/form-data');

			elgg_push_breadcrumb($set->title, $set->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));

			$content = elgg_view_form('au_sets/save', $form_vars, $body_vars);
		} else {
			$content = elgg_echo('au_set:error:cannot_edit');
		}
	} else {
		elgg_push_breadcrumb(elgg_echo('au_sets:add'));
		$body_vars = au_sets_prepare_form_vars(null);
		$form_vars = array('enctype' => 'multipart/form-data');

		$title = elgg_echo('au_sets:add');
		$content = elgg_view_form('au_sets/save', $form_vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	return $return;	
}


function au_sets_get_set_list($guid) {
  $set = get_entity($guid);
  if (!elgg_instanceof($set, 'object', 'au_set') || !$set->canEdit()) {
	register_error(elgg_echo('au_sets:error:invalid:set'));
	forward(REFERER);
  }
  
  elgg_push_breadcrumb($set->title, $set->getURL());
  elgg_push_breadcrumb(elgg_echo('List'));
  
  $context = elgg_get_context();
  elgg_set_context('au_sets_profile');
  $list = elgg_list_entities_from_relationship(array(
	  'relationship_guid' => $set->guid,
	  'relationship' => AU_SETS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'full_view' => false,
	  'limit' => 10,
	  'order_by' => 'r.time_created DESC'
  ));
  
  $list .= '<div class="au-sets-guid-markup" data-set="' . $set->guid . '"></div>';
  elgg_set_context($context);
  
  $params = array(
	'filter' => elgg_view('au_sets/navigation/edit', array('entity' => $set)),
	'content' => $list,
	'title' => $set->title
  );
  
  return $params;
}

/**
 * Returns bool whether the entity is already pinned
 * assumes $entity and $set are valid objects
 * 
 * @param type $entity
 * @param type $set
 */
function au_sets_is_pinned($entity, $set) {
  return check_entity_relationship($entity->getGUID(), AU_SETS_PINNED_RELATIONSHIP, $set->getGUID());
}


/**
 * Pull together set variables for the save form
 *
 * @param ElggObject       $set
 * @return array
 */
function au_sets_prepare_form_vars($set = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_PRIVATE,
		'comments_on' => 'On',
		'tags' => NULL,
		'container_guid' => NULL,
		'guid' => NULL,
	);

	if ($set) {
		foreach (array_keys($values) as $field) {
			if (isset($set->$field)) {
				$values[$field] = $set->$field;
			}
		}
	}

	if (elgg_is_sticky_form('au_set')) {
		$sticky_values = elgg_get_sticky_values('au_set');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	
	elgg_clear_sticky_form('au_set');

	return $values;
}


/**
 * Pins an entity to a given set
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 */
function au_sets_pin_entity($entity, $set, $user = NULL) {
  
  if (!au_sets_pin_sanity_check($entity, $set, $user)) {
	return add_entity_relationship($entity->getGUID(), AU_SETS_PINNED_RELATIONSHIP, $set->getGUID());
  }
  
  return false;
}

/**
 * Checks to make sure there are no errors with pinning/unpinning entities
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 * @return boolean
 */
function au_sets_pin_sanity_check($entity, $set, $user = NULL) {
  //make sure we have an entity
  if (!elgg_instanceof($entity)) {
	return elgg_echo('au_sets:error:invalid:entity');
  }
  
  if (!elgg_instanceof($set, 'object', 'au_set')) {
	return elgg_echo('au_sets:error:invalid:set');
  }
  
  if ($set->getGUID() == $entity->getGUID()) {
	return elgg_echo('au_sets:error:recursive:pin');
  }
  
  if (!elgg_instanceof($user, 'user')) {
	$user = elgg_get_logged_in_user_entity();
  }
  
  if (!$user) {
	return elgg_echo('au_sets:error:invalid:user');
  }
  
  // make sure we can edit the set
  if (!$set->canEdit($user->guid)) {
	return elgg_echo('au_sets:error:cannot:edit');
  }
  
  return false;
}

/**
 * Pins an entity to a given set
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 */
function au_sets_unpin_entity($entity, $set, $user = NULL) {
  
  if (!au_sets_pin_sanity_check($entity, $set, $user)) {
	return remove_entity_relationship($entity->getGUID(), AU_SETS_PINNED_RELATIONSHIP, $set->getGUID());
  }
  
  return false;
}
