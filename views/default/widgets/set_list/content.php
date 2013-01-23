<?php

$set = $vars['entity']->getContainerEntity();
$limit = $vars['entity']->num_results ? $vars['entity']->num_results : 10;

echo elgg_list_entities_from_relationship(array(
	  'relationship_guid' => $set->guid,
	  'relationship' => AU_SETS_PINNED_RELATIONSHIP,
	  'inverse_relationship' => true,
	  'full_view' => false,
	  'limit' => $limit,
	  'order_by' => 'r.time_created DESC',
	  'pagination' => false
  ));