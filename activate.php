<?php

namespace AU\Sets;

$version = elgg_get_plugin_setting('version', PLUGIN_ID);
if (!$version) {
	elgg_set_plugin_setting('version', PLUGIN_VERSION, PLUGIN_ID);
}

if (!get_subtype_id('object', 'au_set')) {
	add_subtype('object','au_set');
}