<?php

namespace AU\Sets;

$pinboard = get_entity($vars['guid']);
if (!elgg_instanceof($pinboard, 'object', 'au_set') || !$pinboard->canEdit()) {
	register_error(elgg_echo('au_sets:error:invalid:set'));
	forward(REFERER);
}

elgg_push_breadcrumb($pinboard->title, $pinboard->getURL());
elgg_push_breadcrumb(elgg_echo('List'));

elgg_push_context('au_sets_list');
$list = elgg_list_entities_from_relationship(array(
	'relationship_guid' => $pinboard->guid,
	'relationship' => AU_SETS_PINNED_RELATIONSHIP,
	'inverse_relationship' => true,
	'full_view' => false,
	'limit' => 10,
	'order_by' => 'r.time_created DESC',
	'no_results' => elgg_echo('au_set:none')
		));

$list .= '<div class="au-sets-guid-markup" data-set="' . $pinboard->guid . '"></div>';
elgg_pop_context();


$body = elgg_view_layout('content', array(
	'filter' => elgg_view('au_sets/navigation/edit', array('entity' => $pinboard)),
	'content' => $list,
	'title' => $pinboard->title
));

echo elgg_view_page($pinboard->title, $body);