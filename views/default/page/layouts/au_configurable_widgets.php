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

$widget_types = elgg_get_widget_types($available_widgets_context);

elgg_push_context('widgets');

$widgets = elgg_get_widgets($owner->guid, $context);
$preview = false;

if (elgg_can_edit_widget_layout($context)) {
	if ($show_add_widgets) {
		echo elgg_view('page/layouts/widgets/add_button');
	}
	
	$params = array(
		'widgets' => $widgets,
		'context' => $context,
		'exact_match' => $exact_match,
		'show_access' => $show_access
	);
	echo elgg_view('page/layouts/widgets/add_panel', $params);
	
	$preview = get_input('view_layout', false);
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
	if (elgg_can_edit_widget_layout($context)) {
	  $class .= ' au-sets-widget-editable';
	}
	
	echo "<div class=\"elgg-widgets au-sets-widgets au-sets-row-{$row} {$class}\" id=\"elgg-widget-col-$column_index\">";
	
	if ($preview) {
	  echo '<div class="au-sets-preview au-sets-widget-width-100" style="float: none;">' . $column_index . '</div>';
	}
	
	if (sizeof($column_widgets) > 0) {
	  foreach ($column_widgets as $widget) {
		if (array_key_exists($widget->handler, $widget_types)) {
		  echo elgg_view_entity($widget, array('show_access' => $show_access));
		}
	  }
	}
	echo '</div>';
  }
  
  // row complete, clear float
  echo '<div style="clear: both;"></div>';
}

elgg_pop_context();

echo elgg_view('graphics/ajax_loader', array('id' => 'elgg-widget-loader'));

// note that core elgg js not set up for vertically stacked widget containers
// will add wierd and unpredictable min-height settings depending on the widgets contained
// use some js to reset the min-height to 0, then recalculate min-height based on rows
// instead of all containers
?>

<script type="text/javascript">

function au_sets_normalize_widget_height() {
<?php
  foreach ($widget_columns as $row => $column) {
?>
	$('.au-sets-row-<?php echo $row; ?>').css('min-height', '0px');
	elgg.ui.widgets.setMinHeight('.au-sets-row-<?php echo $row; ?>');
	
<?php
  }
?>
}

elgg.register_hook_handler('init', 'system', au_sets_normalize_widget_height, 1000);
</script>