<?php

require_once 'lib/hooks.php';

define(AU_SETS_PINNED_RELATIONSHIP, 'au_sets_pinned_to');

/**
 * our init process
 */
function au_sets_init() {
    
  elgg_extend_view('css/elgg', 'au_sets/css');
  elgg_extend_view('js/elgg', 'au_sets/js');
  elgg_extend_view('page/layouts/one_column', 'au_sets/navigation/title_menu', 0);
  
  elgg_register_library('au_sets', elgg_get_plugins_path() . 'au_sets/lib/au_sets.php');
  
  //register our actions
  elgg_register_action("au_sets/save", dirname(__FILE__) . "/actions/save.php");
  elgg_register_action("au_set/delete", dirname(__FILE__) . "/actions/delete.php");
  elgg_register_action("au_sets/pin", dirname(__FILE__) . "/actions/pin.php");
  elgg_register_action("au_sets/unpin", dirname(__FILE__) . "/actions/unpin.php");
  
  elgg_register_event_handler('pagesetup', 'system', 'au_sets_pagesetup');
  
  // register page handler
  elgg_register_page_handler('pinboards','au_sets_page_handler');
  
  // make it show up in search
  elgg_register_entity_type('object', 'au_set');
  
  elgg_register_plugin_hook_handler('permissions_check', 'object', 'au_sets_permissions_check');
  elgg_register_plugin_hook_handler('permissions_check', 'object', 'au_sets_widget_permissions_check');
  elgg_register_plugin_hook_handler('permissions_check', 'widget_layout', 'au_sets_widget_layout_perms');
  elgg_register_plugin_hook_handler('register', 'menu:entity', 'au_sets_entity_menu');
  elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'au_sets_owner_block_menu');
  
  $replace_bookmarks_icon = elgg_get_plugin_setting('change_bookmark_icon', 'au_sets');
  if ($replace_bookmarks_icon != 'no') {
	elgg_register_plugin_hook_handler('register', 'menu:extras', 'au_sets_extras_menu', 1000);
  }
  
  // notifications
  register_notification_object('object', 'au_set', elgg_echo('au_sets:newset'));
  elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'au_sets_notify_message');
	
  // determine urls
  elgg_register_entity_url_handler('object', 'au_set', 'au_sets_url_handler');
  elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'au_sets_icon_url_override');
  
  // add a site navigation item
  $item = new ElggMenuItem('sets', elgg_echo('au_sets:sets'), 'pinboards/all');
  elgg_register_menu_item('site', $item);
  
  // Add group option
  add_group_tool_option('sets', elgg_echo('au_sets:enablesets'), true);
  elgg_extend_view('groups/tool_latest', 'au_sets/group_module');
  
  elgg_register_ajax_view('au_sets/search');
  elgg_register_ajax_view('au_sets/search_results');
  elgg_register_ajax_view('au_sets/item_search');
  
  elgg_register_widget_type('set_avatar', elgg_echo("au_sets:widget:set_avatar:title"), elgg_echo("au_sets:widget:set_avatar:description"), 'pinboards', TRUE);
  elgg_register_widget_type('set_description', elgg_echo("au_sets:widget:set_description:title"), elgg_echo("au_sets:widget:set_description:description"), 'pinboards', TRUE);
  elgg_register_widget_type('set_list', elgg_echo("au_sets:widget:set_list:title"), elgg_echo("au_sets:widget:set_list:description"), 'pinboards', TRUE);
  elgg_register_widget_type('set_item', elgg_echo("au_sets:widget:set_item:title"), elgg_echo("au_sets:widget:set_item:description"), 'pinboards', TRUE);
  elgg_register_widget_type('set_comments', elgg_echo("au_sets:widget:set_comments:title"), elgg_echo("au_sets:widget:set_comments:description"), 'pinboards', TRUE);
  elgg_register_widget_type('sets', elgg_echo("au_sets:widget:sets:title"), elgg_echo("au_sets:widget:sets:description"), 'profile,groups,dashboard', TRUE);
  
  au_sets_add_widget_context('free_html', 'pinboards');
  au_sets_add_widget_context('tabtext', 'pinboards');
  au_sets_add_widget_context('rss', 'pinboards');
  au_sets_add_widget_context('xgadget', 'pinboards');
  au_sets_add_widget_context('au_tagtracker', 'pinboards');
  
  // get all widget handlers and extend the edit form
  $types = elgg_get_widget_types('pinboards', true);
  if (is_array($types)) {
	foreach ($types as $handle => $info) {
	  elgg_extend_view("widgets/{$handle}/edit", 'au_sets/widgets/style_visibility');
	}
  }
  
  if (elgg_is_admin_logged_in()) {
	run_function_once('au_sets_w_context_20130205');
  }
}


/**
 * Dispatches sets pages.
 * URLs take the form of
 *  All sets:        pinboards/all
 *  User's sets:     pinboards/owner/<username>
 *  Friends' sets:   pinboards/friends/<username>
 *  Set:             pinboards/view/<guid>/<title>
 *  New set:         pinboards/add/<guid>
 *  Edit set:        pinboards/edit/<guid>/
 *  Group set:       pinboards/group/<guid>/all
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
	elgg_push_breadcrumb(elgg_echo('au_sets:sets'), "pinboards/all");

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
			return au_sets_get_page_content_read($page[1]);
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
		case 'list':
			$params = au_sets_get_set_list($page[1]);
			break;
		default:
			return false;
	}

	if (isset($params['sidebar'])) {
		$params['sidebar'] .= elgg_view('au_sets/sidebar', array('page' => $page[0]));
	} else {
		$params['sidebar'] = elgg_view('au_sets/sidebar', array('page' => $page[0]));
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
  
  return "pinboards/view/{$entity->guid}/$friendly_title";
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


function au_sets_pagesetup() {
  if (elgg_get_context() == 'sets') {
	$set = elgg_get_page_owner_entity();
	
	if (elgg_instanceof($set, 'object', 'au_set') && $set->canEdit()) {
	  elgg_register_title_button('sets', 'edit');
	}
  }
}


function au_sets_add_widget_context($handle, $context) {
  
  if (!elgg_is_widget_type($handle)) {
	return false;
  }
  
  $widgets = elgg_get_config('widgets');
  
  if (!in_array($context, $widgets->handlers[$handle]->context)) {
	array_push($widgets->handlers[$handle]->context, $context);
  }
  
  elgg_set_config('widgets', $widgets);
  
  return true;
}

elgg_register_event_handler('init', 'system', 'au_sets_init');



/**
 * Upgrades
 */

function au_sets_w_context_20130205() {
  $options = array(
	'type'	=> 'object',
	  'subtype' => 'widget',
	  'private_setting_name' => 'context',
	  'private_setting_value' => 'sets',
	  'limit' => 0
  );
  
  $batch = new ElggBatch('elgg_get_entities_from_private_settings', $options, 'au_sets_change_context_20130205', 25);
}

function au_sets_change_context_20130205($result, $getter, $options) {
  $result->context = 'pinboards';
  $result->save();
}
