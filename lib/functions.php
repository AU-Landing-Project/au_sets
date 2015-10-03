<?php

/**
 * Returns bool whether the entity is already pinned
 * assumes $entity and $set are valid objects
 * 
 * @param type $entity
 * @param type $set
 */
function au_sets_is_pinned($entity, $set) {
  return check_entity_relationship($entity->guid, AU_SETS_PINNED_RELATIONSHIP, $set->guid);
}


/**
 * Pull together set variables for the save form
 *
 * @param ElggObject       $set
 * @return array
 */
function au_sets_prepare_form_vars($set = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_PRIVATE,
		'comments_on' => 'On',
		'tags' => NULL,
		'container_guid' => NULL,
		'guid' => NULL,
	);

	if ($set) {
		foreach (array_keys($values) as $field) {
			if (isset($set->$field)) {
				$values[$field] = $set->$field;
			}
		}
	}

	if (elgg_is_sticky_form('au_set')) {
		$sticky_values = elgg_get_sticky_values('au_set');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	
	elgg_clear_sticky_form('au_set');

	return $values;
}


/**
 * Pins an entity to a given set
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 */
function au_sets_pin_entity($entity, $set, $user = NULL) {
  
  if (!au_sets_pin_sanity_check($entity, $set, $user)) {
	return add_entity_relationship($entity->getGUID(), AU_SETS_PINNED_RELATIONSHIP, $set->getGUID());
  }
  
  return false;
}

/**
 * Checks to make sure there are no errors with pinning/unpinning entities
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 * @return boolean
 */
function au_sets_pin_sanity_check($entity, $set, $user = NULL) {
  //make sure we have an entity
  if (!elgg_instanceof($entity)) {
	return elgg_echo('au_sets:error:invalid:entity');
  }
  
  if (!elgg_instanceof($set, 'object', 'au_set')) {
	return elgg_echo('au_sets:error:invalid:set');
  }
  
  if ($set->getGUID() == $entity->getGUID()) {
	return elgg_echo('au_sets:error:recursive:pin');
  }
  
  if (!elgg_instanceof($user, 'user')) {
	$user = elgg_get_logged_in_user_entity();
  }
  
  if (!$user) {
	return elgg_echo('au_sets:error:invalid:user');
  }
  
  // make sure we can edit the set
  if (!$set->canEdit($user->guid)) {
	return elgg_echo('au_sets:error:cannot:edit');
  }
  
  return false;
}

/**
 * Pins an entity to a given set
 * 
 * @param type $entity
 * @param type $set
 * @param type $user
 */
function au_sets_unpin_entity($entity, $set, $user = NULL) {
  
  if (!au_sets_pin_sanity_check($entity, $set, $user)) {
	return remove_entity_relationship($entity->getGUID(), AU_SETS_PINNED_RELATIONSHIP, $set->getGUID());
  }
  
  return false;
}


/**
 *  returns an array of accesses the user can write to sets
 * 	this is in start because we use it for a lot of hooks
 * 
 * @param type $user
 * @return type
 */
function au_sets_get_write_accesses($user) {
	if (!elgg_instanceof($user, 'user')) {
		return array(ACCESS_PUBLIC);
	}

	// write access is set using acl nomenclature
	$access = get_access_array($user->getGUID());

	// remove private and friends ids
	foreach (array(ACCESS_PRIVATE, ACCESS_FRIENDS) as $id) {
		if (($key = array_search($id, $access)) !== false) {
			unset($access[$key]);
		}
	}

	return $access;
}


function au_sets_add_widget_context($handle, $context) {

	if (!elgg_is_widget_type($handle)) {
		return false;
	}

	$widgets = elgg_get_config('widgets');

	if (!in_array($context, $widgets->handlers[$handle]->context)) {
		array_push($widgets->handlers[$handle]->context, $context);
	}

	elgg_set_config('widgets', $widgets);

	return true;
}