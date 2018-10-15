// console.log(jsCalendar.onDateClick(ss))
// jsCalendar.prototype.onDateClick(function(data){
// 	console.log('onDateClick:',data)
//   // On day click
// });

// jsCalendar.onDateClick(function(event, date){
// 	console.log('onDateClick:',event,date)
//   // On day click
// });

// jsCalendar.onMonthChange(function(event, date){
//   // On month change
// 	console.log('onMonthChange:',event,date)

// });
var calendar = jsCalendar.new("#my-calendar");
// Get the inputs
var print_date = document.getElementById("my-input-a");
var print_month = document.getElementById("my-input-b");
// When the user clicks on a date
calendar.onDateClick(function(event, date){
	console.log(date)
	print_date.value = jsCalendar.tools.dateToString(date, 'DAY, DD MONTH YYYY', 'en');
});
// When a user change the month
calendar.onMonthChange(function(event, date){
	console.log(date)

	print_month.value = jsCalendar.tools.dateToString(date, 'MONTH YYYY', 'en');
});
