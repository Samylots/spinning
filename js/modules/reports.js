/**
 * Created by administrator on 2016-03-16.
 */

function showAllReports(callback){
    callback = callback ? callback : reload;
    selectOption('Rapports');
    callAjax('php/reports/getFullReport.php', {},'html', callback);
}

function showActiveCardsReports(callback){
    callback = callback ? callback : reload;
    callAjax('php/reports/getActiveCards.php', {},'html', callback);
}

function showActiveSubscriptions(callback){
    callback = callback ? callback : reload;
    callAjax('php/reports/getActiveSubcriptions.php', {},'html', callback);
}

function showBetweenDatesCardsReports(callback){
    callback = callback ? callback : reload;
    askDates(function(){
        var start = $('#reportStart').val();
        var end = $('#reportEnd').val();
        callAjax('php/reports/getBetweenCards.php', {start:start,end:end},'html', callback);
    });
}

function showBetweenDatesSubscriptonsReports(callback){
    callback = callback ? callback : reload;
    askDates(function(){
        var start = $('#reportStart').val();
        var end = $('#reportEnd').val();
        callAjax('php/reports/getBetweenSubscriptions.php', {start:start,end:end},'html', callback);
    });
}

function showRefundsReports(callback){
    callback = callback ? callback : reload;
    callAjax('php/reports/getRefunds.php', {},'html', callback);
}

function showUnpaidReports(callback){
    callback = callback ? callback : reload;
    callAjax('php/reports/getUnpaid.php', {},'html', callback);
}

function askDates(onTrue, onFalse){
    customModal('Dates:','Date de début:'+
        '<input name="reportStart" id="reportStart" type="date" class="start" value="" readonly placeholder="Date de début" required>'+
        'Date de fin:'+
        '<input name="reportEnd" id="reportEnd" type="date" class="end" value="" readonly placeholder="Date de fin" required>',
        'Afficher','Annuler',function(){
            var start = $('#reportStart').val();
            var end = $('#reportEnd').val();
            if(!start || !end){
                showInfo('Attention!','Veuillez bien définir les deux dates!');
            }else{
                hideCustom();
                onTrue();
            }
        },onFalse,function(){
           initDatePickers('#reportStart','#reportEnd',1);
        });
}
