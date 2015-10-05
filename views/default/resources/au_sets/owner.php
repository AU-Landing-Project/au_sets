<?php

namespace AU\Sets;

$container_guid = $vars['container_guid'];

$filter_context = $container_guid ? 'mine' : 'all';

$options = array(
	'type' => 'object',
	'subtype' => 'au_set',
	'full_view' => false,
	'no_results' => elgg_echo('au_sets:none')
);

$current_user = elgg_get_logged_in_user_entity();

if ($container_guid) {
	// access check for closed groups
	elgg_group_gatekeeper();

	$options['container_guid'] = $container_guid;
	$container = get_entity($container_guid);
	if (!$container) {
		// @TODO
		// what do we do if we don't have a container?
		forward('', '404');
	}
	$title = elgg_echo('au_sets:title:user_sets', array($container->name));

	elgg_push_breadcrumb($container->name);

	if ($current_user && ($container_guid == $current_user->guid)) {
		$filter_context = 'mine';
	} else if (elgg_instanceof($container, 'group')) {
		$filter = false;
	} else {
		// do not show button or select a tab when viewing someone else's posts
		$filter_context = 'none';
	}

	$sidebar = elgg_view('page/elements/comments_block', array(
		'subtypes' => 'au_set',
		'owner_guid' => elgg_get_page_owner_guid(),
	));
} else {
	$filter_context = 'all';
	$title = elgg_echo('au_sets:title:all_sets');
	elgg_pop_breadcrumb();
	elgg_push_breadcrumb(elgg_echo('au_sets:sets'));

	$sidebar = elgg_view('page/elements/comments_block', array(
		'subtypes' => 'au_set',
	));
}

if (elgg_is_logged_in()) {
	elgg_register_title_button();
}

$content = elgg_list_entities_from_metadata($options);



$sidebar .= elgg_view('page/elements/tagcloud_block', array(
	'subtypes' => 'au_set',
	'owner_guid' => elgg_get_page_owner_guid(),
		));

$body = elgg_view_layout('content', array(
	'filter_context' => $filter_context,
	'filter' => $filter,
	'content' => $content,
	'sidebar' => $sidebar,
	'title' => $title
		));

echo elgg_view_page($title, $body);
