(function($){
    'use strict';

    redux.field_objects                     = redux.field_objects || {};
    redux.field_objects.color_rgba          = redux.field_objects.color_rgba || {};
    redux.field_objects.color_rgba.fieldID  = '';

    redux.field_objects.color_rgba.hexToRGBA = function( hex, alpha ) {
        var result;
        console.log('hex:',hex);
        if (hex === null) {
            result = '';
        } else {
            hex = hex.replace('#', '');
            var r = parseInt(hex.substring(0, 2), 16);
            var g = parseInt(hex.substring(2, 4), 16);
            var b = parseInt(hex.substring(4, 6), 16);

            result = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
        }
        
        return result;        
    };

    redux.field_objects.color_rgba.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-color_rgba:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;

                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }

                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }

                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                redux.field_objects.color_rgba.modInit(el);
                redux.field_objects.color_rgba.initColorPicker(el);
            });
    };

    redux.field_objects.color_rgba.modInit = function(el) {

        redux.field_objects.color_rgba.fieldID    = el.find('.redux-color_rgba-container').data('id');

    };

    // Initialize colour picker
    redux.field_objects.color_rgba.initColorPicker = function(el){
        el.find( '.redux-color-init' ).wpColorPicker(
        {
            change: function( e, ui ) {
                var color = ui.color;
                var colorVal, alphaVal, rgbaVal;
                redux_change( $( this ) );
                if (color === null) {
                    if (outputTransparent === true) {
                        colorVal = 'transparent';
                    } else {
                        colorVal = null;
                    }
                    alphaVal = 1;
                } else {
                    colorVal = Color(color.toRgb()).toString();
                    alphaVal = color._alpha;
                }

                if (colorVal != 'transparent') {
                    rgbaVal     = redux.field_objects.color_rgba.hexToRGBA(colorVal, alphaVal);
                } else {
                    rgbaVal     = 'transparent';
                }

                var blockID = $(this).data('block-id');

                // Update HTML color value
                el.find('input#' + blockID + '-color').val(colorVal);

                // Update HTML alpha value
                el.find('input#' + blockID + '-alpha').val(alphaVal);

                // Update RGBA alpha value
                el.find('input#' + blockID + '-rgba').val(rgbaVal);
                redux_change(el.find('.redux-color-rgba-container'));
            },
            clear: function( e, ui ) {
                $( this ).val( '' );
                redux_change( $( this ).parent().find( '.redux-color-init' ) );
            }
        }
        );
};
})(jQuery);