$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
	$( "#datepicker" ).datepicker({
	beforeShowDay: function(date) {
		if(date.getDay() == 0) {
			return [true,"date-sunday"]; 
		} else if(date.getDay() == 6){
			return [true,"date-saturday"];
		} else {
			return [true];
		}
	},changeMonth: 'true'
	,changeYear: 'true'
	,onSelect: function(dateText, inst){
		setDate(dateText);
	},
	showButtonPanel: true,
	beforeShow: showAdditionalButton,       
	onChangeMonthYear: showAdditionalButton
	});
	
	$("#datepicker").blur( function() {
		var dateText = $(this).val();
		setDate(dateText);
	});

});

var showAdditionalButton = function (input) {
	setTimeout(function () {
		var buttonPane = $(input)
				 .datepicker("widget")
				 .find(".ui-datepicker-buttonpane");
		var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
		btn
				.unbind("click")
				.bind("click", function () {
					$.datepicker._clearDate(input);
					$("*[name=year]").val("");
					$("*[name=month]").val("");
					$("*[name=day]").val("");
				});
		btn.appendTo(buttonPane);
	}, 1);
};
function setDate(dateText){
var dates = dateText.split('/');
$("*[name=year]").val(dates[0]);
$("*[name=month]").val(dates[1]);
$("*[name=day]").val(dates[2]);
}