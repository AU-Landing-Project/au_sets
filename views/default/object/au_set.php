<?php

$full = elgg_extract('full_view', $vars, FALSE);
$set = elgg_extract('entity', $vars, FALSE);

if (!$set) {
	return TRUE;
}

// see if we need to show just a mimimal view for ajax results
// set in the view au_sets/search_results
if ($vars['view_context'] == 'ajax_results') {
  echo elgg_view('object/au_set/ajax_result', $vars);
  return;
}

$owner = $set->getOwnerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = $set->excerpt;
if (!$excerpt) {
	$excerpt = elgg_get_excerpt($set->description);
}

$icon = elgg_view_entity_icon($set, 'tiny');
$link = elgg_view('output/url', array(
	'href' => "sets/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));

$authored_by = elgg_echo('au_sets:authored_by', array($link));

$date = elgg_view_friendly_time($set->time_created);

// The "on" status changes for comments, so best to check for !Off
if ($set->comments_on != 'Off') {
	$comments_count = $set->countComments();
	//only display if there are commments
	if ($comments_count != 0) {
		$text = elgg_echo("comments") . " ($comments_count)";
		$comments_link = elgg_view('output/url', array(
			'href' => $set->getURL() . '#set-comments',
			'text' => $text,
			'is_trusted' => true,
		));
	} else {
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'au_set',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$authored_by $date $comments_link $categories";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
  
  $icon = elgg_view_entity_icon($set, 'large');

  $content = elgg_view('output/longtext', array(
	'value' => $set->description,
	'class' => 'set-description',
  ));

  $params = array(
	'entity' => $set,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'content' => $content
  );
  
  $params = $params + $vars;
  $summary = elgg_view('object/elements/summary', $params);
  
  $context = elgg_get_context();
  elgg_set_context('au_sets_profile');
  $body = elgg_list_entities_from_relationship(array(
	  'relationship_guid' => $set->guid,
	  'relationship' => AU_SETS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'full_view' => false,
	  'limit' => 10,
	  'order_by' => 'r.time_created DESC'
  ));
  
  // add invisible markup to contain set guid for js
  $body .= '<div class="au-sets-guid-markup" data-set="' . $set->guid . '"></div>';
  
  elgg_set_context($context);

  echo elgg_view('object/elements/full', array(
	'summary' => $summary,
	'icon' => $icon,
	'body' => $body,
  ));

} else {
	// brief view

	$params = array(
		'entity' => $set,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body);
}
