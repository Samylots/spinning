/**
 * Created by administrator on 2016-03-17.
 */
var actualModalIndex = 0;
var enterBindQueue = [];
var enterBindIdex = 0;
var canToggleEnter =false;

var canHideCustom = false;

var callbackForInfo;
var callbackForYesNo;

var onCustomShownCallback;

function showInfo(title, text, callbackOnClose) {
    $('#modalTitle').html(title);
    $('#modalContent').html(text);

    callbackForInfo = callbackOnClose;

    $('#modal').modal('show');
}

function showYesNo(title, text, callbackOnTrue, callbackOnFalse){
    $('#yesNoDialogTitle').html(title);
    $('#yesNoDialogContent').html(text);

    //Unbind previous event
    var yesOption = $('#yesNoDialogYesOption');
    var noOption = $('#yesNoDialogNoOption');
    yesOption.off();
    noOption.off();
    yesOption.click( function (){
            callbackForYesNo = callbackOnTrue;
        });
    noOption.click( function (){
            callbackForYesNo = callbackOnFalse;
        });
    $('#yesNoDialog').modal('show');
}

function customModal(title, text,yesText, noText,callbackOnTrue, callbackOnFalse, callbackOnShown)
{
    callbackOnFalse = callbackOnFalse ? callbackOnFalse : hideCustom;
    var modalToTest = $('#CustomDialog'+ actualModalIndex);
    if(modalToTest.length == 0 || modalToTest.hasClass('in')) {
        createCustomModal();
    }

    $('#CustomDialogTitle'+ actualModalIndex).text(title);

    $.when($('#CustomDialogContent'+ actualModalIndex).html(text)).done(function(){
    });

    onCustomShownCallback = function () {
        if(typeof callbackOnShown === 'function'){
            callbackOnShown();
        }
        initForms();
        $("#CustomDialog" + actualModalIndex + " form input, #CustomDialog" + actualModalIndex + " form textarea").first().focus();
        bindEnterTo('#CustomDialog'+ actualModalIndex, '#CustomDialogYesOption'+ actualModalIndex);
    };

    //Unbind previous events
    var yesOption = $('#CustomDialogYesOption'+ actualModalIndex);
    var noOption = $('#CustomDialogNoOption'+ actualModalIndex);
    yesOption.text(yesText);
    noOption.text(noText);
    yesOption.off();
    noOption.off();
    yesOption.click(
        function (){
            if (typeof callbackOnTrue === 'function') {
                canHideCustom = true;
                isEditing = false;
                callbackOnTrue();
            }
        });
    noOption.click(
        function (){
            if(typeof callbackOnFalse === 'function'){
                callbackOnFalse();
            }
        });
    //it need to be a duplicated selector in case we created
    // a new custom modal and the actual modal index changed!
    $('#CustomDialog'+ actualModalIndex).modal('show');
}

function createCustomModal(){
    actualModalIndex++;
    //Create only if not exist! (Otherwise, you can reuse an old customModal)
    if($('#CustomDialog'+ actualModalIndex).length == 0) {
        $('body').append('<div class="modal fade draggable" class="CustomDialog" id="CustomDialog' + actualModalIndex + '" role="alertdialog">' +
            '<div class="modal-dialog"><div class="modal-content" role="document"><div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
            '<h4 id="CustomDialogTitle' + actualModalIndex + '" class="modal-title">Modal Header</h4></div>' +
            '<div id="CustomDialogContent' + actualModalIndex + '" class="modal-body"></div><div class="modal-footer">' +
            '<button id="CustomDialogYesOption' + actualModalIndex + '" type="button" class="btn btn-default confirm">Oui</button>' +
            '<button id="CustomDialogNoOption' + actualModalIndex + '" type="button" class="btn btn-default cancel">Non</button>' +
            '</div></div></div></div>');
        $('#CustomDialog' + actualModalIndex).off('shown.bs.modal').on('shown.bs.modal', function () {
            onCustomShownCallback();
        });
        $('#CustomDialog' + actualModalIndex).off('hidden.bs.modal').on('hidden.bs.modal', function () {
            unbindEnterKey();
        });
        $('#CustomDialog' + actualModalIndex).off('hide.bs.modal').on('hide.bs.modal', function (e) {
            if(!canHideCustom) {
                e.preventDefault();
                hideCustom();
            }else{
                canHideCustom = false;
            }
        });
    }
}

function hideCustom(){
    if(isEditing){
    showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',privateCloseCustom);
    }else {
        privateCloseCustom();
    }
}

function privateCloseCustom(){
    canHideCustom = true;
    enableCustomButton();
    var actualModal = $('#CustomDialog'+ actualModalIndex);
    $.when(actualModal.modal('hide')).done(function(){
        $('.modal-backdrop').last().remove();
        actualModalIndex--;
    });

}

function disableCustomButton(){
    $('#CustomDialogYesOption'+ actualModalIndex).prop('disabled', true);
}

function enableCustomButton(){
    $('#CustomDialogYesOption'+ actualModalIndex).prop('disabled', false);
}

function bindEnterTo(modal, item){
    enterBindQueue[enterBindIdex] = {'modal': modal, 'item':item, 'isEditing':isEditing};
    isEditing = false; //reset for new custom modal state
    canToggleEnter = true;
    enterBindIdex++;
    $(document).unbind("keyup").keyup(function(e){
        var code = e.which; // recommended to use e.which, it's normalized across browsers
        if(code==13) { // 13 = enter key
            if(canToggleEnter) {
                canToggleEnter = false; //prevent fast enter toggling
                $(item).click();
            }
        }
        if(code==27){ // 27 = escape
            canToggleEnter = false;
            $(modal).modal('hide')
        }
    });
}

function unbindEnterKey(){
    var lastState = enterBindQueue.pop();
    enterBindIdex--;
    if(enterBindQueue.length == 0){ //when it's last binded item
        $(document).unbind("keyup").keyup(function(e){}); //unbind enter key when modal hidden!
    }else{ //when there is other items "binded" to it
        enterBindIdex--;
        $('body').toggleClass( "modal-open" ); //Fix the scroll bar for modals that disappeared
        var previous = enterBindQueue.pop();
        bindEnterTo(previous['modal'], previous['item']);
    }
    isEditing = lastState['isEditing'];
}

$(function(){
    var modal = $('#modal');
    var yesNoModal = $('#yesNoDialog');
    modal.on('shown.bs.modal', function () {
        bindEnterTo('#modal', '#OkButton');
    });
    modal.on('hidden.bs.modal', function () {
        unbindEnterKey();
        if (typeof callbackForInfo === 'function') {
            callbackForInfo();
        }
    });

    yesNoModal.on('shown.bs.modal', function () {
        bindEnterTo('#yesNoDialog', '#yesNoDialogYesOption');
    });
    yesNoModal.on('hidden.bs.modal', function () {
        unbindEnterKey();
        if (typeof callbackForYesNo === 'function') {
            callbackForYesNo();
        }
    });

});

/**
 * To be able to call multiple modals on top of each others
 */
$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});