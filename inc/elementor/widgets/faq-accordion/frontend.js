(function ($) {
	'use strict';

	function setPanelState($item, isOpen) {
		var $button = $item.find('.dinopack-faq-question');
		var $panel = $item.find('.dinopack-faq-answer');

		$item.toggleClass('is-active', isOpen);
		$button.attr('aria-expanded', isOpen ? 'true' : 'false');
		$panel.attr('aria-hidden', isOpen ? 'false' : 'true');
	}

	function initFaqAccordion($scope) {
		var $accordion = $scope.hasClass('dinopack-faq-accordion')
			? $scope
			: $scope.find('.dinopack-faq-accordion');

		if (!$accordion.length) {
			return;
		}

		var allowMultiple = $accordion.data('allow-multiple') === 1 || $accordion.data('allow-multiple') === '1';

		$accordion.find('.dinopack-faq-item').each(function () {
			var $item = $(this);
			setPanelState($item, $item.hasClass('is-active'));
		});

		$accordion.off('click.dinopackFaq').on('click.dinopackFaq', '.dinopack-faq-question', function () {
			var $button = $(this);
			var $item = $button.closest('.dinopack-faq-item');
			var isOpen = $item.hasClass('is-active');

			if (!allowMultiple && !isOpen) {
				$accordion.find('.dinopack-faq-item.is-active').not($item).each(function () {
					setPanelState($(this), false);
				});
			}

			setPanelState($item, !isOpen);
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-faq-accordion.default', initFaqAccordion);
	});
})(jQuery);
