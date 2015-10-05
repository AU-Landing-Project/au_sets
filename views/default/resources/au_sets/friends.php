<?php

namespace AU\Sets;

$user = $vars['user'];
if (!$user) {
	forward('pinboards/all');
}

$filter_context = 'friends';
$title = elgg_echo('au_sets:title:friends');

elgg_push_breadcrumb($user->name, "pinboards/owner/{$user->username}");
elgg_push_breadcrumb(elgg_echo('friends'));

if (elgg_is_logged_in()) {
	elgg_register_title_button();
}

$dbprefix = elgg_get_config('dbprefix');
$options = array(
	'type' => 'object',
	'subtype' => 'au_set',
	'joins' => array(
		"JOIN {$dbprefix}entity_relationships r ON r.guid_two = e.container_guid AND r.relationship = 'friend'"
	),
	'wheres' => array(
		"r.guid_one = {$user->guid}"
	),
	'full_view' => false,
	'no_results' => elgg_echo('au_sets:none')
);

$content = elgg_list_entities($options);

$body = elgg_view_layout('content', array(
	'filter_context' => $filter_context,
	'filter' => $filter,
	'content' => $content,
	'title' => $title
		));

echo elgg_view_page($title, $body);
