(function ($) {
	'use strict';

	// Remove no-transition class on DOM ready so panel can animate when opened
	$(function () {
		$('.dinopack-offset-side-panel--no-transition').removeClass('dinopack-offset-side-panel--no-transition');
	});

	// Close panel: remove is-open and body class
	function closePanel($panel) {
		var bodyClass = $panel.attr('data-body-class');
		$panel.removeClass('is-open');
		if (bodyClass) {
			$('body').removeClass(bodyClass);
		}
	}

	// Overlay click: close this panel
	$(document).on('click', '.dinopack-offset-side-panel__overlay', function () {
		var $panel = $(this).closest('.dinopack-offset-side-panel');
		if ($panel.length) {
			closePanel($panel);
		}
	});

	// Close button click
	$(document).on('click', '.dinopack-offset-side-panel__close', function (e) {
		e.preventDefault();
		var $panel = $(this).closest('.dinopack-offset-side-panel');
		if ($panel.length) {
			closePanel($panel);
		}
	});

	// ESC key: close any open panel
	$(document).on('keydown', function (e) {
		if (e.keyCode === 27) {
			$('.dinopack-offset-side-panel.is-open').each(function () {
				closePanel($(this));
			});
		}
	});
})(jQuery);
