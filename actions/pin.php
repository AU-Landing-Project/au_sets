<?php

$set_guid = get_input('set_guid');
$entity_guid = get_input('entity_guid');

$set = get_entity($set_guid);
$entity = get_entity($entity_guid);

// make sure we load our functions
elgg_load_library('au_sets');

$error = au_sets_pin_sanity_check($entity, $set);

if ($error) {
  register_error($error);
}
elseif (au_sets_is_pinned($entity, $set)) {
  register_error(elgg_echo('au_sets:error:existing:pin'));
}
elseif (au_sets_pin_entity($entity, $set)) {
  system_message(elgg_echo('au_sets:success:pinned'));
}
else {
  register_error(elgg_echo('au_sets:error:generic'));
}

forward(REFERER);