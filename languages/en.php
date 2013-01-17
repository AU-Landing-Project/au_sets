<?php

$english = array(
	'au_sets:set' => 'Set',
	'au_sets:sets' => 'Sets',
	'item:object:au_set' => 'Sets',
	'au_sets:enablesets' => "Enable group Sets",
	'au_sets:group' => 'Group Sets',
	'au_sets:title:user_sets' => '%s\'s Sets',
	'au_sets:title:all_sets' => "All site Sets",
	'au_sets:none' => "No Sets",
	'au_sets:title:friends' => 'Friends\' Sets',
	'au_sets:message:deleted' => "Set has been deleted",
	'au_sets:error:cannot_delete' => "Could not delete the Set",
	'au_sets:error:not_found' => "Could not find the Set",
	'au_sets:error:invalid:set' => "Invalid Set",
	'au_sets:error:invalid:entity' => "Invalid entity",
	'au_sets:error:cannot:edit' => "You do not have permission to edit this Set",
	'au_sets:error:invalid:user' => "Invalid user",
	'au_sets:error:recursive:pin' => "Cannot pin a Set to itself",
	'au_sets:pin' => "Pin",
	'au_sets:pin:to:this' => "Pin it",
	'au_sets:error:timeout' => "A timeout error has occurred...",
	'au_sets:error:generic' => "An error has occurred...",
	'au_sets:pin:to' => "Pin to a Set",
	'au_sets:search' => "Search for a Set",
	'au_sets:search:help' => "Type a word or words that appears in the title or description of the Set you are looking for",
	'au_sets:success:pinned' => "Content has been pinned",
	
	// Editing
	'au_sets:add' => 'Add Set',
	'sets:add' => 'Add Set',
	'au_sets:edit' => 'Edit Set',
	'au_sets:body' => 'Body',
	'au_sets:never' => 'Never',
	'au_sets:label:icon' => "Set Icon (Leave blank to remain unchanged)",
	'au_sets:label:write_access' => "Who can edit this Set?",
	
	// messages
	'au_sets:error:cannot_save' => 'Cannot save Set.',
	'au_sets:error:cannot_edit' => 'This Set may not exist or you may not have permissions to edit it.',
	'au_sets:error:post_not_found' => "This Set cannot be found",
	'au_sets:error:missing:title' => "Title cannot be empty",
	'au_sets:error:cannot_write_to_container' => "Error - cannot write to container",
	'au_sets:error:cannot_save' => "Error - cannot save the value %s",
	'au_sets:message:saved' => "Set has been saved",
	'au_sets:error:cannot_save' => "Cannot save Set",
	
	// river
	'au_sets:river:create' => "%s has created a new Set %s",
	
	/* notifications */
	'au_sets:newset' => 'A new Set has been created',
	'au_sets:notification' =>
'
%s made a new Set.

%s
%s

View and comment on the new Set:
%s
',
);
					
add_translation("en",$english);