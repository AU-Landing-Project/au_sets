<?php
/**
 * This view is called via ajax
 */

if (!elgg_is_logged_in() || !$vars['entity']) {
  return;
}


echo elgg_view('output/url', array(
	'text' => '<span class="elgg-icon elgg-icon-delete-alt"></span>',
	'href' => '#',
	'class' => 'au-sets-selector-close au-sets-selector-close-top'
));

echo "<h3>" . elgg_echo('au_sets:item:add') . "</h3>";

echo '<div class="au-sets-item-search-results" id="au-sets-selector-results-' . $vars['entity']->guid . '">';

// get all items
$items = elgg_get_entities_from_relationship(array(
	  'relationship_guid' => $vars['entity']->guid,
	  'relationship' => AU_SETS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'order_by' => 'r.time_created DESC',
	  'limit' => false,
  ));

if ($items) {
  foreach ($items as $item) {
	
	$default_icon = elgg_get_site_url() . '_graphics/icons/default/tiny.png';
	$owner = $item->getOwnerEntity();
	
	if ($owner) {
	  $owner_link = elgg_view('output/url', array('href' => $owner->getURL(), 'text' => $owner->title ? $owner->title : $owner->name));
	}
	
	if ($item->getIconURL('tiny') == $default_icon) {
	  $icon_subject = $owner;
	}
	else {
	  $icon_subject = $item;
	}
	
	$icon = elgg_view_entity_icon($icon_subject, 'tiny', array('use_hover' => false, 'href' => false, 'use_link' => false));
	
	$body = elgg_view('output/url', array(
		'text' => $item->title ? $item->title : $item->name,
		'href' => $item->getURL()
	));
	
	$body .= elgg_view('output/longtext', array(
		'value' => $owner_link . '&nbsp;' . elgg_view_friendly_time($item->time_created),
		'class' => 'elgg-subtext'
	));
	
	//echo $item->getIconURL('tiny');
	echo '<div class="au-set-item-preview" data-widget="' . $vars['widget_guid'] . '" data-item="' . $item->guid . '">';
	echo elgg_view_image_block($icon, $body);
	echo '</div>';
  }
}
else {
  echo elgg_echo('au_sets:widget:set_item:noresults');
}

echo '</div>';

echo elgg_view('output/url', array(
	'text' => elgg_echo('close'),
	'href' => '#',
	'class' => 'au-sets-selector-close elgg-button elgg-button-delete'
));