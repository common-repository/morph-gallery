jQuery(document).ready(function($) {

    var $itemsMetaBox = $('#morph-gallery-items-box'),
        $itemsArea = $('#morph-area-items');

    function _getItemJson( $item ){
        return JSON.parse($item.find('.morph-item-json').val());
    }

    function _setItemJson( $item, json ){
        $item.find('.morph-item-json').val( JSON.stringify( json ) );
    }

    function _imageLoaded($img){
        $img.show();
    }

    /* Convert px to % */
    function pxToRel(imageLength, playPenLength){
        return 100 * ( imageLength / playPenLength );
    }

    function calcImageSize(w, h, newW, newH, mode, unitW, unitH, playPenW, playPenH){
        var ratio,
            resizeW,
            resizeH;

        w = parseInt(w);
        h = parseInt(h);
        newW = parseInt(newW);
        newH = parseInt(newH);
        resizeW = newW;
        resizeH = newH;
        ratio = w / h;

        if(mode == 'exactWidth'){

            resizeH = Math.round(newW / ratio);

            if(unitW == '%'){
                resizeW = pxToRel(resizeW, playPenW);
            }
            if(unitH == '%'){
                resizeH = pxToRel(resizeH, playPenH);
            }

        } else if( mode == 'fit'){

            // Try basing it on width first
            resizeW = newW;
            resizeH = Math.round(newW / ratio);

            if( ( resizeW > newW ) || ( resizeH > newH ) ){ // Oops, either with or height does not fit
                // So base on height instead
                resizeH = newH;
                resizeW = newH * ratio;
            }

            if(unitW == '%'){
                resizeW = pxToRel(resizeW, playPenW);
            }
            if(unitH == '%'){
                resizeH = pxToRel(resizeH, playPenH);
            }

        }

        return [Math.round(resizeW), Math.round(resizeH)];
    }

    /*** Init - Sortable items ***/
    $itemsArea.sortable({
        placeholder: "morph-item-placeholder",
        forcePlaceholderSize:true,
        delay: 200,
        /*** Update form field indexes when slide order changes ***/
        update: function(event, ui) {
            $itemsArea.find('.morph-item').each(function(boxIndex, box){ /*** Loop thru each box ***/
                $(box).find('input, select, textarea').each(function(i, field){ /*** Loop thru relevant form fields ***/
                    var name = $(field).attr('name');
                    if(name){
                        name = name.replace(/\[[0-9]+\]/, '['+boxIndex+']'); /*** Replace all [index] in field_key[index][name] ***/
                        $(field).attr('name',name);
                    }
                });
            });
        }
    });

    /*** Add multiple images ***/
    $itemsMetaBox.on('selectItem.morph', '.morph-item', function(){
        var $item = $(this),
            src = $item.find('img').attr('src'),
            $preview = $('#morph-preview'),
            $previewImage = $preview.find('img'),
            jsonObj = _getItemJson($item);

        console.log(jsonObj);
        $item.addClass('morph-item-selected');

        /* Update editor fields with values from current item */
        $('.morph-editor-field').each(function(i, editField){
            var $editField = $(editField),
                name = $editField.attr('name'); /*  Editor field name attribute */
            $editField.val( jsonObj[name] ); /* Pull info from json data of current item */
        });

        if($previewImage.length > 0) {
            $previewImage.attr('src', src).show();
        } else {
            $previewImage = $('<img src="' + src + '" />').prependTo($preview).attr('src', src);
            $previewImage.on('load', function(e) {

                _imageLoaded($(this));

            });
        }

    }).on('deselectItem.morph', '.morph-item', function(){
        var $item = $(this),
            $preview = $('#morph-preview'),
            $previewImage = $preview.find('img');


        $item.removeClass('morph-item-selected');
        $previewImage.attr('src', '').hide();

    }).on('click', '.morph-item', function(e){ /* Item click */

        var $item = $(this),
            $selectedItem = $(this),
            $prevSelectedItem = $itemsMetaBox.find('.morph-item-selected'),
            src = $item.find('img').attr('src'),
            $preview = $('#morph-preview');

        if($prevSelectedItem.length > 0){
            $('.morph-editor-field').each(function(i, editField){
                var $editField = $(editField);

                $prevSelectedItem.trigger('updateItemField.morph', [$editField.val(), $editField, $prevSelectedItem, $selectedItem]);
            });
        }

        $itemsMetaBox.find('.morph-item').not(this).removeClass('morph-item-selected');

        if( $item.hasClass('morph-item-selected') ){
            /* De-select item */
            $item.trigger('deselectItem.morph');
        } else {
            /* Select item */
            $item.trigger('selectItem.morph');
        }

        if( $itemsMetaBox.find('.morph-item-selected').length > 0 ){
            $('#morph-gallery-items-box').addClass('morph-gallery-items-selected');
        } else {
            $itemsMetaBox.removeClass('morph-gallery-items-selected');
        }


    }).on('click', '.morph-remove-item', function(e) {
        /* Remove item */
        var selected = $('#morph-gallery-items-box').find('.morph-item-selected');
        selected.fadeOut('slow', function(){ selected.remove()});

        e.preventDefault();
        e.stopPropagation();
    }).on('blur', '.morph-editor-field', function(e) {

        var $selected = $('#morph-gallery-items-box').find('.morph-item-selected'),
            $me = $(this),
            val = $me.val(),
            name = $me.attr('name'),
            jsonObj = _getItemJson($selected);

        if($selected.length>0){
            jsonObj[name] = val;
            _setItemJson($selected, jsonObj);
        }

        e.preventDefault();
        e.stopPropagation();
    }).on('updateItemField.morph', '.morph-item', function(e, value, $editField, $prevItem, $item ) {

        var jsonObj = _getItemJson( $prevItem),
            name = $editField.attr('name');

        jsonObj[ name ] = value;

        _setItemJson( $prevItem, jsonObj );

        e.preventDefault();
        e.stopPropagation();
    });

    /*** Templates ***/
    $('#morph-gallery-templates').on('focus', '.morph-template-list input', function(e){

        $('.morph-template').removeClass('morph-template-chosen');
        $(this).parents('.morph-template').addClass('morph-template-chosen');

    }).on('click', '.morph-template-settings-show', function(e){
        e.preventDefault();

        var template_name = $(this).data('morph-template-name');

        $('#morph-gallery-templates').find('.morph-dialog').removeClass('morph-show');
        $('#morph-modal,#morph-template-settings-'+template_name).addClass('morph-show');
    });

    /* Add multiple items */
    $('#morph-multiple-items').data('frameSettings', {
        className: 'media-frame',
        frame: 'select',
        multiple: true,
        title: 'Select Images - Use Ctrl + Click or Shift + Click',
        library: {
            type: 'image'
        },
        button: {
            text: 'Add Items'
        },
        morphCallback: function($sourceElement, media_attachments){

            var template = $('#morph-skeleton-item').html(),
                html = '',
                items_count = $('.morph-item').length,
                jsonObj = {};

            for (i = 0; i < media_attachments.length; ++i) {

                html = template;
                html = html.replace(/(\#src)/g, media_attachments[i].url);
                html = html.replace(/(\{index\})/g, i + items_count);

                jsonObj = {
                    id: media_attachments[i].id,
                    alt: media_attachments[i].alt,
                    title: media_attachments[i].title,
                    name: media_attachments[i].name
                };
                /* Replace with single quotes for json to work */
                html = html.replace('value="{}"', "value='{}'");
                html = html.replace(/(\{\})/g, JSON.stringify(jsonObj));

                $itemsArea.append(html);
            }

        }
    });

    /* Update item image */
    $('#morph-update-item').data('frameSettings', {
        className: 'media-frame',
        frame: 'select',
        multiple: false,
        title: 'Update Image',
        library: {
            type: 'image'
        },
        button: {
            text: 'Choose'
        },
        morphCallback: function($sourceElement, media_attachments){
            var $item = $itemsArea.find('.morph-item-selected'),
                $img = $item.find('img'),
                jsonObj = _getItemJson( $item),
                src = media_attachments[0].sizes.medium.url,
                $preview = $('#morph-preview'),
                $previewImage = $preview.find('img');
            //console.log(jsonObj);
            $img.attr('src', src);
            $previewImage.attr('src', src);

            jsonObj.id = media_attachments[0].id;
            jsonObj.src = src;

            //console.log(media_attachments);
            //console.log(jsonObj);

            _setItemJson( $item, jsonObj );

        }
    });

    /* Watermark add */
    $('#morph-watermark-add').data('frameSettings', {
        className: 'media-frame',
        frame: 'select',
        multiple: false,
        title: 'Watermark Image',
        library: {
            type: 'image'
        },
        button: {
            text: 'Choose'
        },
        morphCallback: function($sourceElement, media_attachments){
            var $hidden = $('#field-hidden-watermark'),
                $playPen = $('#watermark-playpen'),
                $img = $playPen.find('img'),
                thumbnail = media_attachments[0].url,
                id = media_attachments[0].id,
                width = media_attachments[0].width,
                height = media_attachments[0].height;

            if($img.length > 0) {
                $img.attr('src', thumbnail);
                $img.attr('data-width', width);
                $img.attr('data-height', height);
            } else {
                $('<img src="' + thumbnail + '" data-width="'+width+'" data-height="'+height+'" />').prependTo($playPen).animate({maxWidth:'100%'}, 500);
            }
            $hidden.val(id);

            $('#field-watermark-width,#field-watermark-height').trigger('change');

        }
    });

    /* Media frame */
    (function () {
        if (typeof(wp) == "undefined" || typeof(wp.media) != "function") {
            return;
        }
        // Prepare the variable that holds our custom media manager.
        var morphMediaFrame;
        var $sourceElement = null;

        // Bind to our click event in order to open up the new media experience.
        $(document).on('click', '.morph-show-media', function (e) {
            // Prevent the default action from occuring.
            e.preventDefault();

            $sourceElement = jQuery(this);

            var frameSettings = $sourceElement.data('frameSettings');

            morphMediaFrame = wp.media.frames.morphMediaFrame = wp.media({
                className: 'media-frame',
                frame: frameSettings.frame,
                multiple: frameSettings.multiple,
                title: frameSettings.title,
                library: {
                    type: 'image'
                },
                button: {
                    text: frameSettings.button.text
                }
            });

            morphMediaFrame.on('select', function () {

                // Grab our attachment selection and construct a JSON representation of the model.
                var media_attachments = morphMediaFrame.state().get('selection').toJSON();
                    //frameSettings = $sourceElement.data('frameSettings');

                frameSettings.morphCallback($sourceElement, media_attachments);


            });

            // Now that everything has been set, let's open up the frame.
            morphMediaFrame.open();
        });
    })();

    /* Watermark */
    $('#morph-gallery-watermark').on('click', '#morph-watermark-remove', function(e){
        var $hidden = $('#field-hidden-watermark'),
            $playPen = $('#watermark-playpen'),
            $img = $playPen.find('img');

        if($img.length > 0) {
            $img.remove();
            $hidden.val(0);
        }
        e.preventDefault();

    }).on('keyup change', '#field-watermark-width, #field-watermark-height', function(e){
        var $playPen = $('#watermark-playpen'),
            $img = $playPen.find('img'),
            $width = $('#field-watermark-width'),
            $widthUnit = $('#field-watermark-width-unit'),
            $height = $('#field-watermark-height'),
            $heightUnit = $('#field-watermark-height-unit'),
            $mode = $('#field-watermark-mode'),
            width = $width.val(),
            height = $height.val(),
            unitW = $widthUnit.val(),
            unitH = $heightUnit.val(),
            mode = $mode.val(),
            newSize,
            origW = $img.data('width'),
            origH = $img.data('height');

        if(unitW == '%'){
            width = (width / 100) * $playPen.width();
        }
        if(unitH == '%'){
            height = (height / 100) * $playPen.height();
        }
        newSize = calcImageSize(origW, origH, width, height, mode, unitW, unitH, $playPen.width(), $playPen.height());


        if(unitW == '%'){
            $img.css({
                'width': newSize[0]+'%'
            });
        } else {
            $img.css({
                'width': newSize[0]+'px'
            });
        }

        if(unitH == '%'){
            $img.css({
                'height': newSize[1]+'%'
            });
        } else {
            $img.css({
                'height': newSize[1]+'px'
            });
        }

    }).on('change', '#field-watermark-width-unit', function() {
        $('#field-watermark-width').trigger('change');
    }).on('change', '#field-watermark-height-unit', function() {
        $('#field-watermark-height').trigger('change');
    }).on('change', '#field-watermark-mode', function() {
        $('#field-watermark-width').trigger('change');
        if($(this).val() == 'exactWidth'){
            $('#field-watermark-height,#field-watermark-height-unit').prop('disabled','disabled');
        } else {
            $('#field-watermark-height,#field-watermark-height-unit').removeProp('disabled');
        }


    }).on('click', '.position-pen button', function(e){

        var data = $(this).data('pos'),
            split = data.split('-'),
            $playPen = $('#watermark-playpen'),
            $img = $playPen.find('img'),
            x = split[1],
            y = split[0];

        $('#field-hidden-watermark-x').val(x);
        $('#field-hidden-watermark-y').val(y);

        if(x == 'left'){
            $img.css({
                'left': 0,
                'right':'auto',
                'marginLeft': 0
            });
        } else if(x == 'right'){
            $img.css({
                'left': 'auto',
                'right': 0,
                'marginLeft': 0
            });
        } else { /* assume center */
            $img.css({
                'left': '50%',
                'right':'auto',
                'marginLeft': (Math.round($img.width()/2)*-1) + 'px' /* negative px. Eg: -10px */
            });
        }

        if(y == 'top'){
            $img.css({
                'top': 0,
                'bottom':'auto',
                'marginTop': 0
            });
        } else if( y == 'bottom'){
            $img.css({
                'top':'auto',
                'bottom': 0,
                'marginTop': 0
            });
        } else { /* assume center */
            $img.css({
                'top': '50%',
                'bottom':'auto',
                'marginTop': (Math.round($img.height()/2)*-1) + 'px' /* negative px. Eg: -10px */
            });
        }

        $(this).addClass('active').siblings().removeClass('active');
    });

    $('#field-watermark-width,#field-watermark-mode').trigger('change');

    $('#morph-gallery-publish').on('click', '#morph-regen', function(e){

        var button = $(this),
            items = [],
            data = {},
            per_batch = 5,
            batches = [],
            batch_count = 0;

        // Put items ID in an array
        $itemsArea.find('.morph-item').each(function(i, el){
           items[i] = $(el).find('.morph-item-id').val();
        });

        // Sanity checks!
        if(items.length <= 0){
            alert('No items to process');
            return false;
        }

        if(per_batch <= 0){
            alert('Items per batch must be > 0');
            return false;
        }

        // Create items ID batches
        batches = create_batches(items, per_batch, items.length);

        // Default data
        data = {
            'action':'morph_regen_thumbs',
            'nonce':morph_admin_vars.nonce,
            'gallery_id': button.data('morph-gallery-id'),
            'template_name': button.data('morph-gallery-template'),
            'batch': []
        };

        // Update UI
        $('.morph-publish-buttons').children('input').prop('disabled', true);
        $('.morph-publish-buttons').children('a').hide();
        $('.morph-resize-progress').show();

        //console.table(items);
        //console.table(batches);
        resize_batch( batches[batch_count] );

        function resize_batch( batch ){

            data.batch = batch;

            $.post(
                ajaxurl, // Automatically added by WordPress in wp-admin
                data,
                null,
                'json'
            ).done(function (result) {
                var progress = Math.ceil(100 * ((batch_count+1) / batches.length));

                if(progress < 0 ){
                    progress = 0;
                }

                if(progress > 100 ){
                    progress = 100;
                }
                //console.log(result);


                //console.log(batch_count, batches.length,  progress);

                $('.morph-resize-progress-bar>div').css('width', progress+'%' );
                $('.morph-resize-label').html(progress+'%');
                batch_count++;

                if(batch_count < batches.length) {
                    resize_batch( batches[batch_count] );
                } else {
                    //$('.morph-publish-buttons').children('input').prop('disabled', false);
                    //$('.morph-publish-buttons').children('a').show();
                    $('.morph-resize-progress').hide();
                    $('.morph-resize-label').html('Processing...');
                    $('form#post').submit();
                }
                //dialog.find('.morph-dialog-close').trigger('click');
            }).fail(function(result){
                alert('Request failed');
                //console.log(result);
            }).always(function(){
                //button.attr('value', 'Save').prop('disabled', false);
                //dialog.find('input[type="text"],input[type="number"]').prop('readonly', false);
            });
        }

        function create_batches(items, per_batch, total_items){
            var max_batch = Math.floor(total_items / per_batch),
                remainder = total_items % per_batch,
                start = 0,
                end = 0,
                batch_count = 1,
                batches = [];

            // Sanity checks!
            if(per_batch > total_items){
                max_batch = 1;
                remainder = 0;
            }

            for(batch_count; batch_count <= max_batch; batch_count++ ){

                start = per_batch * (batch_count-1);
                end = start + (per_batch-1);
                batches[batch_count-1] = items.slice(start, end+1); // Array.prototype.slice(start, length)
            }


            if( remainder > 0 ){
                start = end + 1;
                end = end + remainder;
                batches[max_batch] = items.slice(start, end+1); // Array.prototype.slice(start, length)
            }

            return batches;
        }
    });

    $('#morph-skeleton-template-settings').on('click', '.morph-dialog-close', function(e) {
        e.preventDefault();
        $(this).parents('.morph-dialog').removeClass('morph-show');
        $('#morph-modal').removeClass('morph-show');
    });

    $('input#publish').click(function(e){
        //e.preventDefault();
    });

    /*** Export ***/
    $('#morph-select-all').click(function(){
        if( $(this).is(':checked') ) {
            $('.morph-gallery').prop('checked', true);
        } else {
            $('.morph-gallery').prop('checked', false);
        }

    });

    $('.morph-logs').on('click', 'button', function(){
        $(this).parent().parent().toggleClass('expand')
    });
});