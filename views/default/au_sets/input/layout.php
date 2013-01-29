<?php

$set = $vars['entity'];

echo '<div class="au-sets-layout clearfix">';
  echo elgg_view('input/dropdown', array(
	  'name' => 'layout',
	  'class' => 'au-set-layout-select',
	  'value' => $set->layout ? $set->layout : '[[100]]',
	  'options_values' => array(
		  '[[100]]' => elgg_echo('au_sets:layout:type', array(1)),
		  '[[50,50]]' => elgg_echo('au_sets:layout:type', array(2)),
		  '[[100],[50,50],[100]]' => elgg_echo('au_sets:layout:type', array(3)),
		  '[[100],[60,40],[100]]' => elgg_echo('au_sets:layout:type', array(4)),
		  '[[100],[40,60],[100]]' => elgg_echo('au_sets:layout:type', array(5)),
		  '[[33,34,33]]' => elgg_echo('au_sets:layout:type', array(6)),
		  '[[100],[33,34,33],[100]]' => elgg_echo('au_sets:layout:type', array(7)),
		  '[[100],[25,25,25,25],[100]]' => elgg_echo('au_sets:layout:type', array(8)),
		  '[[50,50],[33,34,33],[50,50]]' => elgg_echo('au_sets:layout:type', array(9)),
		  '[[100],[75,25],[100]]' => elgg_echo('au_sets:layout:type', array(10)),
		  '[[100],[25,75],[100]]' => elgg_echo('au_sets:layout:type', array(11)),
		  '[[100],[50,25,25],[100]]' => elgg_echo('au_sets:layout:type', array(12)),
	  )
  ));  
  
  // dynamically populated
  echo '<div id="au-set-layout-preview">';
	echo elgg_view('au_sets/layout_preview', array(
		'layout' => $set->layout ? $set->layout : '[[100]]'
	));
  echo '</div>';
echo '</div>';