/**
 * Created by administrator on 2016-03-16.
 */

function loadCompanies(callback){
    callback = callback ? callback : reload;
    selectOption('Compagnies');
    callAjax('php/companies/getCompanies.php', {},'html', callback);
}

function editCompanie(id){
    callAjax('php/companies/editCompanie.php', {id: id}, 'html', function (html) {
        replaceContentOf('CompanieContent' + id, html);
    });
}

function deleteCompanie(id){
    askBeforeDelete(function(){
        callAjax('php/companies/deleteCompanie.php', {id: id},'html');
        hideItem('Companie' + id);
    });
}

function saveEditCompanie(id){
    actualPackageId = id;
    var title = $('#newCompanieTitle'+id).val();
    if(canSubmitForm()) {
        callAjax('php/companies/editCompanie.php', {
            id: id,
            title: title
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de la compagnie ont bien été enregistrées!');
            callAjax('php/companies/getCompanie.php', {id: id},'html', showCompanieInfo);
        });
    }
}

function cancelEditCompanie(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditCompanie);
    }else {
        cancellingEditCompanie();
    }
}

function cancellingEditCompanie(){
    isEditing = false;
    callAjax('php/companies/getCompanie.php', {id: actualPackageId},'html', showCompanieInfo);
}

function showCompanieInfo(html){
    replaceContentOf('CompanieContent'+actualPackageId, html);
}

function newCompanie(){
    callAjax('php/companies/newCompanie.php', {}, 'html', function(html){
        customModal('Nouvelle compagnie',html,'Créer','Annuler',function(){
            var title = $('#CompanieTitle').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/companies/newCompanie.php', {title: title}, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé une nouvelle compagnie!');
                    loadCompanies(reload);
                });
            }
        });
    });
}
