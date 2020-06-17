/**
 * Created by administrator on 2016-03-16.
 */

var DAY_STAMP = 1000*60*60*24;
var WEEK_STAMP = DAY_STAMP*7;
var isLoading = false;
var slidingUpSpeed = 350;//'fast';
var slidingDownSpeed = 800;//'slow';
var isEditing = false;
var isLoadingUp = true;
var urlOptions = {};
var isFirstLoading = true;

var overlayLoadingCount = 0;

var toggleAsked = false;
/**
 * Used to get POST/GET values
 * (A php function print them in the html page)
 * @param varName
 * @returns {string|bool}
 * (when false, show modal to inform user that an error occurred)
 */
function get(varName){
    var data = $('#'+varName).val();
    if(data){
        return data;
    }
    invalid();
    return false;
}

/**
 *
 * @param url : string like "php/[MODULE]/page.php"
 * @param data : object like { name: "John", location: "Boston" }
 * @param dataType : 'html','json',...
 * @param funcDone : function to exec on success
 * @param funcFail : function to exec on fail
 */
function callAjax(url, data, dataType, funcDone, funcFail){
    funcDone = typeof funcDone !== 'undefined' ? funcDone : AJAXdone;
    funcFail = typeof funcFail !== 'undefined' ? funcFail : AJAXfail;
    if(!isLoading && funcDone != reload) {
        startLoading();
    }
    $.ajax({
            method: 'POST',
            url: url,
            dataType: dataType,
            data: data //{ name: "John", location: "Boston" }
        }).done(function( data, textStatus, jqXHR){
            stopLoading();
            if(typeof funcDone == 'function'){
                funcDone(data, textStatus, jqXHR);
            }
        }).fail(function(jqXHR, textStatus, error){
            stopLoading();
            AJAXfail(jqXHR, textStatus, error);
            if(typeof funcFail == 'function'){
                funcFail();
            }
        });
}

function setOptions(options){
    urlOptions = options;
}

function addOption(tag, value){
    urlOptions[tag] = value;
}

function clearOptions(){
    setOptions({});
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + (value ? "=" + value : '') + '$2');
    }
    else {
        return uri + separator + key + (value ? "=" + value : '');
    }
}

function getUrlWithOptions(url){
    var actualUrl = url ? url : location.href;
    for (var k in urlOptions){
        if (urlOptions.hasOwnProperty(k)) {
            actualUrl = updateQueryStringParameter(actualUrl, k, urlOptions[k]);
        }
    }
    return actualUrl;
}
/**
 * Default function called if there is no success function in callAjax
 * @param data : object received data from ajax call
 * @param textStatus : string
 * @param jqXHR : object
 * @constructor
 */
function AJAXdone( data, textStatus, jqXHR ){
    console.log( "Data from Ajax: " + data );
    console.log( "textStatus from Ajax: " + textStatus );
    console.log( "jqXHR from Ajax: " + jqXHR );
}
/**
 *
 * @param jqXHR
 * @param textStatus
 * @param error
 * @constructor
 */
function AJAXfail(jqXHR, textStatus, error ) {
    isLoading = false;
    isLoadingUp = true;
    var signifientError = jqXHR['responseText'];
    switch (jqXHR['status']){
        case 604:
            //nothing and it's normal!
            break;
        case 603:
            //redirect('members.php');
            showInfo('Attention!', signifientError, login);
            break;
        case 602:
            showInfo('Attention!', signifientError);
            break;
        case 601:
            showInfo('Erreur', 'L\'une des données saisies n\'est pas valide... veuillez vérifier vos données.');
            break;
        case 500:
            showInfo('Erreur', 'Une erreur est survenue lors votre dernière action...' + "\r\n" + signifientError);
            break;
        case 404:
            showInfo('Page introuvable', 'La page que vous tentez accéder n\'existe pas...');
            slideInSidebar();
            break;
    }
    var err=[];
    err.status = textStatus;
    err.error = error;
    console.log( "Request AJAX Failed: ");
    console.log(err);
    toggleLoadingDown();
}

function invalid(message){
    showInfo('Erreur', message ? message : 'Une erreur est survenu... veuillez vérifier vos informations!');
}
/**
 *  It smoothly replace content of selected element and scroll the page to get it in view
 * @param id
 * @param html : content to set in id's element
 * @param callback : function OPTIONAL
 */
function replaceContentOf(id, html, callback){
    $('#'+id).setHtml(html, 500, function(){
        $.when(initForms()).done(callback).done(function(){
            var content = $('#content');
            //scroll to show element at the top of view
            content.animate({scrollTop:$("#"+id).offset().top - ($('.toolbar').outerHeight()*2) + content.scrollTop()}, '1000', 'swing', function() {
            });
        });
    });
}
/**
 * Make an element to fade out [then call callback]
 * @param itemId
 * @param callback : function OPTIONAL
 */
function hideItem(itemId, callback){
    var actualSelector = $('#'+itemId);
    actualSelector.setHtml('', 500, function(){
        actualSelector.fadeOut(1000, function(){
            if(actualSelector.parent().children(':visible').length == 0) {
                if(typeof main === 'function'){
                    //reload??
                }
            }else{
                if(typeof callback === 'function'){
                    callback();
                }
            }
        });
    });
}

/**
 * Replace all main content by given html with an animation
 * @param html string (data from ajax calls)
 * @param callback : function to exec after changing main content
 */
function reload(html, callback){
    if(!isLoading) {
        isLoading = true;
        toggleLoadingUp(function(){
            $('.innerContent').setPopHtml(html, function(){
                updateHistory();
                toggleLoadingDown(function(){
                    if(typeof callback === "function"){
                        callback();
                    }
                });
            });
        });
    }
}
/**
 * Replace main content without animation
 * @param html
 */
function setContent(html){
    $.when($('.innerContent').html(html)).done(function(){
        initForms();
    });
}

function setAutocompleteContent(html, $autocompletId){
    var value = $('#'+$autocompletId).val();
    $.when($('.innerContent').html(html)).done(function(){
        initForms();
        $('#'+$autocompletId).val(value);
        $('#'+$autocompletId).focus();
    });
}

function refresh(){
    redirect(getUrlWithOptions());
}
/**
 * Main function to have nice transitions all over the website
 * This is used to switch between all different main pages/modules
 * without being flashy
 *
 * This is mainly used by the sidebar and header Menu
 * @param url : String Url to open after some animations
 * @param module : String Module name to be open directly on loading
 * @param data : object parameters to pass on the ajax call if needed
 */
function redirect(url, module, data){
    if(module){
        addOption('m', module);
        url = getUrlWithOptions(url);
    }
    clearOptions();
    data = data ? data : {};
    if (!isLoading) {
        isLoading = true;
        UrlExists(url, function(){
            slideOutSidebar();
            toggleLoadingUp(function () {
                callAjax(url,data,'html',function(data){
                    changePageContent(data, url);
                });
            });
        }, function(){
            showInfo('Erreur','Il semble que ce lien ne soit pas bien configuré! Veuillez communiquer ' +
                'avec les responsables de ce site web afin qu\'ils puissent le régler le plus rapidement possible!' +
                "\r\n" + 'Merci de votre compréhension');
        });
    }
}

/**
 * Manually replace content of the page by the content called in ajax
 * looks like it loads a new page.
 * @param data
 * @param url
 */
function changePageContent(data, url){
    updateHeader(url);
    $('.underHeader').html($(data).filter('.underHeader').html()).promise().done(function(){
        document.title = $(data).filter('title').text();
        updateHistory(url);
        slideInSidebar();
    });
}

function updateHeader(url){
    if(!url){
        url = getUrlWithOptions();
    }
    isLoading=true;
    callAjax('php/site/header.php',{url:url},'html',function(html){
        $('.header').html($(html).filter('.header').html());
    });
    isLoading=false;
}

function updateHistory(url){
    var url= getUrlWithOptions(url);
    console.log('setting history for: ' + url);
    var actualSidebar = sidebar = $(".sidebar .menu");
    html = $('.underHeader').html();
    var temp = $("<div>" + html + "</div>"); //encapsulate for JQUERY manipulations...
    temp.find(".sidebar .menu").css("left", "-" + actualSidebar.outerWidth() + "px");
    //Atempt to delete the possible "height:1px" that toggleLoadingDown generate... but this doesnt work for now...
    temp.find(".innerContent").css('height', null);
    html = temp.html();
    window.history.pushState({"content":html,"pageTitle":document.title, "url":url},"", url);
    if(toggleAsked){
        toggleAsked = false;
        toggleLoadingDown();
    }
}
/**
 * Help detecting back/forward button navigation
 * @param e
 */
window.onpopstate = function(e){
    if(e.state){
        if(!isLoading) {
            isLoading = true;
            slideOutSidebar();
            toggleLoadingUp(function () {
                setHistoryContent(e.state.content);
            });
        }else{
            setHistoryContent(e.state.content);
        }
    }else{
        window.history.back();
    }
};

function setHistoryContent(content){
    $('.underHeader').setPopHtml(content, function () {
        toggleLoadingDown(null);
        slideInSidebar();
        updateHeader();
    });
}

/**
 * Check if this is a valid link before trying to load it in browser
 * @param url : string
 * @param success
 * @param failed
 * @returns {boolean}
 * @constructor
 */
function UrlExists(url, success, failed) {
    if(url != '#') {
        callAjax(url,{},'html',success,function(){
            isLoading = false;
            failed();
        });
    }else{
        isLoading = false;
        failed();
    }
}

/**
 * Highlight the requested Option in the sidebar
 * @param id : Number|String of the Option
 */
function selectOption(id){
    if(!isLoading || $(".sidebar .menu").css("left") != '0px') {
        var sidebar = $('.sidebar');
        var content = sidebar.children();
        selector = null;
        if (typeof id === "string") {
            selector = $('a:contains(' + id + ')');
        } else {
            id = id ? id : 1;
            selector = $('.sidebar #Option' + id);
        }
        sidebar.find('.active').removeClass('active');
        selector.addClass('active');
        if(selector.offset()){
            //scroll to show element at the top of view
            content.animate({scrollTop: selector.offset().top - $('#header').outerHeight() - (sidebar.find('.active').outerHeight()) + content.scrollTop()}, '1000', 'swing');
        }else {
            console.log('COULD NOT SELECT OPTION :' + id);
        }
    }
}

function toggleLoadingUp(callback){
    console.log('TOGGLE UP');
    if(!isLoadingUp) {
        isLoadingUp = true;
        $('.content').css("overflow", 'hidden');
        $('.content').slideUp(slidingUpSpeed, null);
    }
    $('.loading').slideDown(slidingUpSpeed, callback);
    //if(typeof callback == "function"){callback();}
}

function toggleLoadingDown(callback){
    console.log('TOGGLE DOWN');
    $('.innerContent').css("overflow", 'hidden');
    $('.loading').slideUp(slidingDownSpeed, callback);
    $('.content').slideDown(slidingDownSpeed, finishLoading);
    isLoading = false;
    /*if(typeof callback == "function"){callback();}
    finishLoading();*/
}

function slideOutSidebar(){
    sidebar = $(".sidebar .menu");
    if(sidebar.css("left") == '0px') {
        $('.contentLogo').fadeOut('fast');
        sidebar.css("overflow", 'hidden');
        sidebar.animate({"left": "-=" + sidebar.outerWidth() + "px"}, slidingUpSpeed).promise().done(function () {
            sidebar.css("overflow", 'auto');
        });
    }
}

function slideInSidebar(){
    sidebar = $(".sidebar .menu");
    if(sidebar.css("left") != '0px') {
        sidebar.css("overflow", 'hidden');
        sidebar.animate({"left": "+=" + sidebar.outerWidth() + "px"}, slidingDownSpeed).promise().done(function () {
            sidebar.css("overflow", 'auto');
        });
    }
}

/**
 * Called when loading is hided
 */
function finishLoading(){
    initForms();
    isLoading = false;
    isLoadingUp = false;
    $('.content').css("overflow", 'auto');
    $('.contentLogo').fadeIn('slow');
}

/**
 * Call it to enable "form is being edited" modal
 */
function initForms(){
    var $myForm =  $("form");
    $myForm.on('submit', function(e){
         e.preventDefault();
         if(canSubmitForm()){
            return false;
         }
    });
    checkFor('input');
    checkFor('select');
    checkFor('textarea');
    $(".underHeader form input, .underHeader form textarea").first().focus();
}

/**
 * It check if user started to change things in forms
 * @param elementId
 */
function checkFor(elementId){
    $(elementId).change(function() {
        isEditing = true;
    });
}
/**
 * command to manually valid forms
 * @returns {*}
 */
function canSubmitForm(){
    return $("form").valid();
}

/**
 * IMPORTANT!!! Call this before deleting ANYTHING!
 * @param callbackOnTrue
 * @param callbackOnFalse
 */
function askBeforeDelete(callbackOnTrue, callbackOnFalse){
    showYesNo('Suppression', 'Voulez-vous vraiment supprimer ceci?',callbackOnTrue, callbackOnFalse);
}
/**
 * Init WEEK picker to two given elements
 * First is for START week
 * Second if for END week
 * (Methods exist to prevent inverted weeks)
 * @param $startId
 * @param $endId
 * @param $startDate
 */
function initWeekPickers($startId, $endId, $startDate){
    $startDate = $startDate ? $startDate : new Date();
    var startDateInput = $($startId);
    var endDateInput = $($endId);
    startDateInput.weekpicker();
    startDateInput.on( "weekselected", function( event, start, end ) {
        startDateInput.val(start);
        var minDate = new Date(new Date(start).getTime() + DAY_STAMP);
        endDateInput.datepicker( "option", "minDate", minDate);
    });
    endDateInput.weekpicker();
    endDateInput.on( "weekselected", function( event, start, end ) {
        endDateInput.val(end);
        var maxDate = new Date(end);
        startDateInput.datepicker( "option", "maxDate", maxDate);
    });
    startDateInput.datepicker( "option", "minDate", $startDate);
    endDateInput.datepicker( "option", "minDate", $startDate);
}

function initWeekPicker($selector){
    var dateInput = $($selector);
    dateInput.weekpicker();
    dateInput.on( "weekselected", function( event, start, end ) {
        dateInput.val(start);
    });
    dateInput.datepicker( "option", "minDate", new Date());
}

/**
 * Default options to set calandars in french
 * @type {{dateFormat: string, onSelect: onSelect, showOtherMonths: boolean, selectOtherMonths: boolean, dayNames: string[], dayNamesMin: string[], dayNamesShort: string[], monthNames: string[], monthNamesShort: string[], numberOfMonths: number}}
 */
var reqOpt = {
    dateFormat:'dd/mm/yy',
    onSelect: function(dateText, inst) {
        var date = $(this).datepicker('getDate');
        var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
        $(this).trigger('dateSelected',[
            $.datepicker.formatDate( dateFormat, date, inst.settings )
        ]);
    },
    showOtherMonths: true,
    selectOtherMonths: true,
    dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
    dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
    dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
    monthNames: [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
    monthNamesShort: [ "Jan", "Fev", "Mars", "Avr", "Mai", "Juin", "Juill", "Août", "Sept", "Oct", "Nov", "Déc" ],
    numberOfMonths: 1
};

/**
 * Init DATE picker to two given elements
 * First is for START day
 * Second if for END day
 * (Methods exist to prevent inverted days)
 * @param $startId
 * @param $endId
 * @param $startDate
 */
function initDatePickers($startId, $endId, $startDate){
    var startDateInput = $($startId);
    var endDateInput = $($endId);
    startDateInput.datepicker(reqOpt);
    startDateInput.on( "dateSelected", function( event, date ) {
        startDateInput.val(date);
        endDateInput.datepicker( "option", "minDate", date);
    });
    endDateInput.datepicker(reqOpt);
    endDateInput.on( "dateSelected", function( event, date ) {
        endDateInput.val(date);
        startDateInput.datepicker( "option", "maxDate", date);
    });
    if($startDate != 1){
        $startDate = $startDate ? $startDate : new Date();
        startDateInput.datepicker( "option", "minDate", $startDate);
        endDateInput.datepicker( "option", "minDate", $startDate);
    }
}

function initDatePicker($selector){
    var dateInput = $($selector);
    dateInput.datepicker(reqOpt);
    dateInput.on( "dateSelected", function( event, date ) {
        dateInput.val(date);
    });
    dateInput.datepicker( "option", "minDate", new Date());
}
/**
 * Init a TIME picker with given time or default time ("07 : 00")
 * @param $selector
 * @param time
 */
function initTime($selector, time){
    time = time ? getTime(time) : "07 : 00";
    $($selector).wickedpicker({
        now: time, //hh:mm 24 hour format only, defaults to current time
        twentyFour: true,  //Display 24 hour format, defaults to false
        title: 'Choisir l\'heure:' //The Wickedpicker's title
    });
    $($selector).val(time);
}
/**
 * Convert MYSQL TIME to time picker format ("12:22:10:0000" -> "12 : 22")
 * @param mysqlTime
 * @returns {string}
 */
function getTime(mysqlTime){
    var time = moment(mysqlTime, "HH:mm:ss:SSSS").toDate();
    return getFull(time.getHours()) + ' : ' + getFull(time.getMinutes());
}
/**
 * Convert int to 24 hour format string ( 1 -> "01")
 * @param $value int
 * @returns string OR int
 */
function getFull($value){
    if($value < 10){
        return "0" + $value;
    }
    return $value;
}
/**
 * Usefull to scroll automatically to top of the page
 */
function scrollToTop(){
    var body = $("#content");
    body.stop().animate({scrollTop:0}, '500', 'swing', function() {
        alert("Finished animating");
    });
}

var isOn= false;
var isFadingIn = false;

var toggleOn = false;
var toggleOff = false;

var notOnTooltipElementCount = 0;

var actualTooltipId;

function setTooltip(item, title, content){
    $(item).mouseover(function(){
        actualTooltipId = item;
        toggleOn = true;
        updateTooltipState(title, content);
    });
    $(item).mouseleave(function(){
        toggleOff = true;
        updateTooltipState();
    });
}

function updateTooltipState(title, content){
    var tooltip = $('#customTooltip');
    if(toggleOn){
        notOnTooltipElementCount = 0;
        toggleOn = false;
        if(!isOn){
            $('#customTooltipTitle').html(title);
            $('#customTooltipContent').html(content);
            tooltip.css('display', 'block');
            isOn = true;
        }
    }else if(toggleOff || notOnTooltipElementCount > 30){
        notOnTooltipElementCount = 0;
        toggleOff = false;
        if(isOn){
            tooltip.css('display', 'none');
            isOn = false;
        }
    }
}
/**
 *
 * @param itemId string (Id for html element to be selected)
 * @param html string (From callAjax)
 * @param value int
 */
function tryToReselectAfterChange(itemId, html, value){
    var closestValue = null;
    var found = false;
    $(itemId).html(html).promise().done(function(){
        $(itemId+" option").each(function() {
            if(!found) {
                closestValue = $(this).val();
                if (closestValue == value) {
                    $(itemId).val(closestValue);
                    found = true;
                }
            }
        });
        if(!found && closestValue){
            $(itemId).val(closestValue);
        }
    });
}

/**
 * JQuery init things
 */
$(function(){
    contentLogo = $('.contentLogo');
    contentLogo.fadeIn('slow'); //show ghost logo img on load
    contentLogo.hover(function(){//hide ghost logo img because it can block some interactions
        $(this).fadeOut('medium');
    });

   jQuery.validator.setDefaults({
        debug: true, //this stop forms to submit! YAY!!! (Stop Reload page))
        success: "valid"
    });

    //Canadian postal code
    $.validator.addMethod("postalCode", function(value) {
        return value.match(/^[a-zA-Z][0-9][a-zA-Z](-| )?[0-9][a-zA-Z][0-9]$/);
    }, "Veuillez entrer un code postal valide.");

    //fancybox things
    $(".fancybox").fancybox();
    $('.fancybox-media').fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        helpers : {
            media : {}
        }
    });

    if(toggleAsked){
        toggleLoadingDown();
    }

    //Be sure that the broser support indexOf for arrays
    Array.prototype.indexOf || (Array.prototype.indexOf = function(d, e) {
        var a;
        if (null == this) throw new TypeError('"this" is null or not defined');
        var c = Object(this),
            b = c.length >>> 0;
        if (0 === b) return -1;
        a = +e || 0;
        Infinity === Math.abs(a) && (a = 0);
        if (a >= b) return -1;
        for (a = Math.max(0 <= a ? a : b - Math.abs(a), 0); a < b;) {
            if (a in c && c[a] === d) return a;
            a++
        }
        return -1
    });
});
function startLoading(){
    // add the overlay with loading image to the page
    if(overlayLoadingCount ==0) {
        var over = '<div id="overlayLoading">' +
            '<img id="loading" src="img/site/load.gif">' +
            '</div>';
        $(over).appendTo('body');
    }
    overlayLoadingCount++;
}
function stopLoading(){
    overlayLoadingCount--;
    if(overlayLoadingCount < 1){
        $('#overlayLoading').remove();
        overlayLoadingCount = 0;
    }
}

function openModule(callback, tag){
    if(!isLoading){
        clearOptions();
        if(tag) {
            addOption('m', tag);
        }
        if(typeof callback === "function"){
            $.when(callback()).done(function(){
                updateHeader();
            });
        }else{
            updateHeader();
        }
    }
}

function askForToggle(){
    toggleAsked = true;
}

function previewPicture(input, target){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(target).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).mousemove( function(e) {
    if(isOn || isFadingIn) {
        //moving tooltip with cursor...
        //TODO if cursor in bottom right move tooltip to cursor's left!
        var x = e.clientX, y = e.clientY, elementMouseId = document.elementFromPoint(x, y).id;
        var tooltipLeft = e.clientX+30;
        var tooltipTop = e.clientY;
        var tooltip = $('#customTooltip');
        var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
        var height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
        var tooltipHeight = tooltip.outerHeight()+100;
        var tooltipWidth = tooltip.outerWidth()+20;
        var left = ( tooltipLeft + tooltipWidth > width ? width - tooltipWidth : tooltipLeft);
        var top = ( tooltipTop + tooltipHeight > height ? height - tooltipHeight : tooltipTop);
        tooltip.css({'top': top, 'left': left});

        if(elementMouseId != '') {
            if (actualTooltipId != elementMouseId) {
                notOnTooltipElementCount++;
                updateTooltipState();
            }
        }
    }
});
//EXEMPLE D'AUTOCOMPLETE
/*$("#memberToBuyCardAC" ).autocomplete({
 source: function( request, response ) {
 $.ajax({
 url: "/gestion/ajax/LoadUsersAutocomplete",
 dataType: "JSON",
 type: 'POST',
 data: {
 q: request.term
 },
 success: function( data ) {
 response( data );
 }
 });
 },
 select: function(event, ui) {
 $('#memberToBuyCard').val(ui.item.id);
 $('#memberToBuyCardAC').val(ui.item.firstname + " " + ui.item.lastname);
 return false;
 }
 })
 .autocomplete().data("uiAutocomplete")._renderItem =  function( ul, item ){
 return $( "<li>" )
 .append( "<a>" + item.firstname + " " + item.lastname+ "<br>" + item.email + "</a>" )
 .appendTo( ul );
 };*/




