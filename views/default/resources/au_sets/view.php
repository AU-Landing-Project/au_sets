<?php

namespace AU\Sets;

$pinboard = $vars['pinboard'];

$filter = '';

$title = $pinboard->title;

$container = $pinboard->getContainerEntity();
if (!$container) {
	forward('', '404');
}
if (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb($container->name, "pinboards/group/$container->guid/all");
} else {
	elgg_push_breadcrumb($container->name, "pinboards/owner/$container->username");
}

elgg_push_breadcrumb($pinboard->title);

$content = elgg_view_layout('au_configurable_widgets', array(
	'widget_layout' => json_decode($pinboard->layout),
	'exact_match' => true
		));

$menu = elgg_view_menu('entity', array(
	'entity' => $pinboard,
	'handler' => 'au_set',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz au-set-title-menu',
		));


elgg_set_context('pinboards');

$body = elgg_view_layout('one_column', array(
	'title' => $title,
	'content' => $menu . '<div class="au-set-widgets-wrapper">' . $content . '</div>',
	'class' => 'au-set'
));

echo elgg_view_page($title, $body);
