"use strict" ;


/// RCH 20160216
// I'm not convinced this is being used... see code in leaflerutils.js?
function showEditButton() {
    var img = $('img', '#leaflet-upload');

    if (img.length && img.attr('src') != undefined) {
        if ($('#leaflet-upload img').attr('src').substr(0, 4) == 'blob') {
            img.after('<span>Please reload to edit key position</span>') ;
        } else {
            img.after('<button id="edit-coords">Edit Key Position</button>');

            $('#edit-coords').click(function () {
                editCoords();
            });
        }
    }
}

$(document).ready(function() {
// 210mm 297mm;
showEditButton() ;
});


function editCoords() {

    var item = $($('#ll-template').html()) ;
    $('#leafletFull img', item).attr('src', $('img', $('#leaflet-upload')).attr('src')) ;

    var dummyKey = $('#dummyKey', item) ;
    dummyKey.text('mock-123456-8907') ;

    $('body').append(item) ;
    item.fadeIn() ;

    /**
     * CLOSE
     * =====
     */
    $('#ll-close').click(function () {
        $('#leafletContainer').fadeOut('fast', function () {
            $('#leafletContainer').remove() ;
        }) ;
    }) ;

    /**
     * CLICK - SAVE
     * ============
     */
    $('#ll-save').click(function () {
        $('#ll-main').hide() ;
        $('span', '#ll-saving').text('Saving...');
        $('#ll-saving').show() ;
        $.post('/digitalproduct/leafletcoords?id=<?=  $model->id ?>',
            {
                xc: $('#ll-xc').text(),
                width: $('#ll-w').text(),
                yc: $('#ll-yc').text(),
                height: $('#ll-h').text()
            })
            .done(function(response) {
                if (response == true) {
                    $('span', '#ll-saving').text('Success') ;
                    setTimeout(function () {
                        $('#leafletContainer').fadeOut('fast', function () {
                            $('#leafletContainer').remove() ;
                        }) ;
                    }, 1500) ;
                } else {
                    $('span', '#ll-saving').text('Unable to record the co-ordinates') ;
                    setTimeout(function () {
                        $('#ll-saving').fadeOut('fast', function () {
                            $('#ll-main').fadeIn();
                        })
                    }, 1500) ;
                }
            }) ;
    }) ;

    /**
     * INITIALISE
     * ==========
     */
    setTimeout(function () {
        //var initTop  = <?= $model->productLeafletInfo->key_ycoord ? $model->productLeafletInfo->key_ycoord :  "$('#leafletFull').height()/2 - 12" ?> ;
        //var initLeft = <?= $model->productLeafletInfo->key_xcoord ? $model->productLeafletInfo->key_xcoord :  "$('#leafletFull').width()/2 - 135" ?> ;

        $('#leafletFull img').Jcrop({
            minSize     : [470, 44],
            setSelect   : [initLeft, initTop, initLeft+270, initTop+24],
//                onSelect    : setCoords,
            onChange    : moveKey
        }) ;
    }, 100) ;

    // ---------------------------------------------------------------------
    // SET COORDS
    // ==========
    // Records the x and y coordinates for the keys
    // ---------------------------------------------------------------------
    function setCoords(coords) {
        // coords.x, coords.y, coords.x2, coords.y2, coords.w, coords.h

        $('#ll-xc').text(coords.x) ;
        $('#ll-yc').text(coords.y) ;
        $('#ll-w').text(coords.w) ;
        $('#ll-h').text(coords.h) ;
    };

    // ---------------------------------------------------------------------
    // MOVE KEY
    // ========
    // This is used to reposition the dummy key inside the selected area.
    // There's nothing unique about the cropping are to link to it via jquery
    // so this allows the positioning inside it. Need to have a minimum
    // z-index of 600 for it to be visible.
    //
    // Now also records the x and y coordinates for the keys
    //
    // ---------------------------------------------------------------------
    function moveKey(coords) {
        dummyKey.css('left', coords.x).css('top', coords.y+20) ;

        $('#ll-xc').text(coords.x) ;
        $('#ll-yc').text(coords.y) ;
        $('#ll-w').text(coords.w) ;
        $('#ll-h').text(coords.h) ;
    }
}
