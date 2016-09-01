
function showEditButton() {
    var img = $('#leaflet-upload img');
        
    if (img.length && img.attr('src') != 'undefined') {
        if ($('#leaflet-upload img').attr('src').substr(0, 4) == 'blob') {
            img.after('<span>Reloading the page to edit key position</span>') ;
            location.reload() ;
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
$('#edit-coords').click(function () {
    editCoords();
});

showEditButton() ;
});

function editCoords () {
    var editor = new LeafletEditor () ;

    editor.setKeyCoords(key_xcoord,
                        key_ycoord,
                        key_box_width,
                        key_box_height
                        ) ;
    editor.setLogoCoords(logo_xcoord,
                         logo_ycoord,
                         logo_box_width,
                         logo_box_height
                        ) ;
    editor.setNameCoords(name_xcoord,
                         name_ycoord,
                         name_box_width,
                         name_box_height
                        ) ;
    editor.setHtmlTemplate('#ll-template') ;
    //editor.setImage('#) ;

    editor.startEditor() ;



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
        $('#ll-saving').text('Saving...');
        $('#ll-saving').show() ;

        $.post('/digitalproduct/leafletcoords?id='+model_id+'',
            {
                key: editor.keyPosition(),
                logo: editor.logoPosition(),
                name: editor.namePosition()
            })
            .done(function(response) {
                
                if (response == true) {
                    $('span', '#ll-saving').text('Success') ;
                    setTimeout(function () {
                        $('#leafletContainer').fadeOut('fast', function () {
                            $('#leafletContainer').remove() ;
                            location.reload() ;
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


}