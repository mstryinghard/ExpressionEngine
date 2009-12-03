﻿/*!! Hebrew initialisation for the UI Datepicker extension. */
/*!! Written by Amir Hardon (ahardon at gmail dot com). */
jQuery(function($){
	$.datepicker.regional['he'] = {
		clearText: 'נקה', clearStatus: '',
		closeText: 'סגור', closeStatus: '',
		prevText: '&#x3c;הקודם', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'הבא&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'היום', currentStatus: '',
		monthNames: ['ינואר','פברואר','מרץ','אפריל','מאי','יוני',
		'יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],
		monthNamesShort: ['1','2','3','4','5','6',
		'7','8','9','10','11','12'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],
		dayNamesShort: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
		dayNamesMin: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
		dayStatus: 'DD', dateStatus: 'DD, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0, 
		initStatus: '', isRTL: true};
	$.datepicker.setDefaults($.datepicker.regional['he']);
});
