/**
 * Created by administrator on 2016-03-16.
 */

function loadSessions(callback){
    callback = callback ? callback : reload;
    selectOption('Sessions');
    callAjax('php/sessions/getSessions.php', {},'html', callback);
}

function loadPublicSessions(callback){
    callback = callback ? callback : reload;
    selectOption('Sessions');
    callAjax('php/sessions/showSessions.php', {},'html', callback);
}

function editSession(id){
    callAjax('php/sessions/editSession.php', {id: id}, 'html', function (html) {
        replaceContentOf('SessionContent' + id, html);
    }, function (data) {
        showInfo('Attention', 'Cette session est ' + data['responseText'] + '. Vous ne pouvez plus la modifier!');
    });
}

function deleteSession(id){
    askBeforeDelete(function(){
        callAjax('php/sessions/deleteSession.php', {id: id},'html');
        hideItem('Session' + id);
    });
}

function saveEditSession(id){
    var title = $('#newSessionTitle'+id).val();
    var startDate =  $('#newSessionStart'+id).val();
    var endDate =  $('#newSessionEnd'+id).val();
    var placesDate =  $('#newSessionSubscriptionPlaces'+id).val();
    if(canSubmitForm()) {
        callAjax('php/sessions/editSession.php', {
            id: id,
            title: title,
            start: startDate,
            end: endDate,
            placesDate:placesDate
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de la session ont bien été enregistrées!');
            callAjax('php/sessions/getSession.php', {id: id},'html', function(html){
                replaceContentOf('SessionContent'+id, html);
            });
        });
    }
}

function cancelEditSession(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditSession);
    }else {
        cancellingEditSession();
    }
}

function cancellingEditSession(){
    isEditing = false;
    callAjax('php/sessions/getSession.php', {id: actualPackageId},'html', function(html){
        replaceContentOf('SessionContent'+actualPackageId, html);
    });
}

function newSession(){
    callAjax('php/sessions/newSession.php', {}, 'html', function(html){
        customModal('Nouvelle Session',html,'Créer','Annuler',function(){
            var title = $('#SessionTitle').val();
            var startDate =  $('#SessionStart').val();
            var endDate =  $('#SessionEnd').val();
            var placesDate =  $('#SessionSubscriptionPlaces').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/sessions/newSession.php', {title: title, start: startDate, end: endDate, placesDate:placesDate}, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé une nouvelle session!');
                    loadSessions(reload);
                });
            }
        });
    });
}

function manageSession(id){
    redirect('administrationSessions.php?session='+id);
}

function showSchedule(id){
    redirect('schedules.php?session='+id);
}
