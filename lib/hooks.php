<?php

function au_sets_entity_menu($hook, $type, $return, $params) {
  if (is_array($return) && elgg_instanceof($params['entity'], 'object', 'au_set')) {
	foreach ($return as $key => $item) {
	  if ($item->getName() == 'edit') {
		$return[$key]->setHref('pinboards/edit/' . $params['entity']->getGUID());
	  }
	  
	  if  ($item->getName() == 'delete' && elgg_is_logged_in()) {
		if (!($params['entity']->owner_guid == elgg_get_logged_in_user_guid()) && !elgg_is_admin_logged_in()) {
		  unset($return[$key]);
		}
	  }
	}
  }
  
  if (elgg_is_logged_in() && !elgg_in_context('widgets')) {
	$use_icon = elgg_get_plugin_setting('pin_icon', 'au_sets');
	
	if ($use_icon != 'no') {
	  $text = '<span class="elgg-icon elgg-icon-push-pin-alt" data-guid="' . $params['entity']->getGUID() . '">';
	  $text .= '</span>';
	}
	else {
	  $text = '<span data-guid="' . $params['entity']->getGUID() . '">';
	  $text .= elgg_echo('au_sets:pin');
	  $text .= '</span>';
	}
	
	$pin = new ElggMenuItem('au_sets_pin', $text, '#');
	$pin->setLinkClass('au-sets-pin');

	$return[] = $pin;
  }
  
  // add unpin link if we're displaying the entity on a set profile
  if (elgg_get_context() == 'au_sets_list') {
	
	$text = '<span data-guid="' . $params['entity']->getGUID() . '">';
	$text .= elgg_echo('au_sets:unpin');
	$text .= '</span>';
	$unpin = new ElggMenuItem('au_sets:unpin', $text, '#');
	$unpin->setLinkClass('au-sets-unpin');
	
	$return[] = $unpin;
  }
  
  // add link for viewing the layout if we're in read mode and can edit the set
  if (stristr($params['class'], 'au-set-title-menu') && $params['entity']->canEdit()) {
	$layout_active = get_input('view_layout', false);
	
	if (!$layout_active) {
	  $url = elgg_http_add_url_query_elements(current_page_url(), array('view_layout' => 1));
	  $view_layout = new ElggMenuItem('au_sets:view_layout', elgg_echo('au_sets:view:layout'), $url);
	}
	else {
	  $url = elgg_http_remove_url_query_element(current_page_url(), 'view_layout');
	  $view_layout = new ElggMenuItem('au_sets:view_layout', elgg_echo('au_sets:hide:layout'), $url);
	}
	
	$return[] = $view_layout;
  }
  
  return $return;
}


/*
 * replaces the bookmarks icon
 */
function au_sets_extras_menu($hook, $type, $return, $params) {
  foreach ($return as $key => $item) {
	if ($item->getName() == 'bookmark') {
	  $return[$key]->setText('<span class="elgg-icon au-sets-bookmark-icon"></span>');
	}
  }
  
  return $return;
}


function au_sets_icon_url_override($hook, $type, $return, $params) {
  if (!elgg_instanceof($params['entity'], 'object', 'au_set')) {
	return $return;
  }
  
  // get our icon url
  $icontime = $params['entity']->icontime;
  if (!$icontime) {
	$icontime = 'default';
  }
  return elgg_get_site_url() . 'pinboards/icon/' . $params['entity']->getGUID() . '/' . $params['size'] . '/' . $icontime . '.jpg';
}



/**
 * Set the notification message body
 * 
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the blog posted
 * @return string
 */
function au_sets_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'au_set')) {
		$descr = $entity->excerpt;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('au_sets:notification', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}


/**
 * Add a menu item to an ownerblock
 */
function au_sets_owner_block_menu($hook, $type, $return, $params) {
  if (elgg_instanceof($params['entity'], 'user')) {
	$url = "pinboards/owner/{$params['entity']->username}";
	$item = new ElggMenuItem('set', elgg_echo('au_sets:sets'), $url);
	$return[] = $item;
  } else {
	if ($params['entity']->sets_enable != "no") {
	  $url = "pinboards/group/{$params['entity']->guid}/all";
	  $item = new ElggMenuItem('set', elgg_echo('au_sets:group'), $url);
	  $return[] = $item;
	}
  }

  return $return;
}

/**
 * Determines if the user canEdit() an object
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 */
function au_sets_permissions_check($hook, $type, $return, $params) {
  if (!elgg_instanceof($params['entity'], 'object', 'au_set')) {
	return $return;
  }
  
  if (!elgg_is_logged_in()) {
	return $return;
  }
  
  // this is our object, lets determine if we can edit it
  $set = $params['entity'];
  $user = $params['user'];
  $owner = $params['entity']->getOwnerEntity();
  
  // owners and admins can always edit
  if ($user->getGUID() == $owner->getGUID() || $user->isAdmin()) {
	return true;
  }

  
  // check for friends special case
  if ($set->write_access_id == ACCESS_FRIENDS) {
	return $owner->isFriendsWith($user->getGUID());
  }
  
  // write access is set using acl nomenclature
  $access = au_sets_get_write_accesses($user);
  
  // now we just look at remaining acls
  if (in_array($set->write_access_id, $access)) {
	return true;
  }
  
  return $return;
}


function au_sets_widget_layout_perms($hook, $type, $return, $params) {
  if (elgg_instanceof($params['page_owner'], 'object', 'au_set')) {	
	$preview = get_input('view_layout', false);
	if ($params['page_owner']->canEdit($params['user']->guid) && $preview) {
	  return true;
	}
  }
  
  return $return;
}


/**
 *	Determine if we can edit a widget
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 */
function au_sets_widget_permissions_check($hook, $type, $return, $params) {
  if (!elgg_instanceof($params['entity'], 'object', 'widget')) {
	return $return;
  }
  
  if ($params['entity']->getContext() != 'pinboards') {
	return $return;
  }
  
  if (elgg_get_config('au_sets_widget_noedit')) {
	return false;
  }
  
  return $return;
}