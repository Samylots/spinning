/**
 * Created by administrator on 2016-03-22.
 */

var lastSelectedDay;
var lastEndHour;

function main(callback){
    loadSchedules(function(html){
        $.when(setContent(html)).done(callback);
    });
}


function loadSchedules(callback){
    callback = callback ? callback : reload;
    selectOption('Horaire');
    callAjax('php/schedules/showSchedule.php', {id: get('session')},'html', callback);
}

function editPeriod(id){
    callAjax('php/schedules/editPeriod.php', {id:get('session'), periodId:id}, 'html', function(html){
        customModal('Modifier une période:', html ,'Enregistrer','Annuler',function(){
            var start = $('#NewPeriodStart').val();
            var end =  $('#NewPeriodEnd').val();
            var day = $('#DaySelector').val();
            var activity = $('#ActivitySelector').val();
            var startTime =  moment(start, "HH : mm").toDate();
            var endTime =  moment(end, "HH : mm").toDate();
            var places = $('#newPeriodsubscriptionPlaces').val();
            if(startTime >= endTime){
                invalid('Veuillez vérifier que l\'heure de fin soit plus tard que l\'heure de début.');
            }else {
                if(canSubmitForm()) {
                    hideCustom();
                    callAjax('php/schedules/editPeriod.php', {id:get('session'), periodId:id, start: start, end: end, day:day, activity:activity, subscriptionPlaces:places}, 'html', function () {
                        showInfo('Information', 'Les modifications de l\'activité ont bien été enregistrées!');
                        loadSchedules(setContent);
                    });
                }
            }
        }, function(){
            actualScheduleId = id;
            if(isEditing){
                showYesNo('Modifications', 'Voulez-vous vraiment annuler vos modifications?',cancellingEditSchedule);
            }else {
                cancellingEditSchedule();
            }
        });
    },function(){
        showInfo('Erreur', 'Désolé, il est présentement impossible de modifier la période souhaité');
    });
}

function deletePeriod(id){
    askBeforeDelete(function(){
        hideCustom();
        callAjax('php/schedules/deletePeriod.php', {id:get('session'), periodId: id},'html');
        hideItem('Period' + id);
    });
}

function cancellingEditSchedule(){
    isEditing = false;
    hideCustom();
}

function newPeriod(){
    callAjax('php/schedules/newPeriod.php', {id:get('session')}, 'html', function(html){
        customModal('Nouvelle période:',html,'Créer','Annuler',function(){
            var start = $('#PeriodStart').val();
            var end =  $('#PeriodEnd').val();
            var day = $('#DaySelector').val();
            var activity = $('#ActivitySelector').val();
            var startTime =  moment(start, "HH : mm").toDate();
            var endTime =  moment(end, "HH : mm").toDate();
            var places = $('#newPeriodsubscriptionPlaces').val();
            if(startTime >= endTime){
                invalid('Veuillez vérifier que l\'heure de fin soit plus tard que l\'heure de début.');
            }else {
                if(canSubmitForm()) {
                    callAjax('php/schedules/newPeriod.php', {id:get('session'),start: start, end: end, day:day, activity:activity,subscriptionPlaces:places}, 'html', function () {
                        hideCustom();
                        showInfo('Information', 'Vous avez bien créé une nouvelle période!');
                        loadSchedules();
                    });
                }
            }
        },null,function(){
            var daySelector = $('#DaySelector');
            var startSelector = $('#PeriodStart');
            var endSelector = $('#PeriodEnd');
            if(lastSelectedDay){
                daySelector.val(lastSelectedDay);
            }
            daySelector.change(function(){
                lastSelectedDay = $(this).val();
            });
            daySelector.on('click',function(){
                startSelector.val("07 : 00");
                endSelector.val("07 : 00");
            });
            if(lastEndHour){
                startSelector.val(lastEndHour);
                endSelector.val(lastEndHour);
            }
            startSelector.on( "hourChanged", function( event, hour ) {
                endSelector.val(hour);
            });
            endSelector.on( "hourChanged", function( event, hour ) {
                lastEndHour = hour;
            });
        });

    });
}
