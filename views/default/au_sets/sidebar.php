<?php

// fetch & display latest comments
if ($vars['page'] == 'all') {
	echo elgg_view('page/elements/comments_block', array(
		'subtypes' => 'au_set',
	));
} elseif ($vars['page'] == 'owner') {
	echo elgg_view('page/elements/comments_block', array(
		'subtypes' => 'au_set',
		'owner_guid' => elgg_get_page_owner_guid(),
	));
}

if ($vars['page'] != 'friends') {
	echo elgg_view('page/elements/tagcloud_block', array(
		'subtypes' => 'au_set',
		'owner_guid' => elgg_get_page_owner_guid(),
	));
}
