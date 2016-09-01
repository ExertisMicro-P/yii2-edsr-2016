//"use strict";

LeafletEditor = function () {
    var imageLoader = this;

    imageLoader.templateSelector = '#ll-template';
    imageLoader.includeLogo      = false;
    imageLoader.imageSelector    = null;
    imageLoader.positioningKey   = true;
    imageLoader.positioningName  = false;
    imageLoader.key              = {
        xCoord: 150,
        yCoord: 150,
        width : 300,
        height: 20
    };
    imageLoader.logo             = {
        xCoord: 150,
        yCoord: 150,
        width : 150,
        height: 150
    };
    imageLoader.name             = {
        xCoord: 150,
        yCoord: 150,
        width : 150,
        height: 150
    };
    imageLoader.dummyKey         = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF';
    imageLoader.jcrop            = null;

    /**
     * SET IMAGE
     * =========
     * This can be used to override the jQuery selector use to identify
     * which image is to have the keys overlaid on it,
     *
     * @param imageSelector
     */
    function setImage(imageSelector) {
        imageLoader.imageSelector = imageSelector;
    }

    function setTemplate(templateSelector) {
        imageLoader.templateSelector = templateSelector;
    }

    /**
     * SET KEY COORDS
     * ==============
     * @param xCoord
     * @param yCoord
     * @param {int} width description
     * @param {int} height description
     */
    function setKeyCoords(xCoord, yCoord, width, height) {
        imageLoader.key.xCoord = xCoord;
        imageLoader.key.yCoord = yCoord;
        imageLoader.key.width  = width;
        imageLoader.key.height = height;

    }

    /**
     * SET LOGO COORDS
     * ===============
     * @param xCoord
     * @param yCoord
     * @param {int} width description
     * @param {int} height description
     */
    function setLogoCoords(xCoord, yCoord, width, height) {
        if (xCoord || yCoord || width | height) {
            imageLoader.logo.xCoord = xCoord;
            imageLoader.logo.yCoord = yCoord;
            imageLoader.logo.width  = width;
            imageLoader.logo.height = height;
        }
    }

    /**
     * SET NAME COORDS
     * ===============
     * @param xCoord
     * @param yCoord
     * @param {int} width description
     * @param {int} height description
     */
    function setNameCoords(xCoord, yCoord, width, height) {
        if (xCoord || yCoord || width | height) {
            imageLoader.name.xCoord = xCoord;
            imageLoader.name.yCoord = yCoord;
            imageLoader.name.width  = width;
            imageLoader.name.height = height;
        }
    }

    function setPositioningKey(event) {
        imageLoader.jcrop.animateTo([
                parseInt(imageLoader.key.xCoord),
                parseInt(imageLoader.key.yCoord),
                parseInt(imageLoader.key.xCoord) + parseInt(imageLoader.key.width),
                parseInt(imageLoader.key.yCoord) + parseInt(imageLoader.key.height)]
        ) ;

        $('.jcrop-tracker:eq(0)')
            .addClass('key-marker')
            .removeClass('logo-marker name-marker')
            .attr('data-content', imageLoader.dummyKey);

        imageLoader.positioningKey = true;
        imageLoader.positioningName = false;
    }

    function setPositioningLogo(event) {

        imageLoader.jcrop.animateTo([
                parseInt(imageLoader.logo.xCoord),
                parseInt(imageLoader.logo.yCoord),
                parseInt(imageLoader.logo.xCoord) + parseInt(imageLoader.logo.width),
                parseInt(imageLoader.logo.yCoord) + parseInt(imageLoader.logo.height)]
        ) ;

        $('.jcrop-tracker:eq(0)')
            .removeClass('key-marker name-marker')
            .addClass('logo-marker')
            .attr('data-image', '../img/logo.png');
        imageLoader.positioningKey = false;
        imageLoader.positioningName = false;
    }

    function setPositioningName(event) {

        imageLoader.jcrop.animateTo([
                parseInt(imageLoader.name.xCoord),
                parseInt(imageLoader.name.yCoord),
                parseInt(imageLoader.name.xCoord) + parseInt(imageLoader.name.width),
                parseInt(imageLoader.name.yCoord) + parseInt(imageLoader.name.height)]
        ) ;

        $('.jcrop-tracker:eq(0)')
            .removeClass('key-marker logo-marker')
            .addClass('name-marker')
            .html('<h2>Product Name</h2>');
        imageLoader.positioningKey = false;
        imageLoader.positioningName = true;
    }

    function trackKey(coords) {
        if (imageLoader.positioningKey) {
            moveKey(coords);
        } else if(imageLoader.positioningName) {
            moveName(coords);
        } else if(!imageLoader.positioningName && !imageLoader.positioningName) {
            moveLogo(coords);
        }
    }

    /**
     * MOVE KEYS
     * =========
     * @param coords
     */
    function moveKey(coords) {
        //imageLoader.dummyKey.css('left', coords.x).css('top', coords.y + 20);

        imageLoader.key = {
            xCoord: coords.x.toFixed(0),
            yCoord: coords.y.toFixed(0),
            width : coords.w.toFixed(0),
            height: coords.h.toFixed(0)
        };

        $('#lk-xc').text(imageLoader.key.xCoord);
        $('#lk-yc').text(imageLoader.key.yCoord);
        $('#lk-w').text(imageLoader.key.width);
        $('#lk-h').text(imageLoader.key.height);
    }

    function moveLogo(coords) {
        //imageLoader.dummyKey.css('left', coords.x).css('top', coords.y + 20);

        imageLoader.logo = {
            xCoord: coords.x.toFixed(0),
            yCoord: coords.y.toFixed(0),
            width : coords.w.toFixed(0),
            height: coords.h.toFixed(0)
        };

        $('#ll-xc').text(imageLoader.logo.xCoord);
        $('#ll-yc').text(imageLoader.logo.yCoord);
        $('#ll-w').text(imageLoader.logo.width);
        $('#ll-h').text(imageLoader.logo.height);
    }

    function moveName(coords) {
        //imageLoader.dummyKey.css('left', coords.x).css('top', coords.y + 20);

        imageLoader.name = {
            xCoord: coords.x.toFixed(0),
            yCoord: coords.y.toFixed(0),
            width : coords.w.toFixed(0),
            height: coords.h.toFixed(0)
        };

        $('#ln-xc').text(imageLoader.name.xCoord);
        $('#ln-yc').text(imageLoader.name.yCoord);
        $('#ln-w').text(imageLoader.name.width);
        $('#ln-h').text(imageLoader.name.height);
    }

    function showEditorPage() {
        var item = $($(imageLoader.templateSelector).html());

        $('#leafletFull img', item).attr('src', $('img', $('#leaflet-upload')).attr('src'));

        //imageLoader.dummyKey = $('#dummyKey', item);
        //imageLoader.dummyKey.text('mock-123456-8907');

        $('body').append(item);
        item.fadeIn();
    }


    function startEditor() {
        showEditorPage();
        var initLeft   = imageLoader.key.xCoord ? imageLoader.key.xCoord : 0;
        var initTop    = imageLoader.key.yCoord ? imageLoader.key.yCoord : 0;
        var initWidth  = imageLoader.key.width ? imageLoader.key.width : 100;
        var initHeight = imageLoader.key.height ? imageLoader.key.height : 50;

        var container = $('#leafletContainer');

        //imageLoader.jcrop = $('#leafletFull img').Jcrop({
        imageLoader.jcrop = $.Jcrop('#leafletFull img', {


            boxWidth : container.width(),
            boxHeight: container.height() - $('#leafletFull').offset().top - 10,        // the 10 is jsut to give a margin at the bottom

            setSelect: [initLeft, initTop, initLeft + initWidth, initTop + initHeight],
            onChange : trackKey
        });
        $('#pos-key').click(setPositioningKey);
        $('#pos-logo').click(setPositioningLogo);
        $('#pos-name').click(setPositioningName);

        setPositioningKey() ;
    }


    /**
     * INIT
     * ====
     * This will instantiate the jsCrop object, using the image files passed
     * in. By default it will only create the placeholder for the licence key,
     * unless includeLogo is set to true
     *
     */
    function init() {

    }

    function getKeyPosition() {
        return imageLoader.key;
    }

    function getLogoPosition() {
        return imageLoader.logo;
    }

    function getNamePosition() {
        return imageLoader.name;
    }


    return {
        includeLogo    : imageLoader.includeLogo,
        setHtmlTemplate: setTemplate,
        setImage       : setImage,
        setKeyCoords   : setKeyCoords,
        setLogoCoords  : setLogoCoords,
        setNameCoords  : setNameCoords,
        startEditor    : startEditor,

        // -------------------------------------------------------------------
        // Have to return these via a call or get the wrong values.
        // -------------------------------------------------------------------
        keyPosition : getKeyPosition,
        logoPosition: getLogoPosition,
        namePosition: getNamePosition,

        init: init
    };

};
