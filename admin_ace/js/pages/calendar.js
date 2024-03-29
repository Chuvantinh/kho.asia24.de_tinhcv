$(document).ready(function() {
 	
 	//------------- Full calendar  -------------//

 	/* initialize the external events
	-----------------------------------------------------------------*/
	
	$('#external-events div.external-event').each(function() {
	
		// create an Event Object (https://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		// it doesn't need to have a start or end
		var eventObject = {
			title: $.trim($(this).text()) // use the element's text as the event title
		};
		
		// store the Event Object in the DOM element so we can get to it later
		$(this).data('eventObject', eventObject);
		
		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 999,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
		
	});

	$(function () {
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		//calendar example
		$('#calendar').fullCalendar({
			//isRTL: true,
			//theme: true,
			header: {
				left: '',
				center: 'title,today,prev,next,month,agendaWeek,agendaDay',
				right: ''
			},
			firstDay: 1,
			dayNamesShort: ['Sunday', 'Monday', 'Tuesday', 'Wednesday',
 'Thursday', 'Friday', 'Saturday'],
			buttonText: {
	        	prev: '<i class="icon24 i-arrow-left-7"></i>',
	        	next: '<i class="icon24 i-arrow-right-8"></i>',
	        	today:'<i class="icon24 i-home-6"></i>'
	    	},
			editable: true,
			droppable: true, // this allows things to be dropped onto the calendar !!!
			drop: function(date, allDay) { // this function is called when something is dropped
			
				// retrieve the dropped element's stored Event Object
				var originalEventObject = $(this).data('eventObject');
				
				// we need to copy it, so that multiple events don't have a reference to the same object
				var copiedEventObject = $.extend({}, originalEventObject);
				
				// assign it the date that was reported
				copiedEventObject.start = date;
				copiedEventObject.allDay = allDay;
				
				// render the event on the calendar
				// the last `true` argument determines if the event "sticks" (https://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
				$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
				$(this).remove();
			},
			events: [
				{
					title: 'All Day Event',
					start: new Date(y, m, 1)
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d-5),
					end: new Date(y, m, d-2)
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d-3, 16, 0),
					allDay: false
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d+4, 16, 0),
					allDay: false
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false,
					color: '#25a7e8',
					borderColor: '#0d7fb8'
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d+1, 19, 0),
					end: new Date(y, m, d+1, 22, 30),
					allDay: false,
					color: '#d8605f',
					borderColor: '#b72827'
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'https://google.com/'
				}
			],
			eventColor: '#72c380',
			eventBorderColor: '#379e49'
		});
	});
 	
});