<?php

/**
 * If $vars['query'] exists then do as a search on title
 * otherwise return latest 5 results
 */

if (!elgg_is_logged_in()) {
  return;
}

$mine = true;
if ($vars['filter_mine'] == 'false') {
  $mine = false;
}

$entity = $vars['entity'];
if (!elgg_instanceof($entity)) {
  echo elgg_echo('au_sets:error:invalid:entity');
  return;
}

$query = sanitise_string($vars['query']);


$user = elgg_get_logged_in_user_entity();
$write_accesses = au_sets_get_write_accesses($user);


$dbprefix = elgg_get_config('dbprefix');

$options = array(
	'types' => array('object'),
	'subtypes' => array('au_set'),
	'pagination' => false,
	'limit' => 5,
	
	// custom values to pass through to $vars for display
	'view_context' => 'ajax_results',
	'target_entity_guid' => $entity->guid
);


$options['joins'] = array();
$options['joins'][] = "JOIN {$dbprefix}objects_entity o ON e.guid = o.guid";
	
// where's
$options['wheres'] = array();

// only search others if 'mine' is not set
if (!$mine) {
  //only show if we have write access to it
  $write_in = implode(', ', $write_accesses);
  $friends_in = "SELECT guid_one FROM {$dbprefix}entity_relationships WHERE guid_two = {$user->guid} AND relationship = 'friend'";

  // restrict to some write accesses, friends, or stuff the user owns
  // @TODO - is there any way to make these granular?
  $options['wheres'][] = "(ms.string IN({$write_in}) OR (ms.string = '" . ACCESS_FRIENDS . "' AND e.owner_guid IN({$friends_in})) OR (e.owner_guid = {$user->guid}))";
  
  
  $metadata_name_id = get_metastring_id('write_access_id');
  $options['joins'][] = "JOIN {$dbprefix}metadata md ON e.guid = md.entity_guid AND md.name_id = {$metadata_name_id}";
  $options['joins'][] = "JOIN {$dbprefix}metastrings ms ON ms.id = md.value_id";
}
else {
  $options['owner_guids'] = array($user->guid);
}
	
/*
 * @TODO - Jon wants everything shown, thinks it's better UX
 * leaving here in case he changes his mind
 * 
// don't show anything already pinned
$options['wheres'][] = "NOT EXISTS ( SELECT 1 FROM {$dbprefix}entity_relationships WHERE guid_two = e.guid AND guid_one = {$entity->guid} AND relationship = '" . AU_SETS_PINNED_RELATIONSHIP . "' )";
 * 
 */

// prevent a set from pinning itself
$options['wheres'][] = "e.guid != {$entity->guid}";


// if we're doing a search
if ($query) {
  $options['selects'] = array("MATCH(o.title, o.description) AGAINST('$query') as relevance");
  $options['wheres'][] = "MATCH(o.title, o.description) AGAINST('$query')";
  $options['order_by'] = 'relevance DESC';
}

echo elgg_list_entities_from_metadata($options);
