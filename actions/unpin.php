<?php

namespace AU\Sets;

$set_guid = get_input('set_guid');
$entity_guid = get_input('entity_guid');

$set = get_entity($set_guid);
$entity = get_entity($entity_guid);


if (!is_pinned($entity, $set)) {
  register_error(elgg_echo('au_sets:error:unpinned'));
  forward(REFERER);
}

if (unpin_entity($entity, $set)) {
  system_message(elgg_echo('au_sets:success:unpinned'));
}

forward(REFERER);