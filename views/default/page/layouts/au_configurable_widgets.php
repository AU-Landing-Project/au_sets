<?php

/**
 * Elgg widgets layout
 *
 * @uses $vars['content']          Optional display box at the top of layout
 * @uses $vars['widget_layout']    Multidimensional array depicting the widget layout
 * @uses $vars['show_add_widgets'] Display the add widgets button and panel (true)
 * @uses $vars['exact_match']      Widgets must match the current context (false)
 * @uses $vars['show_access']      Show the access control (true)
 *
 *
 * widget_layout example
 *	array(array('33', '34', '33')) = 1 row with columns 33%, 34%, 33%
 *
 *  array(array('100'), array('50', '50'), array('100')) = 3 rows, 100%, 50/50, 100%
 */

$widget_columns = elgg_extract('widget_layout', $vars, array(array('100')));
$show_add_widgets = elgg_extract('show_add_widgets', $vars, true);
$exact_match = elgg_extract('exact_match', $vars, false);
$show_access = elgg_extract('show_access', $vars, true);

$owner = elgg_get_page_owner_entity();

$context = elgg_get_context();
$available_widgets_context = elgg_trigger_plugin_hook("available_widgets_context", "widget_manager", array(), $context);

// ADDED TRUE to this call, otherwise we get all widgets returned
$widget_types = elgg_get_widget_types($available_widgets_context,true);

elgg_push_context('widgets');
$widgets = elgg_get_widgets($owner->guid, $context);

// are we in view or layout mode?
$min_height = '0px';
$preview = false;
if ($owner->canEdit()) {
  $preview = get_input('view_layout', false);

	if ($preview) {
	  $helptext = elgg_echo('au_sets:mode:layout:help', array(
		  elgg_view('output/url', array(
			  'text' => elgg_echo('au_sets:mode:layout:linktext'),
			  'href' => $owner->getURL()
		  ))
	  ));
	  $min_height = '50px';
	}
	else {
	  $helptext = elgg_echo('au_sets:mode:view:help', array(
		  elgg_view('output/url', array(
			  'text' => elgg_echo('au_sets:mode:view:linktext'),
			  'href' => $owner->getURL() . '?view_layout=1'
		  ))
	  ));
	  elgg_set_config('au_sets_widget_noedit', true);
	}
}

echo "<div class='elgg-layout-widgets layout-widgets-" . $context . "'>";

if (elgg_can_edit_widget_layout($context)) {
	if ($show_add_widgets) {
		// added params to get the correct content of the panel
		echo elgg_view('page/layouts/widgets/add_button', array('context' => $context,
		'widgets' => $widgets,
		'exact_match' => $exact_match,
		'show_access' => $show_access));
	}

	$params = array(
		'widgets' => $widgets,
		'context' => $context,
		'exact_match' => $exact_match,
		'show_access' => $show_access
	);
	echo elgg_view('page/layouts/widgets/add_panel', $params);
}

if ($owner->canEdit()) {
  echo elgg_view('output/longtext', array('value' => $helptext, 'class' => 'elgg-subtext au-sets-pinboard-help'));
}

echo elgg_extract("content", $vars);
$column_index = 0;

foreach ($widget_columns as $row => $columns) {
  foreach ($columns as $width) {
	$column_index++;

	if (isset($widgets[$column_index])) {
	  $column_widgets = $widgets[$column_index];
	}
	else {
	  $column_widgets = array();
	}

	$class = 'au-sets-widget-width-' . $width;
	if (elgg_can_edit_widget_layout($context) && $preview) {
	  $class .= ' au-sets-widget-editable';
	}

	echo "<div class=\"elgg-widgets au-sets-widgets au-sets-row au-sets-row-{$row} {$class}\" id=\"elgg-widget-col-$column_index\">";

	if ($preview) {

	  // column header
	  echo '<div class="au-sets-widget-view au-sets-widget-width-100 elgg-state-fixed" style="float: none;">';
	  echo elgg_echo('au_sets:widget:column', array($column_index));
	  echo '<div class="elgg-subtext">' . elgg_echo('au_sets:drag:widgets') . '</div>';
	  echo '</div>';

	  // background div

	}

	if (sizeof($column_widgets) > 0) {
	  foreach ($column_widgets as $widget) {
		if (array_key_exists($widget->handler, $widget_types)) {
		  // if not in preview mode, wrap widgets that need styling hidden
		  if (!$preview && ($widget->sets_hide_style == 'yes')) {
			echo elgg_view('object/widget/widget_alt', array(
				'entity' => $widget,
				'show_access' => $show_access,
				'class' => 'au-sets-hide-style'
				));
		  }
		  else {
			echo elgg_view_entity($widget, array('show_access' => $show_access));
		  }
		}
	  }
	}
	echo '</div>';
  }

  // row complete, clear float
  echo '<div style="clear: both;"></div>';
}
echo '</div>';

elgg_pop_context();

echo elgg_view('graphics/ajax_loader', array('id' => 'elgg-widget-loader'));
