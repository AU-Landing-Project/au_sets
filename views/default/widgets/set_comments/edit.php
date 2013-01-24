<?php

echo elgg_echo('au_sets:comments:new_comments');

echo elgg_view('input/dropdown', array(
	'name' => 'params[new_comments]',
	'value' => !empty($vars['entity']->new_comments) ? $vars['entity']->new_comments : 'yes',
	'options_values' => array(
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	)
));

echo '<br><br>';