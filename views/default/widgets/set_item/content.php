<?php

$guid = $vars['entity']->subject_guid;
$full = $vars['entity']->full_view ? true : false;
$set = $vars['entity']->getContainerEntity();

$subject = get_entity($guid);
if (elgg_instanceof($subject)) {
  if (au_sets_is_pinned($subject, $set)) {
	
	if ($full) {
	  $title = $subject->title ? $subject->title : $subject->name;
	  echo "<h3>" . elgg_view('output/url', array('text' => $title, 'href' => $subject->getURL())) . "</h3>";
	}
	
	echo elgg_view_entity_list(array($subject), array('full_view' => $full));
  }
  else {
	echo elgg_echo('au_sets:not:pinned');
  }
}
else {
  echo elgg_echo('au_sets:set_list:invalid:entity');
}