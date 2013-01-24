<?php

$guid = $vars['entity']->subject_guid;
$full = $vars['entity']->full_view ? true : false;

$subject = get_entity($guid);
if (elgg_instanceof($subject)) {
  echo elgg_view_entity($subject, array('full_view' => $full));
}
else {
  echo elgg_echo('au_sets:set_list:invalid:entity');
}