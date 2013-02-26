<?php

$container = $vars['entity']->getContainerEntity();
$limit = $vars['entity']->num_results ? $vars['entity']->num_results : 10;

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'au_set',
	'container_guid' => $container->guid,
	'limit' => $limit,
	'full_view' => false
));

if (!$content) {
  echo elgg_echo('au_sets:none');
}
else {
  echo $content;
}
