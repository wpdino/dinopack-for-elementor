(function ($) {
	'use strict';

	function formatNumber(value, useSeparator) {
		var number = Math.round(value);
		if (!useSeparator) {
			return String(number);
		}
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function animateCounter($counter) {
		if ($counter.data('dinopackAnimated')) {
			return;
		}

		var start = parseFloat($counter.data('start')) || 0;
		var end = parseFloat($counter.data('end')) || 0;
		var duration = parseInt($counter.data('duration'), 10) || 2000;
		var useSeparator = $counter.data('separator') === 1 || $counter.data('separator') === '1';
		var $number = $counter.find('.dinopack-counter-number');
		var startTime = null;

		$counter.data('dinopackAnimated', true);

		function step(timestamp) {
			if (!startTime) {
				startTime = timestamp;
			}

			var progress = Math.min((timestamp - startTime) / duration, 1);
			var eased = 1 - Math.pow(1 - progress, 3);
			var current = start + (end - start) * eased;

			$number.text(formatNumber(current, useSeparator));

			if (progress < 1) {
				window.requestAnimationFrame(step);
			} else {
				$number.text(formatNumber(end, useSeparator));
			}
		}

		window.requestAnimationFrame(step);
	}

	function initCounter($scope) {
		var $counter = $scope.hasClass('dinopack-counter') ? $scope : $scope.find('.dinopack-counter');
		if (!$counter.length) {
			return;
		}

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						animateCounter($(entry.target));
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.2 });

			$counter.each(function () {
				observer.observe(this);
			});
		} else {
			$counter.each(function () {
				animateCounter($(this));
			});
		}
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-counter.default', initCounter);
	});
})(jQuery);
