/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

var Nella = Nella || {};

Nella.addError = function(elem, message) {
	if (elem.focus) {
		elem.focus();
	}
	if (message) {
		$(elem).after($("<span>", {
			class: 'error', 
			id: elem.id + '_error', 
			html: message
		}));
	}
};

Nella.removeError = function(elem) {
	$('#' + elem.id + '_error').remove();
};

Nella.processPayload = function(payload) {
	if (payload.redirect) {
		window.location = payload.redirect;
	}
	if (payload.snippets) {
		for (var i in payload.snippets) {
			$("#" + i).html(payload.snippets[i]);
		}
	}
};

//-------------------- Remove next code on production server --------------------------
jQuery.ajaxSetup({
	error: function (xhr) {
		var errorWin = window.open('', 'Error');
		errorWin.document.write(xhr.responseText);
		return false;
	}
});
//-------------------------------------------------------------------------------------

jQuery.fn.nellaForm = function() {
	if (this.length < 1) {
		return;
	}
	
	var form = this[0];
	form.noValidate = true; // disable browser HTML5 validation
	
	this.live('submit', function(event) {
		return Nette.validateForm(event.target || event.srcElement);
	});
	
	this.live('click', function(event) {
		var target = event.target || event.srcElement;
		this['nette-submittedBy'] = (target.type in {submit:1, image:1}) ? target.name : null;
	});
	
	this.find('select, textarea, input').live('keyup change paste cut', function(event) {
		Nette.validateControl(event.target || event.srcElement);
	});
	
	for (var i = 0; i < form.elements.length; i++) {
		Nette.toggleControl(form.elements[i], null, true);
	}
	
	if (/MSIE/.exec(navigator.userAgent)) {
		var labels = {};
		for (i = 0, elms = form.getElementsByTagName('label'); i < elms.length; i++) {
			labels[elms[i].htmlFor] = elms[i];
		}

		for (i = 0, elms = form.getElementsByTagName('select'); i < elms.length; i++) {
			$(elms[i]).live('mousewheel', function() { return false }); // prevents accidental change in IE
			if (labels[elms[i].htmlId]) {
				$(labels[elms[i].htmlId]).live('click', function() { document.getElementById(this.htmlFor).focus(); return false }); // prevents deselect in IE 5 - 6
			}
		}
	}
};

/*jQuery.fn.nellaAjax = function() {
	
};*/

jQuery.fn.nellaAjaxSnippet = function() {
	$(this).live('click', function(event) {
		event.preventDefault();
		$.getJSON(a.href, function(data) {
			Nella.processPayload(data);
			if (window.history && window.history.pushState) {
				window.history.pushState(null, document.title, this.attr('action'));
			}
		});
	});
};

jQuery.fn.nellaAjaxSnippetForm = function() {
	$(this).live('submit', function(event) {
		event.preventDefault();
		this.find('input[type=submit]').attr('disbled', true).addClass('loading');
		$.post(this.attr('action'), this.serialize(), function(data) {
			Nella.processPayload(data);
			this.find('input[type=submit]').removeClass('loading').removeAttr('disabled');
			if (window.history && window.history.pushState) {
				window.history.pushState(null, document.title, this.attr('action'));
			}
		});
		return false;
	});
};

$(document).ready(function() {
	// Forms
	$('form').nellaForm();
	$('form[data-nella-ajax-snippet]').nellaAjaxSnippetForm();
	// Datetime (for time and datetime please use: http://trentrichardson.com/examples/timepicker/)
	$('input[type="time"]').each(function() {
		$this = $(this);
		$this.timepicker({ format: $this.attr('data-nella-forms-time') });
	});
	$('input[type="datetime"]').each(function() {
		$this = $(this);
		$this.datetimepicker({ 
			format: $this.attr('data-nella-forms-date'), 
			timeFormat: $this.attr('data-nella-forms-time')
		});
	});
	$('input[type="date"]').each(function() {
		$this = $(this);
		$this.datepicker({ format: $this.attr('data-nella-forms-date') });
	});
	$('[data-nella-confirm]').each(function() {
		$this = $(this);
		$this.bind('click', function() { confirm($this.attr('data-nella-confirm')) });
	});
	$('[data-nella-confirm]').each(function() {
		$this = $(this);
		$this.bind('click', function() { return confirm($this.attr('data-nella-confirm')) });
	});
	
	$('a[data-nella-ajax-snippet]').nellaAjaxSnippet();
});

/**
 * NetteForms - simple form validation.
 *
 * This file is part of the Nette Framework.
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 */

var Nette = Nette || {};


Nette.getValue = function(elem) {
	if (!elem) {
		return null;

	} else if (!elem.nodeName) { // radio
		for (var i = 0, len = elem.length; i < len; i++) {
			if (elem[i].checked) {
				return elem[i].value;
			}
		}
		return null;

	} else if (elem.nodeName.toLowerCase() === 'select') {
		var index = elem.selectedIndex, options = elem.options;

		if (index < 0) {
			return null;

		} else if (elem.type === 'select-one') {
			return options[index].value;
		}

		for (var i = 0, values = [], len = options.length; i < len; i++) {
			if (options[i].selected) {
				values.push(options[i].value);
			}
		}
		return values;

	} else if (elem.type === 'checkbox') {
		return elem.checked;

	} else if (elem.type === 'radio') {
		return Nette.getValue(elem.form.elements[elem.name]);

	} else {
		return elem.value.replace(/^\s+|\s+$/g, '');
	}
};


Nette.validateControl = function(elem, rules, onlyCheck) {
	rules = rules || eval('[' + (elem.getAttribute('data-nette-rules') || '') + ']');
	for (var id = 0, len = rules.length; id < len; id++) {
		var rule = rules[id], op = rule.op.match(/(~)?([^?]+)/);
		rule.neg = op[1];
		rule.op = op[2];
		rule.condition = !!rule.rules;
		var el = rule.control ? elem.form.elements[rule.control] : elem;
		Nella.removeError(el);

		var success = Nette.validateRule(el, rule.op, rule.arg);
		if (success === null) continue;
		if (rule.neg) success = !success;

		if (rule.condition && success) {
			if (!Nette.validateControl(elem, rule.rules, onlyCheck)) {
				return false;
			}
		} else if (!rule.condition && !success) {
			if (el.disabled) continue;
			if (!onlyCheck) {
				Nella.addError(el, rule.msg.replace('%value', $('<span/>',{text:Nette.getValue(el)}).html()));
			}
			return false;
		}
	}
	return true;
};

Nette.validateForm = function(sender) {
	var form = sender.form || sender;
	if (form['nette-submittedBy'] && form.elements[form['nette-submittedBy']] && form.elements[form['nette-submittedBy']].getAttribute('formnovalidate') !== null) {
		return true;
	}
	for (var i = 0; i < form.elements.length; i++) {
		var elem = form.elements[i];
		if (!(elem.nodeName.toLowerCase() in {input:1, select:1, textarea:1}) || (elem.type in {hidden:1, submit:1, image:1, reset: 1}) || elem.disabled || elem.readonly) {
			continue;
		}
		if (!Nette.validateControl(elem)) {
			return false;
		}
	}
	return true;
};

Nette.validators = {
	filled: function(elem, arg, val) {
		return val !== '' && val !== false && val !== null;
	},

	valid: function(elem, arg, val) {
		return Nette.validateControl(elem, null, true);
	},

	equal: function(elem, arg, val) {
		arg = arg instanceof Array ? arg : [arg];
		for (var i = 0, len = arg.length; i < len; i++) {
			if (val == (arg[i].control ? Nette.getValue(elem.form.elements[arg[i].control]) : arg[i])) {
				return true;
			}
		}
		return false;
	},

	minLength: function(elem, arg, val) {
		return val.length >= arg;
	},

	maxLength: function(elem, arg, val) {
		return val.length <= arg;
	},

	length: function(elem, arg, val) {
		if (typeof arg !== 'object') {
			arg = [arg, arg];
		}
		return (arg[0] === null || val.length >= arg[0]) && (arg[1] === null || val.length <= arg[1]);
	},

	email: function(elem, arg, val) {
		return (/^[^@\s]+@[^@\s]+\.[a-z]{2,10}$/i).test(val);
	},

	url: function(elem, arg, val) {
		return (/^.+\.[a-z]{2,6}(\/.*)?$/i).test(val);
	},

	regexp: function(elem, arg, val) {
		var parts = arg.match(/^\/(.*)\/([imu]*)$/);
		if (parts) { try {
			return (new RegExp(parts[1], parts[2].replace('u', ''))).test(val);
		} catch (e) {} }
		return;
	},

	pattern: function(elem, arg, val) {
		return (new Regexp('^(?:' + arg + ')$')).test(val);
	},

	integer: function(elem, arg, val) {
		return (/^-?[0-9]+$/).test(val);
	},

	float: function(elem, arg, val) {
		return (/^-?[0-9]*[.,]?[0-9]+$/).test(val);
	},

	range: function(elem, arg, val) {
		return (arg[0] === null || parseFloat(val) >= arg[0]) && (arg[1] === null || parseFloat(val) <= arg[1]);
	},

	submitted: function(elem, arg, val) {
		return elem.form['nette-submittedBy'] === elem;
	}
};

Nette.validateRule = function(elem, op, arg) {
	var val = Nette.getValue(elem);

	if (elem.getAttribute) {
		if (val === elem.getAttribute('data-nette-empty-value')) { val = ''; }
	}

	if (op.charAt(0) === ':') {
		op = op.substr(1);
	}
	op = op.replace('::', '_');
	return Nette.validators[op] ? Nette.validators[op](elem, arg, val) : null;
};

Nette.toggleForm = function(form) {
	for (var i = 0; i < form.elements.length; i++) {
		if (form.elements[i].nodeName.toLowerCase() in {input:1, select:1, textarea:1, button:1}) {
			Nette.toggleControl(form.elements[i]);
		}
	}
};


Nette.toggleControl = function(elem, rules, firsttime) {
	rules = rules || eval('[' + (elem.getAttribute('data-nette-rules') || '') + ']');
	var has = false;
	for (var id = 0, len = rules.length; id < len; id++) {
		var rule = rules[id], op = rule.op.match(/(~)?([^?]+)/);
		rule.neg = op[1];
		rule.op = op[2];
		rule.condition = !!rule.rules;
		if (!rule.condition) continue;

		var el = rule.control ? elem.form.elements[rule.control] : elem;
		var success = Nette.validateRule(el, rule.op, rule.arg);
		if (success === null) continue;
		if (rule.neg) success = !success;

		if (Nette.toggleControl(elem, rule.rules, firsttime) || rule.toggle) {
			has = true;
			if (firsttime) {
				if (!el.nodeName) { // radio
					for (var i = 0; i < el.length; i++) {
						$(el[i]).bind('click', function(event) { Nette.toggleForm(event.target.form); });
					}
				} else if (el.nodeName.toLowerCase() === 'select') {
					$(el).bind('change', function(event) { Nette.toggleForm(event.target.form); });
				} else {
					$(el).bind('click', function(event) { Nette.toggleForm(event.target.form); });
				}
			}
			for (var id in rule.toggle || []) {
				Nette.toggle(id, success ? rule.toggle[id] : !rule.toggle[id]);
			}
		}
	}
	return has;
};


Nette.toggle = function(id, visible) {
	var $elem = $('#' + id);
	if ($elem) {
		$elem.css('display', visible ? "" : "none");
	}
};
