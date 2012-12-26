$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
	$( "#datepicker" ).datepicker({
	beforeShowDay: function(date) {
		//Sunday
		if(date.getDay() == 0) {
			return [true,"date-sunday"];
		//Saturday   
		} else if(date.getDay() == 6){
			return [true,"date-saturday"];
		//day
		} else {
			return [true];
		}
	},changeMonth: 'true'
	,changeYear: 'true'
	,onSelect: function(dateText, inst){
		var dates = dateText.split('/');
		$("*[name=year]").val(dates[0]);
		$("*[name=month]").val(dates[1]);
		$("*[name=day]").val(dates[2]);
	}
	});
});