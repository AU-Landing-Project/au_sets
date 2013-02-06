<?php

$set = $vars['entity']->getContainerEntity();

$owner = $set->getOwnerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = $set->excerpt;
if (!$excerpt) {
	$excerpt = elgg_get_excerpt($set->description);
}

$link = elgg_view('output/url', array(
	'href' => "pinboards/owner/$owner->username",
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
  
  echo elgg_view('object/elements/summary', $params);
