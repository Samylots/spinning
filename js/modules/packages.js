/**
 * Created by administrator on 2016-03-28.
 */

function loadPackages(callback){
    callback = callback ? callback : reload;
    selectOption('Forfaits');
    callAjax('php/packages/getPackages.php', {},'html', callback);
}

function editPackage(id){
    callAjax('php/packages/editPackage.php', {id: id}, 'html', function (html) {
        replaceContentOf('PackageContent' + id, html, function () {
        });
    });
}

function deletePackage(id){
    askBeforeDelete(function(){
        callAjax('php/packages/deletePackage.php', {id: id},'html');
        hideItem('Package' + id);
    });
}

function saveEditPackage(id){
    var limitStart = $('#newLimitStart'+id).val();
    var limitCancel =  $('#newLimitCancel'+id).val();
    var meetings =  $('#newMeetings'+id).val();
    var weekly =  $('#newWeekly'+id).prop('checked');
    var weekAdvance =  $('#newWeekAdvance'+id).val();
    if(canSubmitForm()) {
        callAjax('php/packages/editPackage.php', {
            id: id,
            start: limitStart,
            cancel: limitCancel,
            meetings: meetings,
            weekly: weekly,
            weekAdvance: weekAdvance
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications du forfait ont bien été enregistrées!');
            callAjax('php/packages/getPackage.php', {id: id},'html', function(html){
                replaceContentOf('PackageContent'+id, html);
            });
        });
    }
}

function cancelEditPackage(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditPackage);
    }else {
        cancellingEditPackage();
    }
}

function cancellingEditPackage(){
    isEditing = false;
    callAjax('php/packages/getPackage.php', {id: actualPackageId},'html',  function(html){
        replaceContentOf('PackageContent'+actualPackageId, html);
    });
}

function newPackage(){
    callAjax('php/packages/newPackage.php', {}, 'html', function(html){
        customModal('Nouveau forfait',html,'Créer','Annuler',function(){
            var limitStart = $('#LimitStart').val();
            var limitCancel =  $('#LimitCancel').val();
            var meetings =  $('#Meetings').val();
            var weekly =  $('#Weekly').prop('checked');
            var weekAdvance =  $('#WeekAdvance').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/packages/newPackage.php', {
                    start: limitStart,
                    cancel: limitCancel,
                    meetings: meetings,
                    weekly: weekly,
                    weekAdvance: weekAdvance}, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé un nouveau forfait!');
                    loadPackages(reload);
                });
            }
        });
    });
}
