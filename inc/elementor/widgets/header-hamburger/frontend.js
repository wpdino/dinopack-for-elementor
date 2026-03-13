(function ($) {
	'use strict';
	$(document).on('click', '[data-dinopack-target]', function () {
		var $btn = $(this);
		var target = $btn.attr('data-dinopack-target');
		var bodyClass = $btn.attr('data-dinopack-body-class');
		if (!target) return;
		var $panel = $(target);
		if (!$panel.length) return;
		var isExpanded = $btn.attr('aria-expanded') === 'true';
		$panel.toggleClass('is-open');
		$btn.attr('aria-expanded', !isExpanded);
		if (bodyClass) {
			$('body').toggleClass(bodyClass, !isExpanded);
		}
	});
})(jQuery);
