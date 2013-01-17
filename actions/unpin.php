<?php

$set_guid = get_input('set_guid');
$entity_guid = get_input('entity_guid');

$set = get_entity($set_guid);
$entity = get_entity($entity_guid);

// make sure we load our functions
elgg_load_library('au_sets');

if (au_sets_unpin_entity($entity, $set)) {
  system_message(elgg_echo('au_sets:success:unpinned'));
}

forward(REFERER);