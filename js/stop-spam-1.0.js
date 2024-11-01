/*
Stop-spam plugin
No spam in comments. No captcha.
wordpress.org/plugins/stop-spam/
*/

"use strict";
(function() {
	function stop_spam_init() {

		var i,
			len,
			elements,
			answer = '',
			current_year = new Date().getFullYear(),
			dynamic_control;

		elements = document.querySelectorAll('.stopspam-group');
		len = elements.length;
		for (i = 0; i < len; i++) { // hide inputs from users
			elements[i].style.display = 'none';
		}

		elements = document.querySelectorAll('.stopspam-control-a');
		if ((elements) && (elements.length > 0)) { // get the answer
			answer = elements[0].value;
		}

		elements = document.querySelectorAll('.stopspam-control-q');
		len = elements.length;
		for (i = 0; i < len; i++) { // set answer into other input instead of user
			elements[i].value = answer;
		}
		
		// clear value of the empty input because some themes are adding some value for all inputs
		elements = document.querySelectorAll('.stopspam-control-e');
		len = elements.length;
		for (i = 0; i < len; i++) {
			elements[i].value = '';
		}

		//dynamic_control = '<input type="text" name="stpspm-d" class="stopspam-control stopspam-control-d" value="' + current_year + '" />';
		dynamic_control = document.createElement('input');
		dynamic_control.setAttribute('type', 'hidden');
		dynamic_control.setAttribute('name', 'stpspm-d');
		dynamic_control.setAttribute('class', 'stopspam-control stopspam-control-d');
		dynamic_control.setAttribute('value', current_year);

		// add input for every comment form if there are more than 1 form with IDs: comments, respond or commentform
		elements = document.querySelectorAll('form');
		len = elements.length;
		for (i = 0; i < len; i++) {
			if ( (elements[i].id === 'comments') || (elements[i].id === 'respond') || (elements[i].id === 'commentform') ) {
				var class_index = elements[i].className.indexOf('stop-spam-form-processed');
				if ( class_index == -1 ) { // form is not yet js processed
					elements[i].appendChild(dynamic_control);
					elements[i].className = elements[i].className + ' stop-spam-form-processed';
				}
			}
		}
	}

	if (document.addEventListener) {
		document.addEventListener('DOMContentLoaded', stop_spam_init, false);
	}

	// set 1 second timeout for having form loaded and adding support for browsers which does not support 'DOMContentLoaded' listener
	setTimeout(function () {
		stop_spam_init();
	}, 1000);

})();