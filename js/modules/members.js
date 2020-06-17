/**
 * Created by administrator on 2016-03-16.
 */

function loadMembers(callback){
    callback = callback ? callback : reload;
    selectOption('Membres');
    callAjax('php/members/getMembers.php', {},'html', callback);
}

function loadAutocompleteMembers(q, autocompleteId){
    callAjax('php/members/getMembersAutocomplete.php',{q:q},'html',function(html){
        setAutocompleteContent(html,autocompleteId);
    });
}

function editMember(id){
    callAjax('php/members/editMember.php', {id: id}, 'html', function (html) {
        replaceContentOf('MemberContent' + id, html);
    });
}

function deleteMember(id){
    askBeforeDelete(function(){
        callAjax('php/members/deleteMember.php', {id: id},'html');
        hideItem('Member' + id);
    });
}

function saveEditMember(id){
    var firstname = $('#newMemberFirstname'+id).val();
    var lastname = $('#newMemberLastname'+id).val();
    var email = $('#newMemberEmail'+id).val();
    var phone = $('#newMemberPhone'+id).val();
    var postalCode = $('#newMemberPostalCode'+id).val();
    var nickname = $('#newMemberUsername'+id).val();
    var gender = $('#genderSelector'+id).val();
    var birthdate = new Date($('#yearSelector'+id).val() + '-' + $('#monthSelector'+id).val()+ '-' + $('#daySelector'+id).val());
    var type = $('#memberTypeSelector'+id).val();
    if(canSubmitForm()) {
        callAjax('php/members/editMember.php', {
            id: id,
            firstname: firstname,
            lastname: lastname,
            email: email,
            phone: phone,
            nickname: nickname,
            gender: gender,
            birthdate: birthdate.getTime(),
            type: type,
            postalCode:postalCode
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications du membre ont bien été enregistrées!');
            callAjax('php/members/getMember.php', {id: id},'html', function(html){
                replaceContentOf('MemberContent'+id, html);
            });
        });
    }
}

function cancelEditMember(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditMember);
    }else {
        cancellingEditMember();
    }
}

function cancellingEditMember(){
    isEditing = false;
    callAjax('php/members/getMember.php', {id: actualPackageId},'html', function(html){
        replaceContentOf('MemberContent'+actualPackageId, html);
    });
}

function newMember(){
    callAjax('php/members/newMember.php', {}, 'html', function(html){
        customModal('Nouveau Membre',html,'Créer','Annuler',function(){
            var firstname = $('#newMemberFirstname').val();
            var lastname = $('#newMemberLastname').val();
            var email = $('#newMemberEmail').val();
            var phone = $('#newMemberPhone').val();
            var postalCode = $('#newMemberPostalCode').val();
            var nickname = $('#newMemberUsername').val();
            var gender = $('#genderSelector0').val();
            var birthdate = new Date($('#yearSelector0').val() + '-' + $('#monthSelector0').val()+ '-' + $('#daySelector0').val());
            var type = $('#memberTypeSelector0').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/members/newMember.php', {
                    firstname: firstname,
                    lastname: lastname,
                    email: email,
                    phone: phone,
                    nickname: nickname,
                    gender: gender,
                    birthdate: birthdate.getTime(),
                    type: type,
                    postalCode:postalCode
                }, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé un nouveau membre!');
                    loadMembers(reload);
                });
            }
        });
    });
}

function manageMember(id){
    redirect('administrationMembers.php?member='+id);
}

function removeMemberCoach(coachId, meetingId){
    showYesNo('Modifications', 'Voulez-vous vraiment retirer cet entraîneur?',function(){
        callAjax('php/meetings/removeCoach.php', {
            meetingId: meetingId,
            coachId:coachId
        }, 'html', function(){
            callAjax('php/members/listActualMeetingCoaching.php', {
                memberId: coachId
            },'html',function(html){
                replaceContentOf('actualMeetingsCoaching', html);
            });
        });
    });
}
