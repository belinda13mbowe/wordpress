(function ($) {
    var draggable_list = [];


    // var counts = 0;
    // var drag_counter = $( ".drag_text" );
    // $( ".drag_block" ).draggable({
    //     drag: function() {
    //     var xPos = $(this).offset().left - $(this).parent().offset().left;
    //     var yPos = $(this).offset().top - $(this).parent().offset().top;

    //     updateCounterStatus( drag_counter, xPos, yPos );
    //   },
    //   containment: ".main_block"
    // });

    // function updateCounterStatus( $event_counter, xPos, yPos ) {    
    //     $event_counter.text( 'X: ' + xPos + ' Y:' + yPos );
    // }


    function definePos(el) {
        var x = el.offset().left - el.parent().offset().left;
        var y = el.offset().top - el.parent().offset().top;
        console.log('X: ' + x + ' Y:' + y);
    }

    // function getAll() {
    //     $('#get_block').on('click', function () {
    //         $.each(draggable_list, function (key, value) {
    //             alert(key + ": " + value.width);
    //         });
    //     });
    // }


    $.fn.parallaxScroll = function (options) {

        var defaults = {
            'drag_blocks_class': 'drag_block',
            'container_width': 700,
            'container_height': 500,
        }

        var item_index;

        var options_merge = $.extend({}, defaults, options);

        this.getOptions = function () {
            return options_merge;
        }

        this.editEl = function(item_id, data){
            $.each(data, function (key, value) {
                if(draggable_list[item_id - 1][key] != undefined)
                    draggable_list[item_id - 1][key] = value;
            });
        }

        this.createEl = function (x, y, width, height, src) {
            var el = {};
            el.sm_pos_x = x;
            el.sm_pos_y = y;
            el.sm_z_index = 0;
            el.sm_width = width;
            el.sm_height = height;
            el.sm_src = src;
            el.sm_when = 'enter';
            el.sm_from = 1;
            el.sm_to = 0;
            el.sm_easing = 'linear';
            el.sm_crop = 'true';
            el.sm_opacity = 1;
            el.sm_scalex = 1;
            el.sm_scaley = 1;
            el.sm_scalez = 1;
            el.sm_rotatex = 0;
            el.sm_rotatey = 0;
            el.sm_rotatez = 0;
            el.sm_translatex = 0;
            el.sm_translatey = 0;
            el.sm_translatez = 0;

            this.addEl(el);
        }

        this.addEl = function (item) {
            var item_id = item.item_id;
            if (item_id != undefined) {
                delete item['id'];
                delete item['post_id'];
                delete item['item_id'];
                draggable_list[item_id - 1] = item;
            } else {
                item_id = draggable_list.push(item);
            }

            var left = (item.sm_pos_x*$('#scroll_main_block').width())/100;
            var top = (item.sm_pos_y*$('#scroll_main_block').height())/100

            $('<img src="' + item.sm_src + '"  class="drag_block" id="sm_' + postID + '_' + item_id + '">').appendTo('#scroll_main_block')
                .css({'height': item.sm_height + 'px', 'width': item.sm_width + 'px', 'left': left, 'top': top, 'z-index': item.sm_z_index})
                .draggable({
                    containment: '#scroll_main_block'
                });
            console.log(draggable_list);
        }

        this.deleteEl = function (item_id) {
            draggable_list[item_id - 1] = null;

            $('#sm_' + postID + '_' + item_id).remove();

            console.log(draggable_list);
        }

        $(document).on('mousedown', '.' + options_merge.drag_blocks_class, function (e) {
            $('.' + options_merge.drag_blocks_class).each(function () {
                $(this).removeClass('selected');
            });
            $(this).addClass('selected');
            $('#edit-image-button').removeAttr("disabled");

            item_index = parseInt($(this).attr('id').split('_')[2]) - 1;
            $.each(draggable_list[item_index], function (index, value) {
                if (('#' + index).length > 0) {
                    $('#' + index).val(value);
                }
            });
        });

        $('#scroll_main_block').on('click', function (e) {
            console.log(e.target);
            if (!$(e.target).is('.' + options_merge.drag_blocks_class)) {
                $('.' + options_merge.drag_blocks_class).each(function () {
                    $(this).removeClass('selected');
                });
                $('#edit-image-button').attr("disabled", true);
                item_index = null;
            }
        });

        $('select[id^=sm_],input[id^=sm_]').on('change', function () {
            $('select[id^=sm_],input[id^=sm_]').each(function () {
                draggable_list[item_index][$(this).attr('id')] = this.value;
                if($(this).attr('id') == 'sm_z_index'){
                    console.log('azaza');
                    console.log($('#sm_' + postID + '_' + (item_index+1)));
                    $('#sm_' + postID + '_' + (item_index+1)).css('z-index', this.value);
                }
            });
        });

        this.setPostData = function () {
            $.each(draggable_list, function (index, item) {
                if (item != undefined) {
                    var width = $('#sm_' + postID + '_' + (index + 1)).width();
                    var height = $('#sm_' + postID + '_' + (index + 1)).height();
                    var left = $('#sm_' + postID + '_' + (index + 1)).position().left;
                    left = (left/$('#scroll_main_block').width())*100 > 100 ? 100 : (left/$('#scroll_main_block').width())*100;
                    var top = $('#sm_' + postID + '_' + (index + 1)).position().top;
                    top = (top/$('#scroll_main_block').height())*100 > 100 ? 100 : (top/$('#scroll_main_block').height())*100;
                    item.sm_pos_x = left;
                    item.sm_pos_y = top;
                    item.sm_width = width;
                    item.sm_height = height;
                }
            });

            var post_data = JSON.stringify(draggable_list);

            $('form#post').prepend('<input type="hidden" name="sm_post_data" value=\'' + post_data + '\'>');


        }


        // getAll();

        return this.each(function () {
            // $(options_merge.drag_blocks_class).draggable({
            //     drag: function () {
            //         definePos($(this));
            //     },
            //     containment: '#scroll_main_block'
            // });
            // $(options_merge.drag_blocks_class);
        });

        // if ( methods[method] ) {
        //   return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        // } else if ( typeof method === 'object' || ! method ) {
        //   return methods.init.apply( this, arguments );
        // } else {
        //   $.error( 'Метод с именем ' +  method + ' не существует для jQuery' );
        // }


    };
})(jQuery);


jQuery(document).ready(function ($) {

    //parallaxScroll init
    var parallaxScroll = $('.main_block').parallaxScroll({
        'drag_blocks_class': 'drag_block'
    });

    //load items
    if (items != undefined) {
        $.each(items, function (index, item) {
            parallaxScroll.addEl(item);
        });
    }

    var add_mediaUploader;
    var edit_mediaUploader;

    $('#add-button').on('click', function (e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
        if (add_mediaUploader) {
            add_mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        add_mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false
        });

        // When a file is selected, grab the URL and set it as the text field's value
        add_mediaUploader.on('select', function () {
            attachment = add_mediaUploader.state().get('selection').first().toJSON();
            console.log(attachment);
            parallaxScroll.createEl(0, 0, attachment.width, attachment.height, attachment.url);
            // $('#image-url').val(attachment.url);
        });
        // Open the uploader dialog
        add_mediaUploader.open();
    });

    $('#edit-image-button').on('click', function (e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (edit_mediaUploader) {
            edit_mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        edit_mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false
        });

        // When a file is selected, grab the URL and set it as the text field's value
        edit_mediaUploader.on('select', function () {
            attachment = edit_mediaUploader.state().get('selection').first().toJSON();
            $('.' + parallaxScroll.getOptions().drag_blocks_class + '.selected').attr('src', attachment.url);
            var item_id = $('.' + parallaxScroll.getOptions().drag_blocks_class + '.selected').attr('id').split('_')[2];
            parallaxScroll.editEl(item_id, { 'sm_src': attachment.url});
        });
        // Open the uploader dialog
        edit_mediaUploader.open();
    });

    $('#delete-button').on('click', function (e) {
        e.preventDefault();

        parallaxScroll.deleteEl($('.drag_block.selected').attr('id').split('_')[2]);
    });

    $('form#post').submit(function (e) {
        e.preventDefault();

        parallaxScroll.setPostData();

        this.submit();
    });

});