<?php

$set = $vars['entity'];

$edit_url = elgg_get_site_url() . 'pinboards/edit/' . $set->getGUID();
$list_url = elgg_get_site_url() . 'pinboards/list/' . $set->getGUID();

$tabs = array(
	array(
		'text' => elgg_echo('edit'),
		'href' => $edit_url,
		'selected' => ($edit_url == current_page_url())
	),
	array(
		'text' => elgg_echo('List'),
		'href' => $list_url,
		'selected' => ($list_url == current_page_url())
	)
);

echo elgg_view('navigation/tabs', array('tabs' => $tabs));
