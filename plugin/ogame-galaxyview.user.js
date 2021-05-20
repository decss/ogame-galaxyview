// ==UserScript==
// @name        OGame GalaxView
// @namespace   https://github.com/decss
// @description OGame GalaxView - Galaxytool analog
// @author      decss
// @version     0.2
// @homepage    https://github.com/decss/ogame-galaxyview
// @updateURL   https://github.com/decss/ogame-galaxyview/raw/dev/plugin/ogame-galaxyview.user.js
// @include     *ogame.gameforge.com/game/*
// @grant       GM_xmlhttpRequest
// @require     https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js
// @license MIT
// ==/UserScript==


(function() {
    'use strict';

    const REFRESH = 200;
    // const API_URL = 'http://ogame.local/api';
    const API_URL = 'https://dev.soft-szn.ru/ogame/api';

    var turn = 0;

    // Main thread
    drawWidget();

    let timerId = setInterval(async function() {
        console.log('refresh ...');

        let page = getPage();
        let isChange = false;
        updateWidgetPage(page);

        if (page == 'galaxy') {

            if ($('#mobileDiv').length > 0 && $('#mobileDiv').attr('viewed') != 'yes') {
                $('#mobileDiv').attr('viewed', 'yes');
                isChange = true;
            }

            if (isChange) {
                console.log('... calling updateSystem request');
                await doRequest('updateSystem', $('#galaxycomponent').html());
            }

            if ($('input[name=autoscan]').is(':checked')) {
                autoscanGalaxy();
            }

        } else if (page == 'messages') {
            if ($('#fleetsgenericpage').length > 0 && $('#fleetsgenericpage').attr('viewed') != 'yes') {
                $('#fleetsgenericpage').attr('viewed', 'yes');
                isChange = true;
            }

            if (isChange) {
                console.log('... calling updateSystem request');
                await doRequest('updateMessages', $('#fleetsgenericpage').html());
            }
        }

        turn++;
    }, REFRESH);


    // Functions
    function autoscanGalaxy() {
        if (turn % 10 == 0) {
            let $gal = $('#galaxy_input').val();
            let $sys = $('#system_input').val();
            if ($sys >= 499) {
                submitOnKey(38);
            }
            submitOnKey(39);
        }
    }

    function getPage(page) {
        if (location.search.indexOf('component=galaxy') != -1) {
            return 'galaxy';
        } else if (location.search.indexOf('page=messages') != -1) {
            return 'messages';
        }
        return '';
    }

    function updateWidget(field, text, cls = 'gray') {
        let container = $('.ogv-foot .ogv-' + field + ' span');
        cls = cls ? 'ovg-' + cls : '';
        container.attr('class', cls);
        container.html(text);
    }

    function updateWidgetPage(page) {
        let cls = page ? 'white' : 'gray';
        let text = page ? page : 'unknown';
        updateWidget('page', text, cls)
    }



    async function doRequest(action, data) {
        console.log('doRequest() start');
        updateWidget('status', 'Sending data ...');

        GM_xmlhttpRequest({
            method: "POST",
            url: API_URL + '/' + action,
            data: 'data=' + encodeURIComponent(data),
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            timeout: 1500,
            onload: function(response) {
                if (response) {
                    let resp = jQuery.parseJSON(response.responseText.trim());
                    if (typeof resp == 'object') {
                        updateWidget('status', resp.message, resp.status);
                        updateWidget('data', resp.data);
                        return true;
                    }
                }

                updateWidget('status', 'Response has errors', 'error');
            },
            onabort: function() {updateWidget('status', 'Request Aborted', 'error');},
            onerror: function() {updateWidget('status', 'Request Error', 'error');},
            ontimeout: function() {updateWidget('status', 'Request Timeout', 'error');}
        });
        console.log('doRequest() end');
    }

    function drawWidget() {
        let html = `<style type="text/css">
            .ogv-widget {width:280px; max-height:90%; overflow-y:scroll; position:fixed; left:10px; top:30px; border:1px solid #333; padding:2px 5px; font-size:10px; line-height:16px; background:#000; color:#333;}
            .ogv-controls {font-size:12px; margin:4px 0 2px 0; color:#7F7F7F}
            .ogv-controls label {cursor:pointer;}
            .ovg-success {color:#4CFF00;}
            .ovg-error {color:#FF4C00; font-weight:bold;}
            .ovg-white {color:#FFFFFF;}
            .ovg-gray {color:#7F7F7F;}
        </style>
        <div class="ogv-widget">
            <div class="ogv-controls">
                <label><input type="checkbox" name="autoscan"> Autoscan</label>
            </div>
            <div class="ogv-foot">
                <div class="ogv-page">Page: <span>-</span></div>
                <div class="ogv-status">Result: <span>-</span></div>
                <div class="ogv-data"><pre><span></span></pre></div>
            </div>
        </div>`;
        $(html).appendTo('body');
    }

})();
