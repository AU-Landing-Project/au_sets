<?php

// available layouts
$layouts = array(
		  '[[100]]',
		  '[[50,50]]',
		  '[[33,34,33]]',
		  '[[25,25,25,25]]',
		  '[[100],[50,50],[100]]',
		  '[[100],[60,40],[100]]',
		  '[[100],[40,60],[100]]',
		  '[[100],[33,34,33],[100]]',
		  '[[100],[25,25,25,25],[100]]',
		  '[[100],[75,25],[100]]',
		  '[[100],[25,75],[100]]',
		  '[[100],[50,25,25],[100]]',
		  '[[50,50],[33,34,33],[50,50]]',
		  '[[50,50],[33,34,33],[100]]',
		  '[[100],[33,34,33],[50,50]]',
		  '[[50,50],[33,34,33],[25,25,25,25]]',
	  );

$set = $vars['entity'];


$selected = $set->layout ? $set->layout : '[[100]]';

echo '<div class="clearfix">';

foreach ($layouts as $layout) {
  echo elgg_view('au_sets/layout_preview', array(
	  'layout' => $layout,
	  'selected' => ($layout == $selected)
  ));
}

echo elgg_view('input/hidden', array(
	'name' => 'layout',
	'value' => $selected,
	'id' => 'au-sets-layout-input'
));

echo '</div>';