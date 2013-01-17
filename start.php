<?php

require_once 'lib/hooks.php';

define(AU_SETS_PINNED_RELATIONSHIP, 'au_sets_pinned_to');

/**
 * our init process
 */
function au_sets_init() {
    
  elgg_extend_view('css/elgg', 'au_sets/css');
  elgg_extend_view('js/elgg', 'au_sets/js');
  
  elgg_register_library('au_sets', elgg_get_plugins_path() . 'au_sets/lib/au_sets.php');
  
  //register our actions
  elgg_register_action("au_sets/save", dirname(__FILE__) . "/actions/save.php");
  elgg_register_action("au_set/delete", dirname(__FILE__) . "/actions/delete.php");
  elgg_register_action("au_sets/pin", dirname(__FILE__) . "/actions/pin.php");
  
  // register page handler
  elgg_register_page_handler('sets','au_sets_page_handler');
  
  elgg_register_plugin_hook_handler('permissions_check', 'object', 'au_sets_permissions_check');
  elgg_register_plugin_hook_handler('register', 'menu:entity', 'au_sets_entity_menu');
  
  // notifications
  register_notification_object('object', 'au_set', elgg_echo('au_sets:newset'));
  elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'au_sets_notify_message');
	
  // determine urls
  elgg_register_entity_url_handler('object', 'au_set', 'au_sets_url_handler');
  elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'au_sets_icon_url_override');
  
  // add a site navigation item
  $item = new ElggMenuItem('sets', elgg_echo('au_sets:sets'), 'sets/all');
  elgg_register_menu_item('site', $item);
  
  // Add group option
  add_group_tool_option('sets', elgg_echo('au_sets:enablesets'), true);
  elgg_extend_view('groups/tool_latest', 'au_sets/group_module');
  
  elgg_register_ajax_view('au_sets/search');
  elgg_register_ajax_view('au_sets/search_results');
}


/**
 * Dispatches sets pages.
 * URLs take the form of
 *  All sets:        sets/all
 *  User's sets:     sets/owner/<username>
 *  Friends' sets:   sets/friends/<username>
 *  Set:             sets/view/<guid>/<title>
 *  New set:         sets/add/<guid>
 *  Edit set:        sets/edit/<guid>/
 *  Group set:       sets/group/<guid>/all
 *
 * Title is ignored
 *
 *
 * @param array $page
 * @return bool
 */
function au_sets_page_handler($page) {

	elgg_load_library('au_sets');

	// push all sets breadcrumb
	elgg_push_breadcrumb(elgg_echo('au_sets:sets'), "sets/all");

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	switch ($page[0]) {
		case 'owner':
			$user = get_user_by_username($page[1]);
			$params = au_sets_get_page_content_list($user->guid);
			break;
		case 'friends':
			$user = get_user_by_username($page[1]);
			$params = au_sets_get_page_content_friends($user->guid);
			break;
		case 'view':
			$params = au_sets_get_page_content_read($page[1]);
			break;
		case 'add':
			gatekeeper();
			$params = au_sets_get_page_content_edit($page[0], $page[1]);
			break;
		case 'edit':
			gatekeeper();
			$params = au_sets_get_page_content_edit($page[0], $page[1]);
			break;
		case 'group':
			$params = au_sets_get_page_content_list($page[1]);
			break;
		case 'all':
			$params = au_sets_get_page_content_list();
			break;
		case 'icon':
			$set = get_entity($page[1]);
			au_sets_get_icon($set, $page[2]);
			return true;
		default:
			return false;
	}

	if (isset($params['sidebar'])) {
		$params['sidebar'] .= elgg_view('au_sets/sidebar', array('page' => $page_type));
	} else {
		$params['sidebar'] = elgg_view('au_sets/sidebar', array('page' => $page_type));
	}

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
}


/**
 * Set a url for a specific set
 * @param type $entity
 * @return boolean
 */
function au_sets_url_handler($entity) {
  if (!$entity->getOwnerEntity()) {
	// default to a standard view if no owner.
	return FALSE;
  }

  $friendly_title = elgg_get_friendly_title($entity->title);
  
  return "sets/view/{$entity->guid}/$friendly_title";
}


/**
 *  returns an array of accesses the user can write to sets
 *	this is in start because we use it for a lot of hooks
 * 
 * @param type $user
 * @return type
 */
function au_sets_get_write_accesses($user) {
  if (!elgg_instanceof($user, 'user')) {
	return array(ACCESS_PUBLIC);
  }
  
  // write access is set using acl nomenclature
  $access = get_access_array($user->getGUID());
  
  // remove private and friends ids
  foreach (array(ACCESS_PRIVATE, ACCESS_FRIENDS) as $id) {
	if (($key = array_search($id, $access)) !== false) {
	  unset($access[$key]);
	}
  }
  
  return $access;
}


elgg_register_event_handler('init', 'system', 'au_sets_init');