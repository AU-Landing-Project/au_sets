elgg.provide('elgg.au_sets');

elgg.au_sets.init = function() {

	// Initialize global variable for ajax requests
	elgg.au_sets.ajax_request = false;

    // Initialize pin click
    elgg.au_sets.pinclick();
		
	// Initialize pin modal close
	elgg.au_sets.closeModal();
	
	// Initialize set text search
	elgg.au_sets.search();
	
	// Initialize the pin action
	elgg.au_sets.pin();
	
	// Initialize unpin click
	elgg.au_sets.unpin();
	
	// Initialize location input
	elgg.au_sets.input();
	
	// Initialize set_item widget search
	elgg.au_sets.set_item();

	// Set the item as the widget input
	elgg.au_sets.set_item_select();
	
	// layout preview
	elgg.au_sets.preview();
};


/**
 *	Initializes click of the pin button
 * opens dialog listing sets that are writeable by the user
 *
 */
elgg.au_sets.pinclick = function() {

  $('.au-sets-pin').live('click', function(e) {
	e.preventDefault();
	
	// remove any existing modals first - we only want one active
	$('.au-sets-selector').remove();
	
	var parent = $(this).parent();
	var span = $(this).children('span').eq(0);
	var guid = span.attr('data-guid');
	var div_id = 'au-sets-selector-'+guid;
	var offset = $(this).offset();
	
	$('body').prepend('<div class="au-sets-selector au-sets-throbber hidden" id="'+div_id+'"></div>');
	var modal = $('#'+div_id);
	var left = Math.round(offset.left - 230);
	var top = Math.round(offset.top + 20);
	
	// position it relative to the pin link
	modal.css('marginTop', top + 'px');
	modal.css('marginLeft', left + 'px');
	modal.hide().fadeIn(1000);
	
	// get the list of writeable sets
	elgg.get('ajax/view/au_sets/search', {
      timeout: 120000, //2 min
      data: {
        guid: guid
      },
      success: function(result, success, xhr){
		modal.removeClass('au-sets-throbber');
        modal.html(result);
      },
      error: function(result, response, xhr) {
		modal.removeClass('au-sets-throbber');
        if (response == 'timeout') {
          modal.html(elgg.echo('au_sets:error:timeout'));
        }
		else {
		  modal.html(elgg.echo('au_sets:error:generic'));
		}
      }
    });
  });
};


/**
 *	closes the sets modal
 *
 */
elgg.au_sets.closeModal = function() {
  $('.au-sets-selector-close').live('click', function(e) {
	e.preventDefault();
	
	$('.au-sets-selector').remove();
  });
}


/**
 *	handles the search interface
 *
 */
elgg.au_sets.search = function() {
  $('.au-sets-query').live('keyup', function(e) {
	var query = $(this).val();
	var guid = $(this).attr('data-guid');
	var mine = $('.au-sets-query-mine').is(':checked');
	
	
	// no sense searching for tiny strings
	if (query.length < 3) {
	  return;
	}
	
	// cancel any existing ajax requests
	// there's a good chance one was initiated
	// for fast typers
	if (elgg.au_sets.ajax_request) {
	  elgg.au_sets.ajax_request.abort();
	}
	
	
	// now we can search
	// first clear the existing results and add a throbber
	var results = $('#au-sets-selector-results-'+guid);
	
	results.addClass('au-sets-throbber');
	results.html('');
	
	// get the results
	elgg.au_sets.ajax_request = elgg.get('ajax/view/au_sets/search_results', {
      timeout: 120000, //2 min
      data: {
        guid: guid,
		query: query,
		filter_mine: mine
      },
      success: function(result, success, xhr){
		results.removeClass('au-sets-throbber');
        results.html(result);
      },
      error: function(result, response, xhr) {
		results.removeClass('au-sets-throbber');
        if (response == 'timeout') {
          results.html(elgg.echo('au_sets:error:timeout'));
        }
		else {
		  results.html(elgg.echo('au_sets:error:generic'));
		}
      }
    });
	alert(au_set_request.toSource());
  });
}

/**
 *	handles the pin action
 *
 */
elgg.au_sets.pin = function() {
  $('.au-set-result').live('click', function(e) {
	e.preventDefault();
	
	// only attempt pinning if it's not already pinned
	if ($(this).hasClass('au-set-result-pinned')) {
	  elgg.system_message(elgg.echo('au_sets:error:existing:pin'));
	  return;
	}
	
	var set_guid = $(this).attr('data-set');
	var entity_guid = $(this).attr('data-entity');
	
	// let the user know we're doing something
	// save the html in a var until we're done in case we need to revert
	// then we'll empty it and put in a throbber
	var entity = $(this);
	var html = entity.html();
	entity.addClass('au-sets-throbber');
	entity.html('');
	
	//something went wrong, lets put the html back
	elgg.action('au_sets/pin', {
      timeout: 120000, //2 min
      data: {
        set_guid: set_guid,
		entity_guid: entity_guid
      },
      success: function(result, success, xhr){
		if (result.status == 0) {
		  entity.removeClass('au-sets-throbber');
		  entity.addClass('au-set-result-pinned');
		  entity.html(html);
		}
		else {
		  entity.removeClass('au-sets-throbber');
		  entity.html(html);
		}
      },
      error: function(result, response, xhr) {
		entity.removeClass('au-sets-throbber');
        if (response == 'timeout') {
          elgg.register_error(elgg.echo('au_sets:error:timeout'));
		  entity.html(html);
        }
		else {
		  elgg.register_error(elgg.echo('au_sets:error:generic'));
		  entity.html(html);
		}
      }
	});
	
  });
}


elgg.au_sets.unpin = function() {
  $('.au-sets-unpin').live('click', function(e) {
	e.preventDefault();
	
	if (!confirm(elgg.echo('au_sets:unpin:confirm'))) {
	  return;
	}
	
	var span = $(this).children('span').eq(0);
	var entity_guid = span.attr('data-guid');
	var set_guid = $('.au-sets-guid-markup').attr('data-set');
	var entity = $(this).parents('.elgg-item').eq(0);
	
	// store html in case of failure
	var html = entity.html();
	
	// make it a throbber for feedback
	entity.html('');
	entity.addClass('au-sets-throbber');
	
	//something went wrong, lets put the html back
	elgg.action('au_sets/unpin', {
      timeout: 120000, //2 min
      data: {
        set_guid: set_guid,
		entity_guid: entity_guid
      },
      success: function(result, success, xhr){
		if (result.status == 0) {
		  entity.fadeOut(1500, function() { entity.remove(); });
		}
		else {
		  entity.removeClass('au-sets-throbber');
		  entity.html(html);
		}
      },
      error: function(result, response, xhr) {
		entity.removeClass('au-sets-throbber');
        if (response == 'timeout') {
          elgg.register_error(elgg.echo('au_sets:error:timeout'));
		  entity.html(html);
        }
		else {
		  elgg.register_error(elgg.echo('au_sets:error:generic'));
		  entity.html(html);
		}
      }
	});
	
  });
}


elgg.au_sets.input = function() {
  $('.au-set-add-new-row').live('click', function(e) {
	e.preventDefault();
	var numcols = $('.au-set-num-columns').val();
	
	if (numcols == 0) {
	  return;
	}
	
	
	
  });
}


elgg.au_sets.set_item = function() {
  $('.au-set-item-add').live('click', function(e) {
	e.preventDefault();
	
	// remove any existing modals first - we only want one active
	$('.au-sets-selector').remove();
	
	var widget_guid = $(this).attr('data-widget');
	var set_guid = $(this).attr('data-set');
	var offset = $(this).offset();
	var div_id = 'au-set-item-select-'+widget_guid;
	
	$('body').prepend('<div class="au-sets-selector au-sets-throbber hidden" id="'+div_id+'"></div>');
	var modal = $('#'+div_id);
	var left = Math.round(offset.left - 230);
	var top = Math.round(offset.top + 20);

	// position it relative to the pin link
	modal.css('marginTop', top + 'px');
	modal.css('marginLeft', left + 'px');
	modal.hide().fadeIn(1000);
	
	// get the list of writeable sets
	elgg.get('ajax/view/au_sets/item_search', {
      timeout: 120000, //2 min
      data: {
        guid: set_guid,
		widget_guid: widget_guid
      },
      success: function(result, success, xhr){
		modal.removeClass('au-sets-throbber');
        modal.html(result);
      },
      error: function(result, response, xhr) {
		modal.removeClass('au-sets-throbber');
        if (response == 'timeout') {
          modal.html(elgg.echo('au_sets:error:timeout'));
        }
		else {
		  modal.html(elgg.echo('au_sets:error:generic'));
		}
      }
    });
  });
}

/**
*
*	Selects the individual pin to use on the single pin widget
*
*/
elgg.au_sets.set_item_select = function() {
  $('.au-sets-item-search-results .au-set-item-preview').live('click', function(e) {
	e.preventDefault();
	
	var widget_guid = $(this).attr('data-widget');
	var item_guid = $(this).attr('data-item');
	var html = $(this).html();
	
	// insert the html into the widget
	$('#au-set-item-selected-'+widget_guid).html(html);
	
	// set this hidden input value
	$('#au-set-item-input-'+widget_guid).val(item_guid);
	
	// remove the modal
	$('.au-sets-selector').remove();
  });
}


elgg.au_sets.preview = function() {
  $('.au-set-layout-select').live('change', function() {
	$('#au-set-layout-preview').html('').addClass('au-sets-throbber');
  
	var layout = $(this).val();
	
	// get the list of writeable sets
	elgg.get('ajax/view/au_sets/layout_preview', {
      timeout: 120000, //2 min
      data: {
        layout: layout
      },
      success: function(result, success, xhr){
		$('#au-set-layout-preview').removeClass('au-sets-throbber').html(result);
      },
      error: function(result, response, xhr) {
        if (response == 'timeout') {
          elgg.register_error(elgg.echo('au_sets:error:timeout'));
        }
		else {
		  elgg.register_error(elgg.echo('au_sets:error:generic'));
		}
      }
    });
  });
}

// reassign the widgets move function to our variable
// then reassign the widgets move variable
elgg.au_sets.widgets_move = elgg.ui.widgets.move;

elgg.ui.widgets.move = function(event, ui) {
  elgg.au_sets.widgets_move(event, ui);
  
  // reset row heights
  // only if our normalization is defined
  if (typeof au_sets_normalize_widget_height == 'function') {
	au_sets_normalize_widget_height();
  }
}

elgg.register_hook_handler('init', 'system', elgg.au_sets.init);
