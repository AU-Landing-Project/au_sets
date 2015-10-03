<?php

namespace AU\Sets;


/**
 * pagesetup - register title buttons if can edit the set
 */
function pagesetup() {
	if (elgg_get_context() == 'pinboards') {
		$set = elgg_get_page_owner_entity();

		if (elgg_instanceof($set, 'object', 'au_set') && $set->canEdit()) {
			elgg_register_title_button('sets', 'edit');
		}
	}
}

function upgrades() {
	if (!elgg_is_admin_logged_in()) {
		return true;
	}
	
	require_once __DIR__ . '/upgrades.php';
	run_function_once(__NAMESPACE__ . '\\upgrade20151003');
}