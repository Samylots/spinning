/**
 * Created by administrator on 2016-04-05.
 */
var actualMemberIdPurchase = null;
var selectedPeriodForSubscription = [];
var updateSubscriptionSub;

function loadPurchases(callback){
    callback = callback ? callback : reload;
    selectOption('achats');
    toggleLoadingUp();
    callAjax('php/purchases/getPurchases.php', {},'html', callback);
}

function loadMemberPurchases(memberId, callback){
    actualMemberIdPurchase = memberId;
    callback = callback ? callback : reload;
    selectOption('Achats');
    toggleLoadingUp();
    callAjax('php/purchases/getPurchases.php', {memberId:memberId},'html', callback);
}

function applyDiscount(){
    var code = $("#discountCode").val();
    if(code === ''){
        showInfo('Attention','Vous devez entrer un code avant de pouvoir l\'appliquer!');
    }
}

function buyCard(memberId){
    callAjax('php/purchases/cardForm.php',{},'html',function(form){
        customModal('Achat d\'une carte',form,'Acheter','Annuler',function(){
            var discountCode = $('#discountCode').val();
            var sessionId = $('#sessionSelector').val();
            var packageId = $('#packageSelector').val();
            callAjax('php/purchases/purchaseCard.php',{
                sessionId: sessionId,
                packagesId:packageId,
                code:discountCode,
                memberId:memberId
            }, 'html',function(){
                hideCustom();
                reloadPurchases();
                showInfo('Succès','Vous avez bien acheté une carte!');
            })
        },null,function(){
            $("#sessionSelector").on("change", changeCards);
            $("#packageSelector").on("change", updateSub);
            $("#discountCode").on("change", updateDiscount);

            function changeCards(){
                var sessionId = $("#sessionSelector").val();
                callAjax("php/purchases/getCardsOptions.php", {sessionId: sessionId}, "html", function (html) {
                    $('#packageSelector').html(html).promise().done(function(){
                        console.log('cards charged');
                        updateSub();
                    });
                });
            }

            function updateSub(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                if(packageType) {
                    enableCustomButton();
                    callAjax("php/purchases/getSubTotal.php", {
                        sessionId: sessionId,
                        packagesType: packageType
                    }, "html", function (html) {
                        console.log('subtotal');
                        $('#subTot').html(html).promise().done(updateDiscount);
                    });
                }else{
                    clearValues();
                }
            }

            function updateDiscount(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                var code = $("#discountCode").val();
                if(code !== '') {
                    callAjax("php/purchases/getDiscountValue.php", {
                        sessionId: sessionId,
                        packagesType: packageType,
                        code: code
                    }, "html", function (html) {
                        console.log('discount');
                        $('#discount').html(html).promise().done(updateTotal);
                    },function(){
                        $("#discountCode").val('');
                        $('#discount').html('0.00$');
                        updateTotal();
                    });
                }else{
                    $('#discount').html('0.00$');
                    updateTotal();
                }
            }

            function updateTotal(){
                $.when(updateTaxes()).done(function(){
                    var sessionId = $("#sessionSelector").val();
                    var packageType = $("#packageSelector").val();
                    var code = $("#discountCode").val();
                    callAjax("php/purchases/getTotal.php", {sessionId: sessionId, packagesType:packageType, code:code}, "html", function (html) {
                        console.log('total');
                        $('#total').html(html);
                    });

                });
            }

            function updateTaxes(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                var code = $("#discountCode").val();
                callAjax("php/purchases/getTaxes.php", {sessionId: sessionId, packagesType:packageType, code:code}, "html", function (html) {
                    console.log('taxes');
                    $('#taxes').html(html);
                });
            }

            function clearValues(){
                $('#subTot').html('0.00$');
                $('#discount').html('0.00$');
                $('#taxes').html('0.00$');
                $('#total').html('0.00$');
                disableCustomButton();
            }

            changeCards();
        })
    })
}

function buySubscription(memberId){
    callAjax('php/purchases/subscriptionForm.php',{},'html',function(form){
        customModal('Achat d\'un abonnement',form,'Acheter','Annuler',function(){
            var discountCode = $('#discountCode').val();
            var sessionId = $('#sessionSelector').val();
            var packageId = $('#packageSelector').val();
            callAjax('php/purchases/purchaseSubscription.php',{
                sessionId: sessionId,
                packagesId:packageId,
                code:discountCode,
                memberId:memberId,
                selectedPeriods: JSON.stringify(selectedPeriodForSubscription)
            }, 'html',function(){
                hideCustom();
                reloadPurchases();
                showInfo('Succès','Vous avez bien acheté un abonnement!');
            })
        },null,function(){
            $("#sessionSelector").on("change", subscriptionSessionChanged);
            $("#packageSelector").on("change", updateSchedule);
            $("#discountCode").on("change", updateSubscriptionDiscount);

            function subscriptionSessionChanged(){
                var sessionId = $("#sessionSelector").val();
                callAjax("php/purchases/getSubscriptionsOptions.php", {sessionId: sessionId}, "html", function (html) {
                    $('#packageSelector').html(html).promise().done(function(){
                        updateSchedule();
                    });
                });
            }

            function updateSchedule(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                callAjax('php/purchases/getSessionSchedule.php',{sessionId:sessionId, packageId:packageType},'html',function(html){
                    $('#scheduleSubscription').html(html);
                });
                selectedPeriodForSubscription = [];
                updateSubscriptionSub();
            }

            updateSubscriptionSub = function(){
                console.log(selectedPeriodForSubscription);
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                if(packageType) {
                    enableCustomButton();
                    callAjax("php/purchases/getSubTotal.php", {
                        sessionId: sessionId,
                        packagesType: packageType,
                        selectedPeriods: JSON.stringify(selectedPeriodForSubscription)
                    }, "html", function (html) {
                        console.log('subtotal');
                        $('#subTot').html(html).promise().done(updateSubscriptionDiscount);
                    });
                }else{
                    clearValues();
                }
            };

            function updateSubscriptionDiscount(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                var code = $("#discountCode").val();
                if(code !== '') {
                    callAjax("php/purchases/getDiscountValue.php", {
                        sessionId: sessionId,
                        packagesType: packageType,
                        code: code
                    }, "html", function (html) {
                        console.log('discount');
                        $('#discount').text(html).promise().done(updateTotal);
                    },function(){
                        $("#discountCode").val('');
                        $('#discount').html('0.00$');
                        updateTotal();
                    });
                }else{
                    $('#discount').html('0.00$');
                    updateTotal();
                }
            }

            function updateTotal(){
                $.when(updateTaxes()).done(function(){
                    var sessionId = $("#sessionSelector").val();
                    var packageType = $("#packageSelector").val();
                    var code = $("#discountCode").val();
                    callAjax("php/purchases/getTotal.php", {
                        sessionId: sessionId,
                        packagesType:packageType,
                        code:code,
                        selectedPeriods: JSON.stringify(selectedPeriodForSubscription)
                    }, "html", function (html) {
                        console.log('total');
                        $('#total').html(html);
                    });

                });
            }

            function updateTaxes(){
                var sessionId = $("#sessionSelector").val();
                var packageType = $("#packageSelector").val();
                var code = $("#discountCode").val();
                callAjax("php/purchases/getTaxes.php", {
                    sessionId: sessionId,
                    packagesType:packageType,
                    code:code,
                    selectedPeriods: JSON.stringify(selectedPeriodForSubscription)
                }, "html", function (html) {
                    console.log('taxes');
                    $('#taxes').html(html);
                });
            }

            function clearValues(){
                $('#subTot').html('0.00$');
                $('#discount').html('0.00$');
                $('#taxes').html('0.00$');
                $('#total').html('0.00$');
                disableCustomButton();
            }

            subscriptionSessionChanged();
        })
    })
}

function selectPeriod(periodId){
    var index = selectedPeriodForSubscription.indexOf(periodId);
    if(index > -1) {
        selectedPeriodForSubscription.splice(index, 1);
        $('#Period'+periodId).toggleClass('expired');
        updateSubscriptionSub();
    }else{
        var packageId = $('#packageSelector').val();
        selectedPeriodForSubscription.push(periodId);
        callAjax('php/purchases/canSelectPeriod.php',{packageId:packageId, selectedPeriods: JSON.stringify(selectedPeriodForSubscription)},'html',function(){
            $('#Period'+periodId).toggleClass('expired');
            updateSubscriptionSub();
        }, function(){
            index = selectedPeriodForSubscription.indexOf(periodId);
            selectedPeriodForSubscription.splice(index, 1);
            updateSubscriptionSub();
        });
    }
}

function reloadPurchases(){
    if(actualMemberIdPurchase){
        loadMemberPurchases(actualMemberIdPurchase);
    }else{
        loadPurchases();
    }
}

function editPurchasePrice(purchaseId){
    showYesNo('Changement de prix d\'un achat', 'Attention, vous êtes sur le point de modifier le prix AVANT ' +
        'TAXES d\'un achat! <br> Voulez-vous vraiment effectuer cette action?', function(){
        callAjax('php/purchases/editPriceForm.php',{purchaseId:purchaseId},'html',function(html){
            customModal('Changement du prix avant taxes:',html,'Modifier','Annuler',function(){
                var newPrice = $('#newPricePurchase').val();
                if(canSubmitForm() && newPrice) {
                    callAjax('php/purchases/editPriceForm.php', {purchaseId: purchaseId, price:newPrice},'html',function(){
                        callAjax('php/purchases/getPurchase.php',{purchaseId:purchaseId},'html',function(html){
                            replaceContentOf('PurchaseContent' + purchaseId,html);
                        });
                        hideCustom();
                        showInfo('Succès','Vous avex bien changé le prix de l\'achat avec succès!');
                    })
                }
            })
        });
    })
}

function payPurchase(purchaseId){
    showYesNo('Paiement d\'un achat', 'Voulez-vous vraiment marquer cet achat comme payé?', function(){
        callAjax('php/purchases/payPurchase.php',{purchaseId:purchaseId},'html',function(){
            callAjax('php/purchases/getPurchase.php',{purchaseId:purchaseId},'html',function(html){
                replaceContentOf('PurchaseContent' + purchaseId,html);
                showInfo('Succès','Vous avez bien payé l\'achat!');
            })
        })
    });
}

function refundPurchase(purchaseId){
    callAjax('php/purchases/refundPurchase.php',{purchaseId:purchaseId},'html',function(html){
        customModal('Remboursement d\'un achat', html,'Rembourser','Annuler',function(){
            var refundValue = $('#newRefundValuePurchase').val();
            if(canSubmitForm() && refundValue) {
                hideCustom();
                callAjax('php/purchases/refundPurchase.php',{purchaseId:purchaseId, refund:refundValue},'html',function(html) {
                    callAjax('php/purchases/getPurchase.php', {purchaseId: purchaseId}, 'html', function (html) {
                        replaceContentOf('PurchaseContent' + purchaseId, html);
                        showInfo('Succès', 'Vous avez bien remboursé le forfait!');
                    })
                })
            }
        })
    })
}

function deletePurchase(purchaseId){
    askBeforeDelete(function(){
        callAjax('php/purchases/deletePurchase.php',{purchaseId:purchaseId},'html',function(){
            hideItem('Purchase'+purchaseId);
        });
    });
}