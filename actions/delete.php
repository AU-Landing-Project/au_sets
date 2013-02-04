<?php
/**
 * Delete au_set entity
 *
 */

$guid = get_input('guid');
$set = get_entity($guid);

if (elgg_instanceof($set, 'object', 'au_set') && $set->canEdit()) {
	$container = $set->getContainerEntity();
	if ($set->delete()) {
		system_message(elgg_echo('au_sets:message:deleted'));
		if (elgg_instanceof($container, 'group')) {
			forward("pinboards/group/$container->guid/all");
		} else {
			forward("pinboards/owner/$container->username");
		}
	} else {
		register_error(elgg_echo('au_sets:error:cannot_delete'));
	}
} else {
	register_error(elgg_echo('au_sets:error:not_found'));
}

forward(REFERER);