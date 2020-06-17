/**
 * Created by administrator on 2016-03-23.
 */

function loadRates(callback){
    callback = callback ? callback : reload;
    selectOption('Tarifs');
    callAjax('php/rates/getRates.php', {id:get('session')},'html', callback);
}

function editRate(id){
    actualPackageId = id;
    callAjax('php/rates/editRate.php', {id:get('session') ,package:id}, 'html', function (html) {
        replaceContentOf('RateContent' + id, html, function () {
            initDatePickers('#newRateStart' + id, '#newRateEnd' + id);
        });
    });
}

function deleteRate(id){
    actualPackageId = id;
    askBeforeDelete(function(){
        callAjax('php/rates/deleteRate.php', {id: get('session'), package: id},'html');
        hideItem('Rate' + id);
    });
}

function saveEditRate(id){
    actualPackageId = id;
    var price = $('#newPrice'+id).val();
    if(canSubmitForm()) {
        callAjax('php/rates/editRate.php', {
            id: get('session'),
            package: id,
            price: price
        }, 'html', function(){
            isEditing = false;
            showInfo('Information', 'Les modifications du prix du forfait pour cette session ont bien été enregistrées!');
            callAjax('php/rates/getRate.php', {
                id: get('session'),
                package: id
            },'html', showRateInfo);
        });
    }
}


function cancelEditRate(id){
    actualPackageId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditRate);
    }else {
        cancellingEditRate();
    }
}

function cancellingEditRate(){
    isEditing = false;
    callAjax('php/rates/getRate.php', {
        id: get('session'),
        package: actualPackageId
    },'html', showRateInfo);
}

function showRateInfo(html){
    replaceContentOf('RateContent'+actualPackageId, html);
}

function newRate(){
    callAjax('php/rates/newRate.php', {id:get('session')}, 'html', function(html){
        customModal('Déterminer un prix:',html,'Enregistrer','Annuler',function(){
            var price = $('#Price').val();
            var package = $('#packageSelector').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/rates/newRate.php', {
                    id: get('session'),
                    package: package,
                    price: price
                }, 'html', function () {
                    showInfo('Information', 'Vous avez bien défini le prix de ce forfait pour cette session!');
                    loadRates(reload);
                });
            }

        })
    });
}
