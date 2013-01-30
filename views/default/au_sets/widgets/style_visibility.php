<?php

if (!elgg_in_context('sets')) {
  return true;
}

echo '<br>';

echo elgg_echo('au_sets:widget:visibility') . '&nbsp;';
echo elgg_view('input/dropdown', array(
	'name' => 'params[sets_hide_style]',
	'value' => $vars['entity']->sets_hide_style ? $vars['entity']->sets_hide_style : 'no',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	),
	'class' => 'au-sets-widget-visibility-select'
));

echo '<br><br>';