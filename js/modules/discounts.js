/**
 * Created by administrator on 2016-03-23.
 */

function loadDiscounts(callback){
    callback = callback ? callback : reload;
    selectOption('Rabais');
    callAjax('php/discounts/getDiscounts.php', {},'html', callback);
}

function editDiscount(id){
    actualDiscountId = id;
    callAjax('php/discounts/editDiscount.php', {id: id}, 'html', function (html) {
        replaceContentOf('DiscountContent' + id, html, function () {
            initDatePickers('#newDiscountStart' + id, '#newDiscountEnd' + id);
        });
    });
}

function deleteDiscount(id){
    actualDiscountId = id;
    askBeforeDelete(function(){
        callAjax('php/discounts/deleteDiscount.php', {id: id},'html');
        hideItem('Discount' + id);
    });
}

function saveEditDiscount(id){
    actualDiscountId = id;
    var code = $('#newDiscountCode'+id).val();
    var description =  $('#newDiscountDescription'+id).val();
    var start =  $('#newDiscountStart'+id).val();
    var end =  $('#newDiscountEnd'+id).val();
    var minAge =  $('#newDiscountAge'+id).val();
    var type =  $('#DiscountTypeSelector'+id).val();
    var value =  $('#newDiscountValue'+id).val();
    var company = $('#companySelector'+id).val();
    if(canSubmitForm()) {
        callAjax('php/discounts/editDiscount.php', {
            id: id,
            code: code,
            start: start,
            end: end,
            minAge: minAge,
            description: description,
            type: type,
            value: value,
            company: company
        }, 'html', function(){
            isEditing = false;
            showInfo('Information', 'Les modifications du rabais ont bien été enregistrées!');
            callAjax('php/discounts/getDiscount.php', {id: id},'html', showDiscountInfo);
        });
    }
}


function cancelEditDiscount(id){
    actualDiscountId = id;
    if(isEditing){
        showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditDiscount);
    }else {
        cancellingEditDiscount();
    }
}

function cancellingEditDiscount(){
    isEditing = false;
    callAjax('php/discounts/getDiscount.php', {id: actualDiscountId},'html', showDiscountInfo);
}

function showDiscountInfo(html){
    replaceContentOf('DiscountContent'+actualDiscountId, html);
}

function newDiscount(){
    callAjax('php/discounts/newDiscount.php', {}, 'html', function(html){
        customModal('Modifier un rabais:',html,'Enregistrer','Annuler',function(){
            var code = $('#DiscountCode').val();
            var description =  $('#DiscountDescription').val();
            var start =  $('#DiscountStart').val();
            var end =  $('#DiscountEnd').val();
            var minAge =  $('#DiscountAge').val();
            var type =  $('#DiscountTypeSelector0').val();
            var value =  $('#DiscountValue').val();
            var company =  $('#companySelector0').val();
            if(canSubmitForm()) {
                hideCustom();
                callAjax('php/discounts/newDiscount.php', {
                    code: code,
                    start: start,
                    end: end,
                    minAge: minAge,
                    description: description,
                    type: type,
                    value: value,
                    company: company
                }, 'html', function () {
                    showInfo('Information', 'Vous avez bien créé une nouveau rabais!');
                    loadDiscounts(reload);
                });
            }

        })
    });
}
