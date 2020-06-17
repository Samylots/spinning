/*
 * Requires jquery ui
 */

(function($){

    function selectCurrentWeek() {
        window.setTimeout(function () {
            var t = $(this).find('.ui-datepicker-current-day a');
			t= t.closest('tr');
			t.find('td>a').addClass('ui-state-active');
        }, 1);
		
    }
	function onSelect(dateText, inst) { 
        var date = $(this).datepicker('getDate');
        startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
        endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
        var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
        $(this).trigger('weekselected',[
			$.datepicker.formatDate( dateFormat, startDate, inst.settings ),
			$.datepicker.formatDate( dateFormat, endDate, inst.settings )
		]);
    }
	var reqOpt = {
        dateFormat:'yy-mm-dd',
		onSelect:onSelect,
		showOtherMonths: true,
        selectOtherMonths: true,
        dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
        dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
        dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
        monthNames: [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
        monthNamesShort: [ "Jan", "Fev", "Mars", "Avr", "Mai", "Juin", "Juill", "Août", "Sept", "Oct", "Nov", "Déc" ],
        numberOfMonths: 1
	};
    $.fn.weekpicker = function(options){
		var $this = this;
		$this.datepicker(reqOpt);
		//events
        $('.ui-datepicker').on({
            mousemove: function() {
                $(this).find('td a').addClass('ui-state-hover');
            },
            mouseleave: function() {
                $(this).find('td a').removeClass('ui-state-hover');
            }
        }, 'tr');
	};
})(jQuery);