<?php

$object = $vars['item']->getObjectEntity();
$subject = $vars['item']->getSubjectEntity();

$excerpt = elgg_get_excerpt(strip_tags($object->description));

$summary = elgg_echo('au_sets:river:create', array(
	elgg_view('output/url', array('text' => $subject->name, 'href' => $subject->getURL())),
	elgg_view('output/url', array('text' => $object->title, 'href' => $object->getURL()))
));

$message = elgg_view_image_block(
		elgg_view_entity_icon($object, 'small'),
		$excerpt
);

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'message' => $message,
	'summary' => $summary
));