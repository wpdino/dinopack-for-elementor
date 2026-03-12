(function ($) {
	'use strict';
	$(document).on('click', 'a[data-dinopack-smooth]', function (e) {
		var href = $(this).attr('href');
		if (!href || href.indexOf('#') !== 0) return;
		var id = href.slice(1);
		if (!id) return;
		var $el = document.getElementById(id);
		if (!$el) return;
		e.preventDefault();
		$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
	});
})(jQuery);
