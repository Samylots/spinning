/**
 * Created by administrator on 2016-04-05.
 */

function loadMemberTypes(callback){
    callback = callback ? callback : reload;
    selectOption('Type de membre');
    callAjax('php/memberTypes/getMemberTypes.php', {},'html', callback);
}

/*function editMemberType(id){
    callAjax('php/memberTypes/editMemberType.php', {id: id}, 'html', function (html) {
        replaceContentOf('MemberTypeContent' + id, html, function () {
            today = new Date();
            nextWeek = today - today.getDay()*DAY_STAMP + WEEK_STAMP;
            console.log(new Date(nextWeek));
            initWeekPickers('#newMemberTypeStart' + id, '#newMemberTypeEnd' + id,  new Date(nextWeek));
        });
    }, function (data) {
        showInfo('Attention', 'Cette memberType est ' + data['responseText'] + '. Vous ne pouvez plus la modifier!');
    });
}

function deleteMemberType(id){
    askBeforeDelete(function(){
        callAjax('php/memberTypes/deleteMemberType.php', {id: id},'html');
        hideItem('MemberType' + id);
    });
}

function saveEditMemberType(id){
    var title = $('#newMemberTypeTitle'+id).val();
    var startDate =  $('#newMemberTypeStart'+id).val();
    var endDate =  $('#newMemberTypeEnd'+id).val();
    if(canSubmitForm()) {
        callAjax('php/memberTypes/editMemberType.php', {
            id: id,
            title: title,
            start: startDate,
            end: endDate
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de la memberType ont bien été enregistrées!');
            callAjax('php/memberTypes/getMemberType.php', {id: id},'html', function(html){
                replaceContentOf('MemberTypeContent'+id, html);
            });
        });
    }
}

function cancelEditMemberType(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditMemberType);
    }else {
        cancellingEditMemberType();
    }
}

function cancellingEditMemberType(){
    isEditing = false;
    callAjax('php/memberTypes/getMemberType.php', {id: actualPackageId},'html', function(html){
        replaceContentOf('MemberTypeContent'+actualPackageId, html);
    });
}*/

/*function newMemberType(){
    callAjax('php/memberTypes/newMemberType.php', {}, 'html', function(html){
        customModal('Nouvelle MemberType',html,'Créer','Annuler',function(){
            var title = $('#MemberTypeTitle').val();
            var startDate =  $('#MemberTypeStart').val();
            var endDate =  $('#MemberTypeEnd').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/memberTypes/newMemberType.php', {title: title, start: startDate, end: endDate}, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé un nouveau type de membre!');
                    loadMemberTypes(reload);
                });
            }
        });
    });
}*/

