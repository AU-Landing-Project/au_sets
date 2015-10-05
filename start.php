<?php

namespace AU\Sets;

require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/functions.php';

const PLUGIN_ID = 'au_sets';
const PLUGIN_VERSION = 20151003;
const AU_SETS_PINNED_RELATIONSHIP = 'au_sets_pinned_to';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

/**
 * our init process
 */
function init() {

	elgg_extend_view('css/elgg', 'css/au_sets');
	elgg_require_js('au_sets');

	//register our actions
	elgg_register_action("au_sets/save", __DIR__ . "/actions/save.php");
	elgg_register_action("au_set/delete", __DIR__ . "/actions/delete.php");
	elgg_register_action("au_sets/pin", __DIR__ . "/actions/pin.php");
	elgg_register_action("au_sets/unpin", __DIR__ . "/actions/unpin.php");

	elgg_register_event_handler('pagesetup', 'system', __NAMESPACE__ . '\\pagesetup');

	// register page handler
	elgg_register_page_handler('pinboards', __NAMESPACE__ . '\\pinboards_page_handler');

	// make it show up in search
	elgg_register_entity_type('object', 'au_set');

	elgg_register_plugin_hook_handler('permissions_check', 'object', __NAMESPACE__ . '\\permissions_check');
	elgg_register_plugin_hook_handler('permissions_check', 'object', __NAMESPACE__ . '\\widget_permissions_check');
	elgg_register_plugin_hook_handler('permissions_check', 'widget_layout', __NAMESPACE__ . '\\widget_layout_perms');
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\entity_menu_hook');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', __NAMESPACE__ . '\\owner_block_menu_hook');
	elgg_register_plugin_hook_handler('entity:url', 'object', __NAMESPACE__ . '\\pinboards_url');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', __NAMESPACE__ . '\\pinboard_icon_url_override');

	$replace_bookmarks_icon = elgg_get_plugin_setting('change_bookmark_icon', PLUGIN_ID);
	if ($replace_bookmarks_icon != 'no') {
		elgg_register_plugin_hook_handler('register', 'menu:extras', __NAMESPACE__ . '\\extras_menu_hook', 1000);
	}

	// notifications
	elgg_register_notification_event('object', 'au_set', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:au_set', __NAMESPACE__ . '\\pinboard_prepare_notification');

	// add a site navigation item
	$item = new \ElggMenuItem('sets', elgg_echo('au_sets:sets'), 'pinboards/all');
	elgg_register_menu_item('site', $item);

	// Add group option
	add_group_tool_option('sets', elgg_echo('au_sets:enablesets'), true);
	elgg_extend_view('groups/tool_latest', 'au_sets/group_module');

	elgg_register_ajax_view('au_sets/search');
	elgg_register_ajax_view('au_sets/search_results');
	elgg_register_ajax_view('au_sets/item_search');

	elgg_register_widget_type('set_avatar', elgg_echo("au_sets:widget:set_avatar:title"), elgg_echo("au_sets:widget:set_avatar:description"), array('pinboards'), TRUE);
	elgg_register_widget_type('set_description', elgg_echo("au_sets:widget:set_description:title"), elgg_echo("au_sets:widget:set_description:description"), array('pinboards'), TRUE);
	elgg_register_widget_type('set_list', elgg_echo("au_sets:widget:set_list:title"), elgg_echo("au_sets:widget:set_list:description"), array('pinboards'), TRUE);
	elgg_register_widget_type('set_item', elgg_echo("au_sets:widget:set_item:title"), elgg_echo("au_sets:widget:set_item:description"), array('pinboards'), TRUE);
	elgg_register_widget_type('set_comments', elgg_echo("au_sets:widget:set_comments:title"), elgg_echo("au_sets:widget:set_comments:description"), array('pinboards'), TRUE);
	elgg_register_widget_type('sets', elgg_echo("au_sets:widget:sets:title"), elgg_echo("au_sets:widget:sets:description"), array('profile','groups','dashboard'), TRUE);

	add_widget_context('free_html', 'pinboards');
	add_widget_context('tabtext', 'pinboards');
	add_widget_context('rss', 'pinboards');
	add_widget_context('xgadget', 'pinboards');
	add_widget_context('au_tagtracker', 'pinboards');
	add_widget_context('image_slider', 'pinboards');

	// use au widgets if it's set
	$use_au_widgets = elgg_get_plugin_setting('use_au_widgets', PLUGIN_ID);
	if ($use_au_widgets == 'yes' && elgg_is_active_plugin('au_widgets')) {
		add_widget_context('blog', 'pinboards');

		add_widget_context('bookmarks', 'pinboards');
		
		add_widget_context('filerepo', 'pinboards');

		add_widget_context('pages', 'pinboards');

		add_widget_context('liked_content', 'pinboards');

		if (elgg_is_active_plugin('group_tools')) {
			add_widget_context('featured_groups', 'pinboards');
			add_widget_context('index_discussion', 'pinboards');
		}

		add_widget_context('index_activity', 'pinboards');
		add_widget_context('content_by_tag', 'pinboards');
		add_widget_context('index_groups', 'pinboards');
		add_widget_context('au_random_content', 'pinboards');
		add_widget_context('tagcloud', 'pinboards');
	}

	// get all widget handlers and extend the edit form
	$types = elgg_get_widget_types('pinboards', true);
	if (is_array($types)) {
		foreach ($types as $handle => $info) {
			elgg_extend_view("widgets/{$handle}/edit", 'au_sets/widgets/style_visibility');
		}
	}

	elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrades');
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
function pinboards_page_handler($page) {

	// push all sets breadcrumb
	elgg_push_breadcrumb(elgg_echo('au_sets:sets'), "pinboards/all");

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	switch ($page[0]) {
		case 'owner':
			$user = get_user_by_username($page[1]);
			if (!$user) {
				register_error(elgg_echo('noaccess'));
				$_SESSION['last_forward_from'] = current_page_url();
				return false;
			}
			elgg_set_page_owner_guid($user->guid);
			echo elgg_view('resources/au_sets/owner', array(
				'container_guid' => $user->guid
			));
			return true;
			break;
		case 'friends':
			$user = get_user_by_username($page[1]);
			if (!$user) {
				register_error(elgg_echo('noaccess'));
				$_SESSION['last_forward_from'] = current_page_url();
				return false;
			}
			elgg_set_page_owner_guid($user->guid);
			echo elgg_view('resources/au_sets/friends', array(
				'user' => $user
			));
			return true;
			break;
		case 'view':
			$pinboard = get_entity($page[1]);
			if (!elgg_instanceof($pinboard, 'object', 'au_set')) {
				register_error(elgg_echo('noaccess'));
				$_SESSION['last_forward_from'] = current_page_url();
				return false;
			}
			elgg_set_page_owner_guid($pinboard->guid);
			echo elgg_view('resources/au_sets/view', array(
				'pinboard' => $pinboard
			));
			return true;
			break;
		case 'add':
			gatekeeper();
			elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
			echo elgg_view('resources/au_sets/edit', array(
				'guid' => $page[1],
				'action_type' => 'add'
			));
			return true;
			break;
		case 'edit':
			gatekeeper();
			$pinboard = get_entity($page[1]);
			if (!elgg_instanceof($pinboard, 'object', 'au_set')) {
				return false;
			}

			elgg_set_page_owner_guid($pinboard->container_guid);
			echo elgg_view('resources/au_sets/edit', array(
				'guid' => $page[1],
				'action_type' => 'edit'
			));
			break;
		case 'group':
			$group = get_entity($page[1]);
			if (!($group instanceof \ElggGroup)) {
				register_error(elgg_echo('noaccess'));
				$_SESSION['last_forward_from'] = current_page_url();
				return false;
			}
			elgg_set_page_owner_guid($group->guid);
			echo elgg_view('resources/au_sets/owner', array(
				'container_guid' => $group->guid
			));
			return true;
			break;
		case 'all':
			echo elgg_view('resources/au_sets/owner');
			return true;
			break;
		case 'icon':
			$pinboard = get_entity($page[1]);
			echo elgg_view('resources/au_sets/icon', array(
				'pinboard' => $pinboard,
				'size' => $page[2]
			));
			return true;
		case 'list':
			elgg_set_page_owner_guid($page[1]);
			echo elgg_view('resources/au_sets/list', array(
				'guid' => $page[1]
			));
			return true;
			break;
		default:
			return false;
	}
}
