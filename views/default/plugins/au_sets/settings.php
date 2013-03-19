<?php

echo elgg_view('input/dropdown', array(
	'name' => 'params[pin_icon]',
	'value' => $vars['entity']->pin_icon ? $vars['entity']->pin_icon : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '&nbsp;';
echo elgg_echo('au_sets:use:pin:icon');

echo '<br><br>';

echo elgg_view('input/dropdown', array(
	'name' => 'params[change_bookmark_icon]',
	'value' => $vars['entity']->change_bookmark_icon ? $vars['entity']->change_bookmark_icon : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '&nbsp;';
echo elgg_echo('au_sets:change:bookmark:icon');

echo '<br><br>';


echo elgg_view('input/dropdown', array(
	'name' => 'params[use_au_widgets]',
	'value' => $vars['entity']->use_au_widgets ? $vars['entity']->use_au_widgets : 'no',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));
echo '&nbsp;';
echo elgg_echo('au_sets:settings:use:au_widgets');
echo '<br><br>';
