<?php
/**
 * Save au_set entity
 *
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('au_set');

// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;

// edit or create a new entity
$guid = get_input('guid');
$pin_guid = get_input('pin');
$pin = get_entity($pin_guid);

if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'au_set') && $entity->canEdit()) {
		$set = $entity;
	} else {
		register_error(elgg_echo('au_sets:error:post_not_found'));
		forward(get_input('forward', REFERER));
	}

} else {
	$set = new ElggObject();
	$set->subtype = 'au_set';
	$new_post = TRUE;
}

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'access_id' => ACCESS_DEFAULT,
	'write_access_id' => ACCESS_PRIVATE,
	'comments_on' => 'On',
	'tags' => '',
	'container_guid' => (int)get_input('container_guid'),
	'layout' => '[[100]]',
);

// fail if a required entity isn't set
$required = array('title');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	if ($name === 'title') {
		$value = htmlspecialchars(get_input('title', $default, false), ENT_QUOTES, 'UTF-8');
	} else {
		$value = get_input($name, $default);
	}

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("au_sets:error:missing:$name");
	}

	if ($error) {
		break;
	}

	switch ($name) {
		case 'tags':
			if ($value) {
				$values[$name] = string_to_tag_array($value);
			} else {
				unset ($values[$name]);
			}
			break;

		case 'container_guid':
			// this can't be empty or saving the base entity fails
			if (!empty($value)) {
					$values[$name] = $value;
			} else {
				unset($values[$name]);
			}
			break;

		// don't try to set the guid
		case 'guid':
			unset($values['guid']);
			break;

		default:
			$values[$name] = $value;
			break;
	}
}


// assign values to the entity, stopping on error.
if (!$error) {
	foreach ($values as $name => $value) {
		if (FALSE === ($set->$name = $value)) {
			$error = elgg_echo('au_sets:error:cannot_save', array("$name=$value"));
			break;
		}
	}
}

// only try to save base entity if no errors
if (!$error) {
	if ($set->save()) {
		// remove sticky form entries
		elgg_clear_sticky_form('au_set');

		system_message(elgg_echo('au_sets:message:saved'));


		if ($new_post) {
			add_to_river('river/object/au_set/create', 'create', $set->owner_guid, $set->getGUID());
		}
		
		$has_uploaded_icon = (!empty($_FILES['icon']['type']) && substr_count($_FILES['icon']['type'], 'image/'));
		
		if ($has_uploaded_icon) {

		  $icon_sizes = elgg_get_config('icon_sizes');

		  $prefix = "pinboards/" . $set->guid;

		  $filehandler = new ElggFile();
		  $filehandler->owner_guid = $set->owner_guid;
		  $filehandler->setFilename($prefix . ".jpg");
		  $filehandler->open("write");
		  $filehandler->write(get_uploaded_file('icon'));
		  $filehandler->close();
		  $filename = $filehandler->getFilenameOnFilestore();
		  
		  $sizes = array('tiny', 'small', 'medium', 'large');

		  $thumbs = array();
		  foreach ($sizes as $size) {
			$thumbs[$size] = get_resized_image_from_existing_file(
			  $filename,
			  $icon_sizes[$size]['w'],
			  $icon_sizes[$size]['h'],
			  $icon_sizes[$size]['square']
			);
		  }

		  if ($thumbs['tiny']) { // just checking if resize successful
			$thumb = new ElggFile();
			$thumb->owner_guid = $set->owner_guid;
			$thumb->setMimeType('image/jpeg');
			
			foreach ($sizes as $size) {
			  $thumb->setFilename("{$prefix}{$size}.jpg");
			  $thumb->open("write");
			  $thumb->write($thumbs[$size]);
			  $thumb->close();
			}

			$set->icontime = time();
		  }
		}
		
		if ($pin) {
		  if (au_sets_pin_entity($pin, $set)) {
			$name = $pin->title ? $pin->title : $pin->name;
			system_message(elgg_echo('au_sets:autopinned', array($name)));
		  }
		}
		
		// send to the set in layout mode
		forward($set->getURL() . '?view_layout=1');

	}
	else {
		register_error(elgg_echo('au_sets:error:cannot_save'));
		forward($error_forward_url);
	}
}
else {
	register_error($error);
	forward($error_forward_url);
}
