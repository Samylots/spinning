/**
 * Created by administrator on 2016-03-16.
 */

function loadActivityTypes(callback){
    callback = callback ? callback : reload;
    selectOption('Activités');
    callAjax('php/activityTypes/getActivityTypes.php', {},'html', callback);
}

function editActivityType(id){
    callAjax('php/activityTypes/editActivityType.php', {id: id},'html', function(html){
        replaceContentOf('ActivityTypeContent'+id, html);
    });
}

function deleteActivityType(id){
    askBeforeDelete(function(){
        callAjax('php/activityTypes/deleteActivityType.php', {id: id},'html');
        hideItem('ActivityType' + id);
    });
}

function saveEditActivityType(id){
    var title = $('#newActivityTypeTitle'+id).val();
    var places =  $('#newActivityTypePlaces'+id).val();
    var description =  $('#newActivityTypeDescription'+id).val();
    var color =  $('#colorSelector'+id).val();
    color = tinycolor(color).toHexString();
    //var endDate =  $('#newActivityTypeDescription'+actualActivityTypeId).val(); //PICTURE?????
    if(canSubmitForm()) {
        callAjax('php/activityTypes/editActivityType.php', {
            id: id,
            title: title,
            places: places,
            description: description,
            color: color
        }, 'html', function(){
            isEditing = false;
            showInfo('Information', 'Les modifications de l\'activité ont bien été enregistrées!');
            callAjax('php/activityTypes/getActivityType.php', {id: id},'html', function(html){
                showActivityTypeInfo(id, html);
            });
        });
    }
}

function cancelEditActivityType(id){
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',function(){
            cancellingEditActivityType(id);
        });
    }else {
        cancellingEditActivityType(id);
    }
}

function cancellingEditActivityType(id){
    isEditing = false;
    callAjax('php/activityTypes/getActivityType.php', {id: id},'html', function(html){
        showActivityTypeInfo(id, html);
    });
}

function showActivityTypeInfo(id, html){
    replaceContentOf('ActivityTypeContent'+id, html);
}

function newActivityType(){
    callAjax('php/activityTypes/newActivityType.php', {},'html', function(html){
        customModal('Nouvelle activité',html,'Créer','Annuler',function(){
            var title = $('#newActivityTypeTitle').val();
            var places =  $('#newActivityTypePlaces').val();
            var description =  $('#newActivityTypeDescription').val();
            var color =  $('#colorSelector').val();
            color = '#' + tinycolor(color).toHex();
            //var endDate =  $('#newActivityTypeDescription'+actualActivityTypeId).val(); //PICTURE?????
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/activityTypes/newActivityType.php', {
                    title: title,
                    places: places,
                    description: description,
                    color: color
                }, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé une nouvelle activité!');
                    loadActivityTypes(reload);
                });
            }
        })
    });
}
