<?php

$set = elgg_extract('entity', $vars, FALSE);

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

$date = elgg_view_friendly_time($vars['entity']->time_created);

$body .= elgg_view('output/longtext', array('value' => $owner_link . '&nbsp;' . $date, 'class' => 'elgg-subtext'));


$pin_link = elgg_view('output/url', array(
	'text' => elgg_echo('au_sets:pin:to:this'),
	'href' => '#',
	'class' => 'au-sets-pin-action',
	'rel' => $set->getGUID()
));

$image_alt = array('image_alt' => $pin_link);

echo '<div class="au-set-result" data-guid="' . $set->getGUID() . '">';
echo elgg_view_image_block($icon, $body);
echo '</div>';