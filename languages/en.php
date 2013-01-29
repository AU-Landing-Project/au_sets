<?php

$english = array(
	'au_sets:set' => 'Set',
	'au_sets:sets' => 'Sets',
	'item:object:au_set' => 'Sets',
	'au_sets:enablesets' => "Enable group Sets",
	'au_sets:group' => 'Group sets',
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
	'au_sets:search' => "Filter Sets",
	'au_sets:search:help' => "Type a word or words that appears in the title or description of the Set you are looking for",
	'au_sets:success:pinned' => "Content has been pinned",
	'au_sets:success:unpinned' => "Content has been unpinned",
	'au_sets:authored_by' => "By %s",
	'au_sets:unpin' => 'UnPin',
	'au_sets:unpin:confirm' => "Are you sure you want to unpin this item?",
	'au_sets:error:existing:pin' => "This content is already pinned to that set",
	'au_sets:search:mine' => 'Restrict results to sets I created',
	'au_sets:ingroup' => "in the group %s",
	'au_sets:error:unpinned' => "Content is already unpinned - possibly an incorrect id",
	
	// Editing
	'au_sets:add' => 'Add Set',
	'sets:add' => 'Add Set',
	'au_sets:edit' => 'Edit Set',
	'au_sets:body' => 'Body',
	'au_sets:never' => 'Never',
	'au_sets:label:icon' => "Set Icon (Leave blank to remain unchanged)",
	'au_sets:label:write_access' => "Who can edit this Set?",
	'au_sets:label:layout' => "Set the layout",
	'au_sets:add:new:row' => "Add new row",
	'au_sets:how:many:columns' => "How many columns?",
	'sets:edit' => 'Edit',
	
	
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
	'river:comment:object:au_set' => '%s commented on the set %s',
	
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
	
	// widget manager
	'widget_manager:widgets:lightbox:title:au_sets_profile' => "Widgets for Sets",
	'widget_manager:widgets:lightbox:title:sets' => "Widgets for Sets",
	
	// widgets
	'au_sets:widget:set_avatar:description' => "Display the avatar of the set in configurable size",
	'au_sets:widget:set_avatar:title' => "Set Avatar",
	'au_sets:widget:set_description:title' => "Set Description",
	'au_sets:widget:set_description:description' => "Profile information for the set, description, author, tags, etc.",
	'au_sets:num:results' => "Number of items to display",
	'au_sets:widget:set_list:title' => "Recent Pins",
	'au_sets:widget:set_list:description' => "Display a list of recently pinned content",
	'au_sets:set_list:invalid:entity' => "Widget is not configured or the entity is no longer accessible",
	'au_sets:set_list:full_view:help' => "Note: some content may not change support this option",
	'au_sets:widget:set_item:title' => "Single Pin",
	'au_sets:widget:set_item:description' => "Display a single pinned item in either a full or condensed view",
	'au_sets:set_list:full_view' => "Choose how to display the content",
	'au_sets:full_view:false' => "Condensed View",
	'au_sets:full_view:true' => "Full View",
	'au_sets:item:add' => "Select Pinned Item",
	'au_sets:item:add:help' => "Note that all users may not be able to see this content depending on its access settings which are independent of the access of this display",
	'au_sets:not:pinned' => "Content is no longer pinned",
	'au_sets:comments:off' => "Comments are disabled for this set",
	'au_sets:widget:set_comments:title' => "Comments",
	'au_sets:widget:set_comments:description' => "Display comments for this set",
	'au_sets:comments:new_comments' => "Allow the addition of new comments?",
	
);
					
add_translation("en",$english);