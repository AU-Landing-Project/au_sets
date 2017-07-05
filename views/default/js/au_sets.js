define(['require', 'jquery', 'elgg'], function(require, $, elgg) {
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

        // update widget class in real time
        elgg.au_sets.widget_save();
        
        if ($('.au-sets-row').length) {
            $('.au-sets-row').each(function(index, item) {
                var id = $(item).attr('id');

                $('#'+id).css('min-height', '20px');
                elgg.ui.widgets.setMinHeight('#' + id);
            });
        }
    };


    /**
     *	Initializes click of the pin button
     * opens dialog listing sets that are writeable by the user
     *
     */
    elgg.au_sets.pinclick = function() {

        $(document).on('click', '.au-sets-pin', function(e) {
            e.preventDefault();

            // remove any existing modals first - we only want one active
            $('.au-sets-selector').remove();

            var parent = $(this).parent();
            var span = $(this).children('span').eq(0);
            var guid = span.attr('data-guid');
            var div_id = 'au-sets-selector-' + guid;
            var offset = $(this).offset();

            $('body').prepend('<div class="au-sets-selector au-sets-throbber hidden" id="' + div_id + '"></div>');
            var modal = $('#' + div_id);
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
                    guid: guid,
                    pageowner: elgg.get_page_owner_guid()
                },
                success: function(result, success, xhr) {
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
        $(document).on('click', '.au-sets-selector-close', function(e) {
            e.preventDefault();

            $('.au-sets-selector').remove();
        });
    };


    /**
     *	handles the search interface
     *
     */
    elgg.au_sets.search = function() {
        $(document).on('keyup', '.au-sets-query', function(e) {
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
            var results = $('#au-sets-selector-results-' + guid);

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
                success: function(result, success, xhr) {
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

        });
    };

    /**
     *	handles the pin action
     *
     */
    elgg.au_sets.pin = function() {
        $(document).on('click', '.au-set-result', function(e) {
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
                success: function(result, success, xhr) {
                    if (result.status == 0) {
                        entity.removeClass('au-sets-throbber');
                        entity.addClass('au-set-result-pinned');
                        entity.html(html);
                        $('#au-sets-entity-goto-' + set_guid).removeClass('hidden');
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
    };


    elgg.au_sets.unpin = function() {
        $(document).on('click', '.au-sets-unpin', function(e) {
            e.preventDefault();

            var span = $(this).children('span').eq(0);
            var entity_guid = span.attr('data-guid');
            var set_guid = $('.au-sets-guid-markup').attr('data-set');
            var entity = $(this).parents('.elgg-item').eq(0);

            if (!confirm(elgg.echo('au_sets:unpin:confirm'))) {
                return;
            }

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
                success: function(result, success, xhr) {
                    if (result.status == 0) {
                        entity.fadeOut(1500, function() {
                            entity.remove();
                        });
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
    };


    elgg.au_sets.input = function() {
        $(document).on('click', '.au-set-add-new-row', function(e) {
            e.preventDefault();
            var numcols = $('.au-set-num-columns').val();

            if (numcols == 0) {
                return;
            }



        });
    };


// select the single pinned item on the single pin widget
    elgg.au_sets.set_item = function() {
        $(document).on('click', '.au-set-item-add', function(e) {
            e.preventDefault();

            // remove any existing modals first - we only want one active
            $('.au-sets-selector').remove();

            var widget_guid = $(this).attr('data-widget');
            var set_guid = $(this).attr('data-set');
            var offset = $(this).offset();
            var div_id = 'au-set-item-select-' + widget_guid;

            $('body').prepend('<div class="au-sets-selector au-sets-throbber hidden" id="' + div_id + '"></div>');
            var modal = $('#' + div_id);
            var left = Math.round(offset.left);
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
                success: function(result, success, xhr) {
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
     *
     *	Selects the individual pin to use on the single pin widget
     *
     */
    elgg.au_sets.set_item_select = function() {
        $(document).on('click', '.au-sets-item-search-results .au-set-item-preview', function(e) {
            e.preventDefault();

            var widget_guid = $(this).attr('data-widget');
            var item_guid = $(this).attr('data-item');
            var html = $(this).html();

            // insert the html into the widget
            $('#au-set-item-selected-' + widget_guid).html(html);

            // set this hidden input value
            $('#au-set-item-input-' + widget_guid).val(item_guid);

            // remove the modal
            $('.au-sets-selector').remove();
        });
    };


    elgg.au_sets.preview = function() {
        $(document).on('click', '.au-sets-preview-wrapper', function(e) {
            e.preventDefault();

            $('.au-sets-preview-wrapper').removeClass('selected');
            $(this).addClass('selected');

            // make our input use this layout
            var layout = $(this).attr('data-layout');

            $('#au-sets-layout-input').val(layout);
        });
    };

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
    };


    elgg.au_sets.widget_save = function() {
        $(document).on('submit', '.elgg-form-widgets-save', function() {
            var parent = $(this).parent();
            var guid = parent.attr('id').substr(12);
            var hide = true;

            var split = location.search.replace('?', '').split('&').map(function(val) {
                return val.split('=');
            });

            for (var i = 0; i < split.length; i++) {
                if (split[i][0] == 'view_layout' && split[i][1] == 1) {
                    hide = false;
                }
            }

            if (hide) {
                if ($('#elgg-widget-' + guid + ' .au-sets-widget-visibility-select').val() == 'yes') {
                    $('#elgg-widget-' + guid).addClass('au-sets-hide-style');
                }
                else {
                    $('#elgg-widget-' + guid).removeClass('au-sets-hide-style');
                }
            }

        });
    };

    elgg.register_hook_handler('init', 'system', elgg.au_sets.init);
});

