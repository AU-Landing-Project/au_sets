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

$classes = ['au-set'];
if (elgg_is_active_plugin('widget_manager')) {
	$classes[] = 'au-set-wm';
}

$body = elgg_view_layout('one_column', array(
	'title' => $title,
	'content' => $menu . '<div class="au-set-widgets-wrapper">' . $content . '</div>',
	'class' => implode(' ', $classes)
));

echo elgg_view_page($title, $body);
