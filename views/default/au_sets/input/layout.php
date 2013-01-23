<?php

$set = $vars['entity'];

echo '<div class="au-sets-layout">';
  echo elgg_view('input/dropdown', array(
	  'name' => 'new-row',
	  'class' => 'au-set-num-columns',
	  'options_values' => array(
		  '' => elgg_echo('au_sets:how:many:columns'),
		  1 => 1,
		  2 => 2,
		  3 => 3,
		  4 => 4,
		  5 => 5,
		  6 => 6,
		  7 => 7,
		  8 => 8,
		  9 => 9,
		  10 => 10
	  )
  ));
  
  echo elgg_view('output/url', array(
	  'text' => elgg_echo('au_sets:add:new:row'),
	  'href' => "#",
	  'class' => 'au-set-add-new-row'
  ));
  
  
  // dynamically populated
  echo '<div class="au-set-layout-config">';
  
  echo '</div>';
echo '</div>';