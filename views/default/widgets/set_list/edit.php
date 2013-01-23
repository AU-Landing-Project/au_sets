<?php

echo elgg_echo('au_sets:num:results') . ' ';

$options_values = array();

$i = 1;
while ($i < 51) {
  $options_values[$i] = $i;
  $i++;
}

echo elgg_view('input/dropdown', array(
	'name' => 'params[num_results]',
	'value' => $vars['entity']->num_results ? $vars['entity']->num_results : 10,
	'options_values' => $options_values
));