/**
 * Created by administrator on 2016-03-16.
 */

function loadTaxes(callback){
    callback = callback ? callback : reload;
    selectOption('Taxes');
    callAjax('php/taxes/getTaxes.php', {},'html', callback);
}

function editTax(id){
    callAjax('php/taxes/editTaxe.php', {id: id}, 'html', function (html) {
        replaceContentOf('TaxeContent' + id, html);
    });
}

function saveEditTax(id){
    var type = $('#typeSelector'+id).val();
    var percentage =  $('#NewTaxPercentage'+id).val();
    if(canSubmitForm()) {
        callAjax('php/taxes/editTaxe.php', {
            type: type,
            percentage: percentage
        }, 'html', function(html){
            isEditing = false;
            showInfo('Information', 'Les modifications de la taxe ont bien été enregistrées!');
            loadTaxes();
        });
    }
}

function cancelEditTax(id){
    actualTaxId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditTax);
    }else {
        cancellingEditTax();
    }
}

function cancellingEditTax(){
    isEditing = false;
    var type = $('#typeSelector'+actualTaxId).val();
    callAjax('php/taxes/getTaxe.php', {type: type},'html', function(html){
        replaceContentOf('TaxeContent'+actualTaxId, html);
    });
}

function manageTax(type){
    newTax(function(){
        $('#typeSelector').val(type);
    });
}

function deleteTax(id, typeId){
    actualTaxTypeId = id;
    askBeforeDelete(function(){
        callAjax('php/taxTypes/deleteTaxType.php', {id: typeId},'html');
        hideItem('Taxe' + id);
    });
}

function newTaxType(){
    callAjax('php/taxTypes/newTaxType.php', {}, 'html', function(html){
        customModal('Nouvelle taxe:',html,'Continuer','Annuler',function(){
            var code = $('#TaxCode').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/taxTypes/newTaxType.php', {
                    code: code.toUpperCase()
                }, 'html', function(type){
                    callAjax('php/taxes/newTaxe.php', {type:type}, 'html', function(html){
                        customModal('Pourcentage de la nouvelle taxe',html,'Créer','Annuler',function(){
                            var type = $('#typeSelector').val();
                            var percentage =  $('#TaxPercentage').val();
                            if(canSubmitForm()) {
                                hideCustom();
                                callAjax('php/taxes/newTaxe.php', {
                                    type: type,
                                    percentage: percentage
                                }, 'html', function () {
                                    showInfo('Information', 'Vous avez bien créer la nouvelle taxe!');
                                    loadTaxes(reload);
                                });
                            }
                        },null,function(){
                            $('#typeSelector').val(type);
                        });
                    });
                });
            }
        })
    });
}