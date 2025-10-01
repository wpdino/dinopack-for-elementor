/**
 * WPDINO Admin JavaScript
 */
(function($) {
	'use strict';

	// Initialize when document is ready
	$(document).ready(function() {
		WPDinoAdmin.init();
	});

	// Main admin object
	const WPDinoAdmin = {
		
		/**
		 * Initialize admin functionality
		 */
		init: function() {
			this.bindEvents();
			this.initTabs();
			this.initMediaUploader();
			this.initImageSelect();
			this.initFormValidation();
			this.initColorPickers();
			this.showNotices();
		},

		/**
		 * Bind event listeners
		 */
		bindEvents: function() {
			// Tab switching
			$(document).on('click', '.wpdino-tab-btn', this.switchTab);
			
			// Reset settings
			$(document).on('click', '#reset-settings', this.resetSettings);
			
			// Export settings
			$(document).on('click', '#export-settings', this.exportSettings);
			
			// Import settings
			$(document).on('click', '#import-settings', this.triggerImport);
			$(document).on('change', '#import-file', this.importSettings);
			
			// Media uploader
			$(document).on('click', '.wpdino-media-upload', this.openMediaUploader);
			
			// Image field handlers
			$(document).on('click', '.wpdino-image-upload, .wpdino-image-change', this.openImageUploader);
			$(document).on('click', '.wpdino-image-remove', this.removeImage);
			
			// Copy system info
			$(document).on('click', '#copy-system-info', this.copySystemInfo);
			
			// Form submission
			$(document).on('submit', '.wpdino-form', this.handleFormSubmission);
			
			// Input changes for live preview
			$(document).on('change', '.wpdino-input, .wpdino-select, .wpdino-textarea', this.handleInputChange);
			
			// Image select changes
			$(document).on('change', '.wpdino-image-select-group input[type="radio"]', this.handleImageSelectChange);
			
			// Smooth scrolling for anchor links
			$(document).on('click', 'a[href^="#"]', this.smoothScroll);
		},

		/**
		 * Initialize tabs
		 */
		initTabs: function() {
			// Set first tab as active if none are active
			if (!$('.wpdino-tab-btn.active').length) {
				$('.wpdino-tab-btn:first').addClass('active');
			}
			
			// Set corresponding tab content as active
			if (!$('.wpdino-tab-content.active').length) {
				const firstTab = $('.wpdino-tab-btn:first').data('tab');
				$('#tab-' + firstTab).addClass('active');
			}
			
			// Handle browser back/forward with tabs
			if (window.location.hash) {
				const hash = window.location.hash.replace('#', '');
				if ($('#tab-' + hash).length) {
					this.activateTab(hash);
				}
			}
			
			// Handle keyboard navigation
			$('.wpdino-tab-btn').on('keydown', function(e) {
				if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
					e.preventDefault();
					const tabs = $('.wpdino-tab-btn');
					const current = tabs.index(this);
					let next = e.key === 'ArrowRight' ? current + 1 : current - 1;
					
					if (next >= tabs.length) next = 0;
					if (next < 0) next = tabs.length - 1;
					
					tabs.eq(next).focus().click();
				}
			});
			
			// Initialize colorpickers after tab setup
			setTimeout(function() {
				WPDinoAdmin.initColorPickers();
			}, 200);
		},

		/**
		 * Switch tab
		 */
		switchTab: function(e) {
			e.preventDefault();
			const $btn = $(this);
			const tab = $btn.data('tab');
			
			WPDinoAdmin.activateTab(tab);
			
			// Update URL hash
			if (history.pushState) {
				history.pushState(null, null, '#' + tab);
			} else {
				window.location.hash = tab;
			}
		},

		/**
		 * Activate specific tab
		 */
		activateTab: function(tabName) {
			// Remove active class from all tabs and content
			$('.wpdino-tab-btn, .wpdino-tab-content').removeClass('active');
			
			// Update ARIA attributes for all tabs
			$('.wpdino-tab-btn').attr('aria-selected', 'false');
			
			// Add active class to selected tab and content
			const $activeTab = $('.wpdino-tab-btn[data-tab="' + tabName + '"]');
			const $activeContent = $('#tab-' + tabName);
			
			$activeTab.addClass('active').attr('aria-selected', 'true');
			$activeContent.addClass('active');
			
			// Focus management for accessibility (don't focus if user clicked)
			if (!$activeTab.is(':focus')) {
				$activeTab.focus();
			}
			
			// Trigger custom event
			$(document).trigger('wpdino:tab-changed', [tabName]);

			// Initialize color pickers in the newly visible tab
			setTimeout(function() {
				$('#tab-' + tabName + ' .wpdino-color-picker').each(function() {
					const $input = $(this);
					
					// Skip if already initialized
					if ($input.hasClass('wp-color-picker') || $input.closest('.wp-picker-container').length > 0) {
						return;
					}
					
					// Initialize the color picker
					$input.wpColorPicker({
						change: function(event, ui) {
							$input.val(ui.color.toString()).trigger('change');
						},
						clear: function() {
							$input.val('').trigger('change');
						},
						palettes: true
					});
				});
			}, 100);
		},

		/**
		 * Initialize color pickers
		 */
		initColorPickers: function() {
			if ($.fn.wpColorPicker) {
				// Find color picker inputs that haven't been initialized yet
				$('.wpdino-color-picker').each(function() {
					const $input = $(this);
					
					// Skip if already initialized
					if ($input.hasClass('wp-color-picker') || $input.closest('.wp-picker-container').length > 0) {
						return;
					}
					
					// Initialize the color picker
					$input.wpColorPicker({
						change: function(event, ui) {
							// Update the original input value
							$input.val(ui.color.toString()).trigger('change');
						},
						clear: function() {
							// Clear the original input value
							$input.val('').trigger('change');
						},
						palettes: true
					});
				});
			}
		},

		/**
		 * Initialize media uploader
		 */
		initMediaUploader: function() {
			// Initialize WordPress media uploader for existing buttons
			if (typeof wp !== 'undefined' && wp.media) {
				this.mediaUploader = wp.media({
					title: 'Select File',
					button: {
						text: 'Use This File'
					},
					multiple: false
				});
			}
		},

		/**
		 * Open image uploader (images only)
		 */
		openImageUploader: function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const target = $btn.data('target');
			const title = $btn.data('title') || 'Select Image';
			const buttonText = $btn.data('button') || 'Use This Image';
			
			// Create new media uploader instance for images only
			const imageUploader = wp.media({
				title: title,
				button: {
					text: buttonText
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});
			
			// When an image is selected
			imageUploader.on('select', function() {
				const attachment = imageUploader.state().get('selection').first().toJSON();
				WPDinoAdmin.updateImageField(target, attachment.url);
			});
			
			// Open the uploader
			imageUploader.open();
		},

		/**
		 * Remove image from field
		 */
		removeImage: function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const target = $btn.data('target');
			
			WPDinoAdmin.updateImageField(target, '');
		},

		/**
		 * Update image field preview
		 */
		updateImageField: function(target, imageUrl) {
			const $input = $(target);
			const $imageField = $input.closest('.wpdino-image-field');
			const $preview = $imageField.find('.wpdino-image-preview');
			const $prompt = $imageField.find('.wpdino-image-upload-prompt');
			const $img = $preview.find('img');
			
			// Update the hidden input value
			$input.val(imageUrl).trigger('change');
			
			if (imageUrl) {
				// Show preview, hide prompt
				$img.attr('src', imageUrl);
				$preview.show();
				$prompt.hide();
			} else {
				// Hide preview, show prompt
				$preview.hide();
				$prompt.show();
				$img.attr('src', '');
			}
		},

		/**
		 * Copy system info to clipboard
		 */
		copySystemInfo: function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const $textarea = $('#system-info-content');
			
			// Select the text
			$textarea.select();
			$textarea[0].setSelectionRange(0, 99999); // For mobile devices
			
			try {
				// Try to copy using the modern API
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText($textarea.val()).then(function() {
						WPDinoAdmin.showCopyFeedback($btn, 'success');
					}).catch(function() {
						// Fallback to execCommand
						WPDinoAdmin.fallbackCopy($textarea, $btn);
					});
				} else {
					// Fallback to execCommand
					WPDinoAdmin.fallbackCopy($textarea, $btn);
				}
			} catch (err) {
				WPDinoAdmin.showCopyFeedback($btn, 'error');
			}
		},

		/**
		 * Fallback copy method using execCommand
		 */
		fallbackCopy: function($textarea, $btn) {
			try {
				$textarea.focus();
				const successful = document.execCommand('copy');
				if (successful) {
					WPDinoAdmin.showCopyFeedback($btn, 'success');
				} else {
					WPDinoAdmin.showCopyFeedback($btn, 'error');
				}
			} catch (err) {
				WPDinoAdmin.showCopyFeedback($btn, 'error');
			}
		},

		/**
		 * Show copy feedback
		 */
		showCopyFeedback: function($btn, status) {
			const originalText = $btn.html();
			
			// Get strings with fallbacks
			const copySuccess = (wpdinoAdmin && wpdinoAdmin.strings && wpdinoAdmin.strings.copySuccess) 
				? wpdinoAdmin.strings.copySuccess 
				: 'System info copied to clipboard!';
			const copyError = (wpdinoAdmin && wpdinoAdmin.strings && wpdinoAdmin.strings.copyError) 
				? wpdinoAdmin.strings.copyError 
				: 'Failed to copy. Please select and copy manually.';
			
			if (status === 'success') {
				$btn.html('<span class="dashicons dashicons-yes"></span> ' + copySuccess);
				$btn.addClass('wpdino-btn-success');
			} else {
				$btn.html('<span class="dashicons dashicons-no"></span> ' + copyError);
				$btn.addClass('wpdino-btn-danger');
			}
			
			setTimeout(function() {
				$btn.html(originalText);
				$btn.removeClass('wpdino-btn-success wpdino-btn-danger');
			}, 2000);
		},

		/**
		 * Initialize image select functionality
		 */
		initImageSelect: function() {
			// Set initial selected state for image select options
			$('.wpdino-image-select-group input[type="radio"]:checked').each(function() {
				$(this).closest('.wpdino-image-select-option').addClass('selected');
			});
		},

		/**
		 * Handle image select changes
		 */
		handleImageSelectChange: function() {
			const $radio = $(this);
			const $group = $radio.closest('.wpdino-image-select-group');
			
			// Remove selected class from all options in this group
			$group.find('.wpdino-image-select-option').removeClass('selected');
			
			// Add selected class to the chosen option
			$radio.closest('.wpdino-image-select-option').addClass('selected');
		},

		/**
		 * Open media uploader
		 */
		openMediaUploader: function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const target = $btn.data('target');
			const title = $btn.data('title') || 'Select File';
			const buttonText = $btn.data('button') || 'Use This File';
			
			// Create new media uploader instance
			const mediaUploader = wp.media({
				title: title,
				button: {
					text: buttonText
				},
				multiple: false
			});
			
			// When a file is selected
			mediaUploader.on('select', function() {
				const attachment = mediaUploader.state().get('selection').first().toJSON();
				$(target).val(attachment.url).trigger('change');
			});
			
			// Open the uploader
			mediaUploader.open();
		},

		/**
		 * Initialize form validation
		 */
		initFormValidation: function() {
			// Real-time validation
			$('.wpdino-form input, .wpdino-form textarea, .wpdino-form select').on('blur', function() {
				WPDinoAdmin.validateField($(this));
			});

			// Slug validation
			$('#portfolio_slug').on('input', function() {
				const value = $(this).val();
				const sanitized = value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-');
				if (value !== sanitized) {
					$(this).val(sanitized);
				}
			});

			// Number validation
			$('input[type="number"]').on('input', function() {
				const min = parseInt($(this).attr('min')) || 0;
				const max = parseInt($(this).attr('max')) || 999;
				const value = parseInt($(this).val()) || min;
				
				if (value < min) $(this).val(min);
				if (value > max) $(this).val(max);
			});
		},

		/**
		 * Validate individual field
		 */
		validateField: function($field) {
			const fieldType = $field.attr('type') || $field.prop('tagName').toLowerCase();
			const value = $field.val();
			const $fieldGroup = $field.closest('.wpdino-field-group');
			
			// Remove existing validation classes
			$fieldGroup.removeClass('has-error has-success');
			$field.removeClass('error success');
			
			// Required field validation
			if ($field.prop('required') && !value.trim()) {
				this.showFieldError($field, 'This field is required');
				return false;
			}

			// Email validation
			if (fieldType === 'email' && value && !this.isValidEmail(value)) {
				this.showFieldError($field, 'Please enter a valid email address');
				return false;
			}

			// URL validation
			if (fieldType === 'url' && value && !this.isValidUrl(value)) {
				this.showFieldError($field, 'Please enter a valid URL');
				return false;
			}

			// Success state
			if (String(value || '').trim()) {
				$fieldGroup.addClass('has-success');
				$field.addClass('success');
			}

			return true;
		},

		/**
		 * Show field error
		 */
		showFieldError: function($field, message) {
			const $fieldGroup = $field.closest('.wpdino-field-group');
			$fieldGroup.addClass('has-error');
			$field.addClass('error');
			
			// Remove existing error message
			$fieldGroup.find('.field-error').remove();
			
			// Add error message
			$field.after(`<div class="field-error">${message}</div>`);
		},

		/**
		 * Handle form submission
		 */
		handleFormSubmission: function(e) {
			const $form = $(this);
			const $submitBtn = $form.find('button[type="submit"]');
			
			// Validate all fields
			let isValid = true;
			$form.find('input, textarea, select').each(function() {
				if (!WPDinoAdmin.validateField($(this))) {
					isValid = false;
				}
			});

			if (!isValid) {
				e.preventDefault();
				WPDinoAdmin.showNotice('Please fix the errors below before saving.', 'error', 'save');
				return false;
			}

			// Show loading state
			$submitBtn.addClass('loading').prop('disabled', true);
			
			// Re-enable after delay (in case of page reload)
			setTimeout(function() {
				$submitBtn.removeClass('loading').prop('disabled', false);
			}, 3000);
		},

		/**
		 * Handle input changes
		 */
		handleInputChange: function() {
			const $input = $(this);
			const fieldName = $input.attr('name');
			
			// Live preview for certain fields
			if (fieldName === 'custom_css') {
				WPDinoAdmin.updateCustomCSS($input.val());
			}
		},

		/**
		 * Update custom CSS preview
		 */
		updateCustomCSS: function(css) {
			let $style = $('#wpdino-custom-css-preview');
			if (!$style.length) {
				$style = $('<style id="wpdino-custom-css-preview"></style>').appendTo('head');
			}
			$style.html(css);
		},

		/**
		 * Reset settings
		 */
		resetSettings: function(e) {
			e.preventDefault();
			
			if (!confirm(wpdinoAdmin.strings.confirmReset)) {
				return;
			}

			const $btn = $(this);
			$btn.addClass('loading').prop('disabled', true);

			$.ajax({
				url: wpdinoAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wpdino_reset_settings',
					nonce: wpdinoAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						// Redirect to show notice
						if (response.data.redirect) {
							window.location.href = response.data.redirect;
						} else {
							WPDinoAdmin.showNotice(response.data.message, 'success', 'reset');
							// Reload page after short delay
							setTimeout(function() {
								window.location.reload();
							}, 1500);
						}
					} else {
						WPDinoAdmin.showNotice(response.data.message || wpdinoAdmin.strings.error, 'error', 'reset');
					}
				},
				error: function() {
					WPDinoAdmin.showNotice(wpdinoAdmin.strings.error, 'error', 'reset');
				},
				complete: function() {
					$btn.removeClass('loading').prop('disabled', false);
				}
			});
		},

		/**
		 * Export settings
		 */
		exportSettings: function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			$btn.addClass('loading').prop('disabled', true);

			$.ajax({
				url: wpdinoAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wpdino_export_settings',
					nonce: wpdinoAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						const blob = new Blob([JSON.stringify(response.data.settings, null, 2)], {
							type: 'application/json'
						});
						const url = window.URL.createObjectURL(blob);
						const a = document.createElement('a');
						a.href = url;
						a.download = response.data.filename;
						document.body.appendChild(a);
						a.click();
						document.body.removeChild(a);
						window.URL.revokeObjectURL(url);
						
						WPDinoAdmin.showNotice(wpdinoAdmin.strings.exportSuccess, 'success');
					} else {
						WPDinoAdmin.showNotice(response.data.message || wpdinoAdmin.strings.error, 'error');
					}
				},
				error: function() {
					WPDinoAdmin.showNotice(wpdinoAdmin.strings.error, 'error');
				},
				complete: function() {
					$btn.removeClass('loading').prop('disabled', false);
				}
			});
		},

		/**
		 * Trigger import file selection
		 */
		triggerImport: function(e) {
			e.preventDefault();
			$('#import-file').click();
		},

		/**
		 * Import settings
		 */
		importSettings: function(e) {
			const file = e.target.files[0];
			if (!file) return;

			// Validate file type
			if (file.type !== 'application/json') {
				WPDinoAdmin.showNotice(wpdinoAdmin.strings.invalidFile, 'error');
				return;
			}

			const reader = new FileReader();
			reader.onload = function(e) {
				try {
					const settings = JSON.parse(e.target.result);
					
					if (!confirm(wpdinoAdmin.strings.confirmImport)) {
						return;
					}

					const $btn = $('#wpdino-import-settings');
					$btn.addClass('loading').prop('disabled', true);

					$.ajax({
						url: wpdinoAdmin.ajaxUrl,
						type: 'POST',
						data: {
							action: 'wpdino_import_settings',
							nonce: wpdinoAdmin.nonce,
							settings: JSON.stringify(settings)
						},
						success: function(response) {
							if (response.success) {
								WPDinoAdmin.showNotice(response.data.message, 'success');
								// Reload page after short delay
								setTimeout(function() {
									window.location.reload();
								}, 1500);
							} else {
								WPDinoAdmin.showNotice(response.data.message || wpdinoAdmin.strings.error, 'error');
							}
						},
						error: function() {
							WPDinoAdmin.showNotice(wpdinoAdmin.strings.error, 'error');
						},
						complete: function() {
							$btn.removeClass('loading').prop('disabled', false);
						}
					});

				} catch (error) {
					WPDinoAdmin.showNotice(wpdinoAdmin.strings.invalidFile, 'error');
				}
			};

			reader.readAsText(file);
			
			// Clear the file input
			$(e.target).val('');
		},

		/**
		 * Show notice message (custom notices only for save/reset actions)
		 */
		showNotice: function(message, type, action) {
			type = type || 'info';
			
			// For custom notices, only show for save settings and reset actions
			if (action && !['save', 'reset'].includes(action)) {
				return;
			}
			
			// Remove existing custom notices only
			$('.wpdino-notice').remove();
			
			const $notice = $(`
				<div class="wpdino-notice wpdino-${type}" role="alert">
					<p>${message}</p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text">Dismiss this notice.</span>
					</button>
				</div>
			`);
			
			// Insert notice after .wpdino-content container
			$('.wpdino-content').after($notice);
			
			// Auto-hide success notices
			if (type === 'success') {
				setTimeout(function() {
					$notice.fadeOut(function() {
						$(this).remove();
					});
				}, 5000);
			}
		},

		/**
		 * Show existing notices (keep WordPress admin notices visible)
		 */
		showNotices: function() {
			// Keep WordPress admin notices visible - don't remove them
			// Only remove any custom wpdino notices that might exist
			$('.wpdino-notice').remove();
		},

		/**
		 * Smooth scrolling for anchor links
		 */
		smoothScroll: function(e) {
			const $link = $(this);
			const href = $link.attr('href');
			
			if (href.indexOf('#') === 0 && href.length > 1) {
				const $target = $(href);
				if ($target.length) {
					e.preventDefault();
					$('html, body').animate({
						scrollTop: $target.offset().top - 80
					}, 500);
				}
			}
		},

		/**
		 * Validate email address
		 */
		isValidEmail: function(email) {
			const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return re.test(email);
		},

		/**
		 * Validate URL
		 */
		isValidUrl: function(url) {
			try {
				new URL(url);
				return true;
			} catch (e) {
				return false;
			}
		},

		/**
		 * Debounce function
		 */
		debounce: function(func, wait, immediate) {
			let timeout;
			return function() {
				const context = this;
				const args = arguments;
				const later = function() {
					timeout = null;
					if (!immediate) func.apply(context, args);
				};
				const callNow = immediate && !timeout;
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
				if (callNow) func.apply(context, args);
			};
		}
	};

	// Handle notice dismissal (both JS and PHP notices)
	$(document).on('click', '.notice-dismiss, .wpdino-notice-dismiss', function() {
		$(this).closest('.wpdino-notice, .wpdino-admin-notice').fadeOut(function() {
			$(this).remove();
		});
	});

	// Auto-hide admin notices after 7 seconds
	if ($('.wpdino-admin-notice').length > 0) {
		setTimeout(function() {
			$('.wpdino-admin-notice').fadeOut(function() {
				$(this).remove();
			});
		}, 7000);
	}

	// Handle tab switching (if tabs are added later)
	$(document).on('click', '.wpdino-tab-link', function(e) {
		e.preventDefault();
		
		const $link = $(this);
		const target = $link.attr('href');
		
		// Update active tab
		$('.wpdino-tab-link').removeClass('active');
		$link.addClass('active');
		
		// Show target content
		$('.wpdino-tab-content').removeClass('active');
		$(target).addClass('active');
		
		// Update URL hash without jumping
		if (history.pushState) {
			history.pushState(null, null, target);
		}
	});

	// Handle initial tab from URL hash
	$(window).on('load', function() {
		const hash = window.location.hash;
		if (hash && $(hash).length) {
			$('.wpdino-tab-link[href="' + hash + '"]').click();
		}
	});

	// Add confirmation to destructive actions
	$(document).on('click', '.wpdino-btn-danger:not(#wpdino-reset-settings)', function(e) {
		if (!confirm('Are you sure you want to perform this action? This cannot be undone.')) {
			e.preventDefault();
			return false;
		}
	});

	// Auto-save draft functionality (for future use)
	let autoSaveTimer;
	$(document).on('input', '.wpdino-form input, .wpdino-form textarea', WPDinoAdmin.debounce(function() {
		// Clear existing timer
		clearTimeout(autoSaveTimer);
		
		// Set new timer for auto-save
		autoSaveTimer = setTimeout(function() {
			// Auto-save logic here if needed
			console.log('Auto-save triggered');
		}, 30000); // 30 seconds
	}, 1000));

	// Keyboard shortcuts
	$(document).on('keydown', function(e) {
		// Ctrl/Cmd + S to save
		if ((e.ctrlKey || e.metaKey) && e.which === 83) {
			e.preventDefault();
			$('.wpdino-form button[type="submit"]').click();
			return false;
		}
		
		// Escape to close modals/notices
		if (e.which === 27) {
			$('.wpdino-notice .notice-dismiss').click();
		}
	});

	// Responsive navigation toggle (for mobile)
	$(document).on('click', '.wpdino-nav-toggle', function() {
		$('.wpdino-navigation').toggleClass('open');
	});

	// Initialize tooltips if needed
	if ($.fn.tooltip) {
		$('[data-toggle="tooltip"]').tooltip();
	}

	// Initialize popovers if needed
	if ($.fn.popover) {
		$('[data-toggle="popover"]').popover();
	}

	// Global object for external access
	window.WPDinoAdmin = WPDinoAdmin;

})(jQuery);

// Localized strings will be provided by wp_localize_script 