/**
 * jQuery Select3 Sortable
 * - enable select3 to be sortable via normal select element
 * 
 * author      : Vafour
 * modified    : Kevin Provance (kprovance)
 * inspired by : jQuery Chosen Sortable (https://github.com/mrhenry/jquery-chosen-sortable)
 * License     : GPL
 */

(function ($) {
    $.fn.extend({
        select3SortableOrder: function () {
            var $this = this.filter('[multiple]');

            $this.each(function () {
                var $select = $(this);

                // skip elements not select3-ed
                if (typeof ($select.data('select3')) !== 'object') {
                    return false;
                }

                var $select3 = $select.siblings('.select3-container');
                var sorted;

                // Opt group names
                var optArr = [];
                
                $select.find('optgroup').each(function(idx, val) {
                    optArr.push (val);
                });
                
                $select.find('option').each(function(idx, val) {
                    var groupName = $(this).parent('optgroup').prop('label');
                    var optVal = this;
                    
                    if (groupName === undefined) {
                        if (this.value !== '' && !this.selected) {
                            optArr.push (optVal);
                        }
                    }
                });
                
                sorted = $($select3.find('.select3-choices li[class!="select3-search-field"]').map(function () {
                    if (!this) {
                        return undefined;
                    }
                    
                    if($(this).data('select3Data') != undefined){
                        var id = $(this).data('select3Data').id;
                        return $select.find('option[value="' + id + '"]')[0];
                    }

                    
                    //var id = $(this).data('select3Data').id;

                    //return $select.find('option[value="' + id + '"]')[0];
                }));
 
                 sorted.push.apply(sorted, optArr);
                
                $select.children().remove();
                $select.append(sorted);
              });

            return $this;
        },
        
        select3Sortable: function () {
            var args = Array.prototype.slice.call(arguments, 0);
            $this = this.filter('[multiple]'),
                    validMethods = ['destroy'];

            if (args.length === 0 || typeof (args[0]) === 'object') {
                var defaultOptions = {
                    bindOrder: 'formSubmit', // or sortableStop
                    sortableOptions: {
                        placeholder: 'ui-state-highlight',
                        items: 'li:not(.select3-search-field)',
                        tolerance: 'pointer'
                    }
                };
                
                var options = $.extend(defaultOptions, args[0]);

                // Init select3 only if not already initialized to prevent select3 configuration loss
                if (typeof ($this.data('select3')) !== 'object') {
                    $this.select3();
                }

                $this.each(function () {
                    var $select = $(this)
                    var $select3choices = $select.siblings('.select3-container').find('.select3-choices');

                    // Init jQuery UI Sortable
                    $select3choices.sortable(options.sortableOptions);

                    switch (options.bindOrder) {
                        case 'sortableStop':
                            // apply options ordering in sortstop event
                            $select3choices.on("sortstop.select3sortable", function (event, ui) {
                                $select.select3SortableOrder();
                            });
                            
                            $select.on('change', function (e) {
                                $(this).select3SortableOrder();
                            });
                        break;
                        
                        default:
                            // apply options ordering in form submit
                            $select.closest('form').unbind('submit.select3sortable').on('submit.select3sortable', function () {
                                $select.select3SortableOrder();
                            });
                        break;
                    }
                });
            }
            else if (typeof (args[0] === 'string')) {
                if ($.inArray(args[0], validMethods) == -1) {
                    throw "Unknown method: " + args[0];
                }
                
                if (args[0] === 'destroy') {
                    $this.select3SortableDestroy();
                }
            }
            
            return $this;
        },
        
        select3SortableDestroy: function () {
            var $this = this.filter('[multiple]');
            $this.each(function () {
                var $select = $(this)
                var $select3choices = $select.parent().find('.select3-choices');

                // unbind form submit event
                $select.closest('form').unbind('submit.select3sortable');

                // unbind sortstop event
                $select3choices.unbind("sortstop.select3sortable");

                // destroy select3Sortable
                $select3choices.sortable('destroy');
            });
            
            return $this;
        }
    });
}(jQuery));