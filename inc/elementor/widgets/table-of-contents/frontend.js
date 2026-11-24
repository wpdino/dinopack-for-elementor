(function ($) {
	'use strict';

	function slugify(text) {
		return String(text || '')
			.toLowerCase()
			.replace(/[^\w\s-]/g, '')
			.trim()
			.replace(/\s+/g, '-')
			.replace(/-+/g, '-')
			.substring(0, 80);
	}

	function ensureHeadingId(heading, index) {
		if (heading.id) {
			return heading.id;
		}

		var base = slugify(heading.textContent) || ('section-' + (index + 1));
		var id = 'dinopack-toc-' + base;
		var unique = id;
		var n = 2;

		while (document.getElementById(unique)) {
			unique = id + '-' + n;
			n += 1;
		}

		heading.id = unique;
		return unique;
	}

	function getTargetId($link) {
		var dataId = $link.attr('data-target-id');
		if (dataId) {
			return dataId;
		}

		var href = $link.attr('href') || '';
		var hashIndex = href.indexOf('#');
		if (hashIndex === -1) {
			return '';
		}

		return href.slice(hashIndex + 1);
	}

	function bindInteractions($toc, links) {
		var smoothScroll = $toc.data('smooth-scroll') === 1 || $toc.data('smooth-scroll') === '1';
		var highlightActive = $toc.data('highlight-active') === 1 || $toc.data('highlight-active') === '1';
		var offset = parseInt($toc.data('offset'), 10) || 80;
		var $list = $toc.find('.dinopack-toc-list');

		$list.off('click.dinopackToc').on('click.dinopackToc', 'a', function (e) {
			var $link = $(this);
			var targetId = getTargetId($link);

			if (!targetId) {
				return;
			}

			var target = document.getElementById(targetId);
			if (!target) {
				return;
			}

			e.preventDefault();

			var top = $(target).offset().top - offset;

			if (smoothScroll) {
				$('html, body').stop(true).animate({ scrollTop: top }, 400);
			} else {
				window.scrollTo(0, top);
			}

			if (history.replaceState) {
				history.replaceState(null, '', '#' + targetId);
			}
		});

		if (highlightActive && links.length) {
			var scrollNs = 'scroll.dinopackToc' + ($toc.closest('.elementor-element').data('id') || $toc.index());

			var onScroll = function () {
				var scrollPos = $(window).scrollTop() + offset + 8;
				var current = null;

				for (var i = 0; i < links.length; i++) {
					if (!links[i].el) {
						continue;
					}
					var top = $(links[i].el).offset().top;
					if (top <= scrollPos) {
						current = links[i];
					} else {
						break;
					}
				}

				links.forEach(function (item) {
					item.$link.toggleClass('is-active', item === current);
				});
			};

			$(window).off(scrollNs).on(scrollNs, onScroll);
			onScroll();
		}

		$toc.off('click.dinopackTocToggle').on('click.dinopackTocToggle', '.dinopack-toc-toggle', function () {
			if (!$toc.hasClass('dinopack-toc-collapse-mobile') || window.innerWidth > 767) {
				return;
			}

			var expanded = $toc.hasClass('is-expanded');
			$toc.toggleClass('is-expanded', !expanded);
			$(this).attr('aria-expanded', !expanded ? 'true' : 'false');
		});
	}

	function initAutoMode($toc) {
		var levels = ($toc.data('levels') || 'h2,h3').toString().split(',').map(function (level) {
			return level.trim();
		}).filter(Boolean);

		var containerSelector = $toc.data('container') || 'body';
		var $list = $toc.find('.dinopack-toc-list');
		var $empty = $toc.find('.dinopack-toc-empty');
		var $container = $(containerSelector);

		if (!$container.length) {
			$container = $('body');
		}

		var $headings = $container.find(levels.join(',')).filter(function () {
			var $el = $(this);
			return !$el.closest('.dinopack-toc, .elementor-editor-element-settings, .elementor-element-overlay').length;
		});

		$list.empty();

		if (!$headings.length) {
			$empty.prop('hidden', false);
			bindInteractions($toc, []);
			return;
		}

		$empty.prop('hidden', true);

		var links = [];

		$headings.each(function (index) {
			var heading = this;
			var id = ensureHeadingId(heading, index);
			var tag = heading.tagName.toLowerCase();
			var text = $.trim($(heading).text());

			if (!text) {
				return;
			}

			var $li = $('<li/>', {
				'class': 'dinopack-toc-level-' + tag
			});

			var $link = $('<a/>', {
				href: '#' + id,
				text: text,
				'data-target-id': id
			});

			$li.append($link);
			$list.append($li);
			links.push({
				id: id,
				$link: $link,
				el: heading
			});
		});

		bindInteractions($toc, links);
	}

	function initManualMode($toc) {
		var $list = $toc.find('.dinopack-toc-list');
		var $empty = $toc.find('.dinopack-toc-empty');
		var $items = $list.children('li');
		var links = [];

		if (!$items.length) {
			$empty.prop('hidden', false);
			bindInteractions($toc, []);
			return;
		}

		$empty.prop('hidden', true);

		$items.each(function () {
			var $link = $(this).find('a').first();
			if (!$link.length) {
				return;
			}

			var targetId = getTargetId($link);
			var el = targetId ? document.getElementById(targetId) : null;

			links.push({
				id: targetId,
				$link: $link,
				el: el
			});
		});

		bindInteractions($toc, links);
	}

	function initToc($scope) {
		var $toc = $scope.hasClass('dinopack-toc') ? $scope : $scope.find('.dinopack-toc');

		if (!$toc.length) {
			return;
		}

		var mode = $toc.data('mode') || 'auto';

		if (mode === 'manual') {
			initManualMode($toc);
		} else {
			initAutoMode($toc);
		}
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-table-of-contents.default', initToc);
	});
})(jQuery);
