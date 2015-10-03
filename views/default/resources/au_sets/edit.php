<?php

namespace AU\Sets;

$filter = '';

$vars = array();
$vars['id'] = 'set-post-edit';
$vars['class'] = 'elgg-form-alt';

$sidebar = '';
if ($vars['action'] == 'edit') {
	$pinboard = get_entity((int) $vars['guid']);

	$title = elgg_echo('au_sets:edit');

	if (elgg_instanceof($pinboard, 'object', 'au_set') && $pinboard->canEdit()) {

		$filter = elgg_view('au_sets/navigation/edit', array('entity' => $pinboard));
		$vars['entity'] = $pinboard;

		$title .= ": \"{$pinboard->title}\"";

		$body_vars = au_sets_prepare_form_vars($pinboard);
		$form_vars = array('enctype' => 'multipart/form-data');

		elgg_push_breadcrumb($pinboard->title, $pinboard->getURL());
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


$body = elgg_view_layout('one_column', array(
	'title' => $title,
	'content' => $content,
	'filter' => $filter,
	'class' => 'au-set'
));

echo elgg_view_page($title, $body);