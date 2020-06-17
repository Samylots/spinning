/**
 * Created by administrator on 2016-03-16.
 */

var actualWeek;

function loadSessionWeek(weekId, callback){
    actualWeek = weekId;
    callback = callback ? callback : reload;
    selectOption(weekId+1);
    callAjax('php/meetings/getSessionWeek.php', {
        sessionId: get('session'),
        weekId : weekId
    },'html', callback);
}

function editMeeting(meetingId){
    console.log(meetingId);
    actualMeetingId = meetingId;
    callAjax('php/meetings/editMeeting.php', {
        sessionId: get('session'),
        meetingId: meetingId
    }, 'html', function (html) {
        customModal('Modifier une séance:', html ,'Enregistrer','Annuler',function(){
            var start = $('#NewMeetingStart').val();
            var end =  $('#NewMeetingEnd').val();
            var startTime =  moment(start, "HH : mm").toDate();
            var endTime =  moment(end, "HH : mm").toDate();
            var active = $('#NewMeetingActive').prop('checked');
            console.log(active);
            if(startTime >= endTime){
                invalid('Veuillez vérifier que l\'heure de fin soit plus tard que l\'heure de début.');
            }else {
                if(canSubmitForm()) {
                    hideCustom();
                    callAjax('php/meetings/editMeeting.php', {
                        sessionId:get('session'),
                        meetingId:meetingId,
                        start: start,
                        end: end,
                        active: (active ? 'false':'true')
                    }, 'html', function () {
                        showInfo('Information', 'Les modifications de la séance ont bien été enregistrées!');
                        reloadWeek();
                    });
                }
            }
        }, function(){
            if(isEditing){
                showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditSessionWeek);
            }else {
                cancellingEditSessionWeek();
            }
        });
    },function(){
        showInfo('Erreur', 'Désolé, il est présentement impossible de modifier la période souhaité');
    });
}

function addCoach(){
    callAjax('php/meetings/listCoaches.php',{
        meetingId: actualMeetingId
    },'html',function(html){
        customModal('Entraîneurs disponibles',html,'Assigner','Annuler',function(){
            var coachId = $('#coachSelector').val();
            if(canSubmitForm() && coachId) {
                hideCustom();
                callAjax('php/meetings/addCoach.php', {
                    meetingId: actualMeetingId,
                    coachId: coachId
                }, 'html', function () {
                    //showInfo('Information', 'Vous avez bien assigné un entraîneur!');
                    updateCoaches();
                });
            }
        },null,function(){
            disableCustomButton();
        });
    })
}

function removeCoach(coachId){
    showYesNo('Modifications', 'Voulez-vous vraiment retirer cet entraîneur?',function(){
        callAjax('php/meetings/removeCoach.php', {
            meetingId: actualMeetingId,
            coachId:coachId
        }, 'html',function(){
            updateCoaches();
        });
    });
}

function updateCoaches(){
    callAjax('php/meetings/listActualCoaches.php', {
        meetingId: actualMeetingId
    },'html', function(html){
        replaceContentOf('coaches', html);
    });
    reloadWeek();
}

function saveEditSessionWeek(meetingId){
    var title = $('#newSessionWeekTitle'+meetingId).val();
    var startDate =  $('#newSessionWeekStart'+meetingId).val();
    var endDate =  $('#newSessionWeekEnd'+meetingId).val();
    if(canSubmitForm()) {
        callAjax('php/meetings/editMeeting.php', {
            id: meetingId,
            title: title,
            start: startDate,
            end: endDate
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de la séance ont bien été enregistrés!');
            callAjax('php/meetings/getSessionWeek.php', {id: meetingId},'html', function(html){
                replaceContentOf('SessionWeekContent'+meetingId, html);
            });
        });
    }
}

function cancellingEditSessionWeek(){
    isEditing = false;
    hideCustom();
    reloadWeek();
}

function openWeek(weekId){
    setOptions({week:weekId});
    loadSessionWeek(weekId);
}

function reloadWeek(){
    setOptions({week:actualWeek});
    loadSessionWeek(actualWeek, setContent);
}
