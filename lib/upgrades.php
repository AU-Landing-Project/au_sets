<?php

namespace AU\Sets;

/**
 * set the initial version if not already set
 * 
 * @return boolean
 */
function upgrade20151003() {
	$version = (int) elgg_get_plugin_setting('version', PLUGIN_ID);
	if ($version >= PLUGIN_VERSION) {
		return true;
	}
	
	elgg_set_plugin_setting('version', 20151003, PLUGIN_ID);
	return true;
}

