/**
 * Created by administrator on 2016-03-16.
 */

var actualMeetingId;

function loadPublicSessionWeek(weekId, callback){
    actualWeek = weekId;
    callback = callback ? callback : reload;
    selectOption(weekId+1);
    callAjax('php/meetings/getPublicSessionWeek.php', {
        sessionId: get('session'),
        weekId : weekId
    },'html', callback);
}

function showMeeting(meetingId){
    actualMeetingId = meetingId;
    console.log(actualMeetingId);
    callAjax('php/registrations/registerMember.php', {
        meetingId: meetingId
    }, 'html', function (html) {
        customModal('Inscription à une séance:', html ,'Inscrire','Fermer',function(){
            if( $('#packageSelector').has('option').length == 0 ) {
                showYesNo('Proposition d\'achat de forfait', 'Désolé, vous devez avoir acheté préalablement un ' +
                    'forfait afin de pouvoir réserver une place avec celui-ci. <br><br> Voulez vous être redirigé vers la page ' +
                    'd\'achat de forfaits?', function(){
                        hideCustom();
                        var memberId = $("#memberSelector").val();
                        if(memberId) {
                            redirect('administrationMembers.php?member='+memberId, 'Purchases');
                        }else{
                            redirect('members.php', 'Purchases');
                        }
                });
            }else{
                if(canSubmitForm()) {
                    //callAjax('php/registrations/isMeetingNotFull.php',{meetingId:meetingId},'html',function(){
                        registerToMeeting(meetingId);
                    /*},function(){
                        showYesNo('Scéance pleine!', 'Attention! Cette séance est déjà pleine!<br>' +
                            'Il est possible de placer en file d\'attente avec un dépot<br>' +
                            '(Le dépot est le coût de la séance et il sera récupéré si vous n\'avez pas eu la chance d\'avoir une place à cette séance)<br>' +
                            'Souhaitez-vous être placé en file d\'attente?',
                            function(){
                            registerToMeeting(meetingId);
                        });
                    })*/
                }
            }


        }, function(){
            cancelRegistration();
        });
        updateRegistrations(); //pour que les données soient à jour dans l'affichage
    });
}

function registerToMeeting(meetingId){
    var receiptId = $('#packageSelector').val();
    var name = $('#newRegistrationName').val();
    callAjax('php/registrations/registerMember.php', {
        meetingId:meetingId,
        receiptId: receiptId,
        name: name
    }, 'html', function () {
        showInfo('Information', 'L\'inscription a été enregistrée avec succès!');
        updateRegistrations();
    });
}

function deleteRegistration(registrationId){
    showYesNo('Modifications', 'Voulez-vous vraiment annuler cette inscription?',function(){
        callAjax('php/registrations/deleteRegistration.php', {
            registrationId: registrationId
        }, 'html',function(){
            updateRegistrations();
        },function(){
            updateRegistrations();
        });
    });

}

function updateRegistrations(){
    var memberId = $("#memberSelector").val();
    var selectedPackage = $("#packageSelector").val();
    console.log(actualMeetingId);
    if(memberId){ //The only way to see if it's the admin controls or not...
        callAjax("php/registrations/listActualRegistrations.php", {meetingId: actualMeetingId, memberId:memberId}, "html", function (html) {
            replaceContentOf("registrations", html);
        });
        callAjax("php/registrations/getPurchasesOptions.php", {meetingId: actualMeetingId,memberId:memberId}, "html", function (html) {
            tryToReselectAfterChange("#packageSelector", html, selectedPackage);
        });
    }else {
        callAjax("php/registrations/listActualRegistrations.php", {meetingId: actualMeetingId}, "html", function (html) {
            replaceContentOf("registrations", html);
        });
        callAjax("php/registrations/getPurchasesOptions.php", {meetingId: actualMeetingId}, "html", function (html) {
            tryToReselectAfterChange("#packageSelector", html, selectedPackage);
        });
    }
    reloadPublicWeek();
}

function cancelRegistration(){
    isEditing = false;
    hideCustom();
    reloadPublicWeek();
}

function openPublicWeek(weekId){
    setOptions({week:weekId});
    loadPublicSessionWeek(weekId);
}

function reloadPublicWeek(){
    setOptions({week:actualWeek});
    loadPublicSessionWeek(actualWeek, setContent);
}

function setList(meetingId){
    callAjax('php/registrations/listMembers.php',{meetingId:meetingId},'html',function(html){
        setTooltip('#Meeting'+meetingId, 'Membres participants:', html);
    });
}

