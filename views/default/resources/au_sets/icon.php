<?php

namespace AU\Sets;

$pinboard = $vars['pinboard'];
$size = $vars['size'];

if (!elgg_instanceof($pinboard, 'object', 'au_set')) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

// If is the same ETag, content didn't changed.
$etag = $pinboard->icontime . $pinboard->guid;
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == "\"$etag\"") {
	header("HTTP/1.1 304 Not Modified");
	exit;
}

if (!in_array($size, array('large', 'medium', 'small', 'tiny', 'master', 'topbar'))) {
	$size = "medium";
}

$filehandler = new \ElggFile();
$filehandler->owner_guid = $pinboard->owner_guid;
$filehandler->setFilename("pinboards/" . $pinboard->guid . $size . ".jpg");

// back compatibility for sets that were created when we were using the name 'sets'
if (!file_exists($filehandler->getFilenameOnFilestore())) {
	$filehandler->setFilename("sets/" . $pinboard->guid . $size . ".jpg");
}

$success = false;
if ($filehandler->open("read")) {
	if ($contents = $filehandler->read($filehandler->getSize())) {
		$success = true;
	}
}

if (!$success) {
	$location = elgg_get_plugins_path() . PLUGIN_ID . "/graphics/default{$size}.jpg";
	$contents = @file_get_contents($location);
}

header("Content-type: image/jpeg");
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime("+10 days")), true);
header("Pragma: public");
header("Cache-Control: public");
header("Content-Length: " . strlen($contents));
header("ETag: \"$etag\"");
echo $contents;
