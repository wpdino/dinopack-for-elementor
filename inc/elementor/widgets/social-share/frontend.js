(function ($) {
	'use strict';

	function copyToClipboard(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var $temp = $('<textarea>');
			$temp.val(text).css({
				position: 'fixed',
				left: '-9999px'
			}).appendTo('body');
			$temp[0].select();

			try {
				document.execCommand('copy');
				$temp.remove();
				resolve();
			} catch (err) {
				$temp.remove();
				reject(err);
			}
		});
	}

	function initSocialShare($scope) {
		var $wrapper = $scope.hasClass('dinopack-social-share')
			? $scope
			: $scope.find('.dinopack-social-share');

		if (!$wrapper.length) {
			return;
		}

		var successText = $wrapper.data('copy-success') || 'Copied!';

		$wrapper.off('click.dinopackShare').on('click.dinopackShare', '[data-share="copy"]', function (e) {
			e.preventDefault();

			var $btn = $(this);
			var url = $btn.data('share-url') || window.location.href;
			var $label = $btn.find('.dinopack-share-label');
			var originalLabel = $label.length ? $label.text() : '';

			copyToClipboard(url).then(function () {
				$btn.addClass('is-copied');

				if ($label.length) {
					$label.text(successText);
				} else {
					$btn.attr('aria-label', successText);
				}

				window.setTimeout(function () {
					$btn.removeClass('is-copied');
					if ($label.length && originalLabel) {
						$label.text(originalLabel);
					}
				}, 1800);
			});
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-social-share.default', initSocialShare);
	});
})(jQuery);
