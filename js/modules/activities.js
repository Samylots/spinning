/**
 * Created by administrator on 2016-03-16.
 */

function loadActivities(callback){
    callback = callback ? callback : reload;
    selectOption('Cours');
    callAjax('php/activities/getActivities.php', {},'html', callback);
}

function editActivity(id){
    actualActivityId = id;
    callAjax('php/activities/editActivity.php', {id: id},'html', function(html){
        replaceContentOf('ActivityContent'+id, html);
    });
}

function deleteActivity(id){
    actualActivityId = id;
    askBeforeDelete(function(){
        callAjax('php/activities/deleteActivity.php', {id: id},'html');
        hideItem('ModuleItem' + id);
    });
}

function saveEditActivity(id){
    actualActivityId = id;
    var title = $('#newActivityTitle'+id).val();
    var units =  $('#newActivityUnits'+id).val();
    unitsTest = parseFloat(units);
    if(canSubmitForm()) {
        if (typeof unitsTest !== "number" || unitsTest.toString() == 'NaN') {
            invalid();
        } else {
            callAjax('php/activities/editActivity.php', {id: id, title: title, units: units}, 'html', updateSuccess);
        }
    }
}

function updateSuccess(html){
    isEditing = false;
    showInfo('Information', 'Les modifications du cours ont bien été enregistrées!');
    callAjax('php/activities/getActivity.php', {id: actualActivityId},'html', showActivityInfo);
}

function cancelEditActivity(id){
    actualActivityId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditActivity);
    }else {
        cancellingEditActivity();
    }
}

function cancellingEditActivity(){
    isEditing = false;
    callAjax('php/activities/getActivity.php', {id: actualActivityId},'html', showActivityInfo);
}

function showActivityInfo(html){
    replaceContentOf('ActivityContent'+actualActivityId, html);
}

function newActivity(){
    callAjax('php/activities/newActivity.php', {}, 'html', function(html){
        customModal('Nouveau cours',html,'Créer','Annuler',function(){
            var title = $('#ActivityTitle').val();
            var units =  $('#ActivityUnits').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/activities/newActivity.php', {title: title, units: units}, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé un nouveeau cours!');
                    loadActivities(reload);
                });
            }

        })
    });
}

function selectTypeToAdd(id){
    actualActivityId = id;
    callAjax('php/activityTypes/listActivityTypes.php', {id:id}, 'html',function(html){
        customModal('Activités du cours:', html ,'Ajouter','Annuler', function(){
            $selectedType = $('#typeSelector').val();
            hideCustom();
            callAjax('php/activities/addType.php', {id:id, idType:$selectedType},'html',function(){
                updateTypes(id);
            });
        });
    },function(){
        showInfo('Erreur', 'Désolé, ce cours pratique déjà toutes les activités!');
    })
}

function removeType(id, idType){
    showYesNo('Modifications', 'Voulez-vous vraiment retirer cette activité ?',function(){
        actualActivityId = id;
        callAjax('php/activities/deleteType.php', {id:id, idType:idType}, 'html',function(){
            updateTypes(id);
        });
    });

}

function updateTypes(id){
    callAjax('php/activities/showActivitiesTypes.php', {id: id},'html', function(html){
        replaceContentOf('activityTypes'+id, html);
    });
}
