<?php

elgg_load_library('au_sets');

$set = elgg_extract('entity', $vars, FALSE);
$entity_guid = elgg_extract('target_entity_guid', $vars, false);
$entity = get_entity($entity_guid);

if (!$set) {
	return TRUE;
}

$owner = $set->getOwnerEntity();
$icon = elgg_view_entity_icon($set, 'tiny');
$body = "<h4>" . strip_tags($set->title) . "</h4>";

$owner_link = '';
if ($owner) {
	$owner_link = elgg_view('output/url', array(
		'href' => $owner->getURL(),
		'text' => $owner->name,
		'is_trusted' => true,
	));
}

$ingroup = '';
$container = $set->getContainerEntity();
if (elgg_instanceof($container, 'group')) {
  $ingroup = elgg_echo('au_sets:ingroup', array(
	  elgg_view('output/url', array('text' => $container->name, 'href' => $container->getURL()))
  ));
}

$date = elgg_view_friendly_time($set->time_created);

$body .= elgg_view('output/longtext', array(
	'value' => elgg_echo('au_sets:authored_by', array($owner_link)) . '&nbsp;' . $ingroup . '&nbsp;' . $date, 'class' => 'elgg-subtext'));


$pin_link = elgg_view('output/url', array(
	'text' => elgg_echo('au_sets:pin:to:this'),
	'href' => '#',
	'class' => 'au-sets-pin-action',
	'rel' => $set->getGUID()
));


$class = 'au-set-result';
$title = '';
if (au_sets_is_pinned($entity, $set)) {
  $class .= ' au-set-result-pinned';
  $title .= ' title="' . elgg_echo('au_sets:error:existing:pin') . '"';
}

echo '<div class="' . $class . '" data-set="' . $set->getGUID() . '" data-entity="' . $entity_guid . '"' . $title . '>';
echo elgg_view_image_block($icon, $body);
echo '</div>';