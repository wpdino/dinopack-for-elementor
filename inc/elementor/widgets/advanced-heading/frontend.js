;(function ($) {
	'use strict';

	var $window = $(window);

	function debounce( func, wait, immediate ) {
		var timeout;

		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( !immediate ) func.apply( context, args );
			};
			var callNow = immediate && !timeout;

			clearTimeout( timeout );

			timeout = setTimeout( later, wait );

			if ( callNow ) func.apply( context, args );
		};
	}

	// Word Rotator
	var WordRotator = function($element) {
		var self = this;
		self.$element = $element;
		self.$container = $element.find('.dinopack-advanced-heading-container.has-word-rotator');
		
		// Check if container exists before proceeding
		if (!self.$container.length) {
			console.log('WordRotator: No rotator container found');
			return;
		}
		
		self.$wordRotator = self.$container.find('.dinopack-word-rotator');
		
		// Check if word rotator exists
		if (!self.$wordRotator.length) {
			console.log('WordRotator: No word rotator element found');
			return;
		}
		
		self.$words = self.$wordRotator.find('.dinopack-rotating-word, .dinopack-rotating-word-current');
		
		// Ensure we have words to rotate
		if (!self.$words.length) {
			console.log('WordRotator: No rotating words found');
			return;
		}
		
		self.animationType = self.$container.data('rotator-animation') || 'slide';
		self.animationSpeed = parseInt(self.$container.data('rotator-speed')) || 2000;
		self.currentWordIndex = 0;
		self.wordCount = self.$words.length;
		self.isAnimating = false;
		self.interval = null;
		self.typingInterval = null;
        self.isInitialized = false;

		// Initialize
		self.init();
	};

	// Prototype methods for WordRotator
	WordRotator.prototype = {
		init: function() {
			var self = this;
			
			// Hide all words except the first one
			self.$words.not(':first-child').hide();
            
            // Store original text of all words
            if (self.animationType === 'typing') {
                self.$words.each(function() {
                    var $word = $(this);
                    // Store original text if not already stored
                    if (!$word.data('original-text')) {
                        $word.data('original-text', $word.text());
                    }
                });
            }

			// Set animation class to container
			self.$wordRotator.addClass('animation-' + self.animationType);
            
            // For typing animation, calculate max width
            if (self.animationType === 'typing') {
                self.calculateMaxWidth();
            }

			// Start rotation
			self.startRotation();
            
            self.isInitialized = true;
		},
        
        calculateMaxWidth: function() {
            var self = this;
            var maxWidth = 0;
            
            // Temporarily make all words visible but with opacity 0
            self.$words.css({
                'position': 'absolute',
                'visibility': 'hidden',
                'display': 'block',
                'opacity': 0
            });
            
            // Calculate max width
            self.$words.each(function() {
                var width = $(this).width();
                if (width > maxWidth) {
                    maxWidth = width;
                }
            });
            
            // Set max width as data attribute and CSS variable
            if (maxWidth > 0) {
                self.$wordRotator.attr('data-max-width', maxWidth);
                self.$wordRotator.css('--max-width', maxWidth + 'px');
            }
            
            // Reset visibility
            self.$words.not(':first-child').css({
                'display': 'none',
                'visibility': '',
                'opacity': ''
            });
            self.$words.filter(':first-child').css({
                'position': 'relative',
                'visibility': '',
                'opacity': ''
            });
        },

		startRotation: function() {
			var self = this;
			
			if (self.wordCount <= 1) return;

			// Clear any existing interval
			if (self.interval) {
				clearInterval(self.interval);
			}

			// For typing animation, we need longer intervals because
			// the animation itself takes time to complete the typing and erasing
			var rotationDelay;
			if (self.animationType === 'typing') {
				// Define speeds for calculations
                var deleteSpeed = 40; // ms per character for deleting
                var typeSpeed = 80;  // ms per character for typing
            
				// Calculate time based on the actual word lengths
                var avgWordLength = 0;
                
                // Calculate average length of all words
                self.$words.each(function() {
                    var text = $(this).data('original-text') || $(this).text();
                    avgWordLength += text.length;
                });
                avgWordLength = Math.max(10, Math.ceil(avgWordLength / self.wordCount));
                
				var typeEraseTime = avgWordLength * (deleteSpeed + typeSpeed); // deleteSpeed + typeSpeed
				var pauseTime = 1500 + 1500; // pauseBeforeDelete + pauseAfterType
				rotationDelay = Math.max(self.animationSpeed, typeEraseTime + pauseTime + 1000); // Extra buffer
			} else {
				rotationDelay = self.animationSpeed;
			}

			self.interval = setInterval(function() {
				if (self.isAnimating) return;
				self.rotateWord();
			}, rotationDelay);
		},

		rotateWord: function() {
			var self = this;
			self.isAnimating = true;
			
			var $currentWord = self.$words.eq(self.currentWordIndex),
				$nextWord = self.$words.eq((self.currentWordIndex + 1) % self.wordCount);

			// Apply animation based on type
			switch (self.animationType) {
				case 'blur':
					self.animateBlur($currentWord, $nextWord);
					break;
				case 'typing':
					self.animateTyping($currentWord, $nextWord);
					break;
				case 'shuffle':
					self.animateShuffle($currentWord, $nextWord);
					break;
				default: // Default to blur if unknown
					self.animateBlur($currentWord, $nextWord);
			}

			// Update current word index
			self.currentWordIndex = (self.currentWordIndex + 1) % self.wordCount;
		},

		animateBlur: function($currentWord, $nextWord) {
			var self = this;
			var transitionDuration = 800; // Match the 0.8s in CSS
			
			// Add exiting class to current word
			$currentWord.addClass('exiting');
			
			// Fade out and blur current word using CSS transitions
			$currentWord.css({
				'filter': 'blur(20px)',
				'opacity': '0'
			});
			
			// Wait for transition to complete
			setTimeout(function() {
				// Hide and reset current word
				$currentWord.css({
					'display': 'none'
				}).removeClass('dinopack-rotating-word-current exiting').addClass('dinopack-rotating-word');
				
				// Prepare next word for animation
				$nextWord.css({
					'display': 'block',
					'opacity': '0',
					'filter': 'blur(20px)'
				}).addClass('entering');
				
				// Small delay before starting the fade in animation
				setTimeout(function() {
					// Fade in and unblur next word using CSS transitions
					$nextWord.css({
						'opacity': '1',
						'filter': 'blur(0)'
					});
					
					// Wait for transition to complete
					setTimeout(function() {
						// Set next word as current
						$nextWord.removeClass('dinopack-rotating-word entering').addClass('dinopack-rotating-word-current');
						self.isAnimating = false;
					}, transitionDuration);
				}, 20);
			}, transitionDuration);
		},
		
		animateTyping: function($currentWord, $nextWord) {
			var self = this;
			var deleteSpeed = 40; // ms per character for deleting
			var typeSpeed = 80;  // ms per character for typing
			var pauseBeforeDelete = 1500; // Longer pause before deleting
			var pauseAfterType = 1500; // Pause after typing completes
			
			// Store original content if not already stored
            if (!$currentWord.data('original-text')) {
                $currentWord.data('original-text', $currentWord.text());
            }
            if (!$nextWord.data('original-text')) {
                $nextWord.data('original-text', $nextWord.text());
            }
            
            // Get the original text
            var currentText = $currentWord.data('original-text');
            var nextText = $nextWord.data('original-text');
            
			// Clear any existing typing intervals
			if (self.typingInterval) {
				clearInterval(self.typingInterval);
                self.typingInterval = null;
			}
			
			// Add exiting class to current word
			$currentWord.addClass('exiting');
            
            // Ensure both words are positioned correctly
            $currentWord.css({
                'position': 'absolute',
                'left': '0',
                'top': '0'
            });
            
            $nextWord.css({
                'position': 'absolute',
                'left': '0',
                'top': '0',
                'display': 'none'
            });
			
			// Create a visible part counter for erasing
			var visibleLength = currentText.length;
			
			// First erase the current word by removing characters
			function eraseText() {
				if (visibleLength > 0) {
					visibleLength--;
					// Show only the visible part of text
					$currentWord.text(currentText.substring(0, visibleLength));
					setTimeout(eraseText, deleteSpeed);
				} else {
					// When erasing is done, hide the current word and prepare to type the next word
					$currentWord.css({
                        'display': 'none',
                        'opacity': '0'
                    }).removeClass('dinopack-rotating-word-current exiting').addClass('dinopack-rotating-word');
					
                    // Show the next word element but with empty text to start typing
                    $nextWord.css({
                        'display': 'block',
                        'opacity': '1',
                        'position': 'relative'
                    }).addClass('dinopack-rotating-word-current entering').removeClass('dinopack-rotating-word');
                    
                    $nextWord.text(''); // Start with empty text
					
					// Start typing the next word
					setTimeout(function(){
						typeText();
					}, typeSpeed);
				}
			}
			
			// Type next word by adding characters
			var typeIndex = 0;
			function typeText() {
				if (typeIndex < nextText.length) {
					typeIndex++;
					// Show the typed part of the text
					$nextWord.text(nextText.substring(0, typeIndex));
					setTimeout(typeText, typeSpeed);
				} else {
					// Add word completion class for styling
					$nextWord.addClass('typing-complete');
					
					// Add pause before proceeding to next word
					setTimeout(function() {
						// Set final text to ensure it's complete
                        $nextWord.text(nextText);
                        
						// Typing complete - remove word completion class
						$nextWord.removeClass('entering typing-complete');
						self.isAnimating = false;
					}, pauseAfterType); // Pause to show the completed word
				}
			}
			
			// Start the erasing process with a delay
			setTimeout(eraseText, pauseBeforeDelete);
		},
		
		animateShuffle: function($currentWord, $nextWord) {
			var self = this;
			var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			var nextText = $nextWord.text();
			var shuffleIterations = 5; // How many random characters to show before settling
			var shuffleSpeed = 50; // ms between shuffles
			
			// Add exiting class to current word
			$currentWord.addClass('exiting');
			
			// Hide current word
			$currentWord.fadeOut(200, function() {
				// Reset current word
				$currentWord.removeClass('dinopack-rotating-word-current exiting').addClass('dinopack-rotating-word');
				
				// Prepare next word for animation
				$nextWord.css({
					'display': 'block',
					'opacity': 1
				}).addClass('dinopack-rotating-word-current entering').removeClass('dinopack-rotating-word');
				$nextWord.text('');
				
				var currentIteration = 0;
				var finalText = '';
				
				// Function to generate a random character
				function getRandomChar() {
					return chars.charAt(Math.floor(Math.random() * chars.length));
				}
				
				// Create a series of random characters the same length as the target text
				function createRandomString(length) {
					var result = '';
					for (var i = 0; i < length; i++) {
						result += getRandomChar();
					}
					return result;
				}
				
				// Execute the shuffle
				function doShuffle() {
					currentIteration++;
					
					// Calculate how much of the real text to show
					var revealLength = Math.floor(nextText.length * (currentIteration / shuffleIterations));
					
					// Create random characters for the rest
					var randomPart = createRandomString(nextText.length - revealLength);
					
					// Combine real text with random characters
					finalText = nextText.substring(0, revealLength) + randomPart;
					
					// Update the text
					$nextWord.text(finalText);
					
					// Continue shuffling or finish
					if (currentIteration < shuffleIterations) {
						setTimeout(doShuffle, shuffleSpeed);
					} else {
						// Final iteration, show the complete text
						$nextWord.text(nextText);
						$nextWord.removeClass('entering');
						self.isAnimating = false;
					}
				}
				
				// Start shuffling
				doShuffle();
			});
		},
        
        destroy: function() {
            var self = this;
            
            // Clear any existing intervals
            if (self.interval) {
                clearInterval(self.interval);
                self.interval = null;
            }
            
            if (self.typingInterval) {
                clearInterval(self.typingInterval);
                self.typingInterval = null;
            }
            
            // Reset all words
            self.$words.removeClass('entering exiting dinopack-rotating-word-current typing-complete')
                .addClass('dinopack-rotating-word')
                .css({
                    'display': '',
                    'opacity': '',
                    'transform': '',
                    'filter': '',
                    'position': ''
                });
            
            // Restore original text for typing animation
            if (self.animationType === 'typing') {
                self.$words.each(function() {
                    var $word = $(this);
                    if ($word.data('original-text')) {
                        $word.text($word.data('original-text'));
                    }
                });
            }
                
            // Set first word as current
            self.$words.first().removeClass('dinopack-rotating-word')
                .addClass('dinopack-rotating-word-current')
                .css({
                    'display': 'block',
                    'opacity': '1',
                    'visibility': 'visible'
                });
                
            // Remove animation class
            self.$wordRotator.removeClass('animation-' + self.animationType);
            
            self.isInitialized = false;
        },
        
        reinit: function() {
            var self = this;
            
            if (self.isInitialized) {
                self.destroy();
            }
            
            self.init();
        }
	};

	// Initialize the WordRotator for each element
	function initWordRotators() {
		try {
			$('.dinopack-advanced-heading-container.has-word-rotator').each(function() {
				var $container = $(this);
				// Check if already initialized
				if (!$container.data('wordRotator')) {
					var $widget = $container.closest('.elementor-widget-dinopack-for-elementor-advanced-heading');
					if ($widget.length) {
						try {
							var rotator = new WordRotator($widget);
							if (rotator) {
								$container.data('wordRotator', rotator);
							}
						} catch (e) {
							console.error('Error initializing word rotator:', e);
						}
					} else {
						console.warn('Word rotator container found but no parent widget located');
					}
				}
			});
		} catch (e) {
			console.error('Error in initWordRotators:', e);
		}
	}
    
    // Re-initialize all word rotators when window resizes
    $window.on('resize', debounce(function() {
        $('.dinopack-advanced-heading-container.has-word-rotator').each(function() {
            var $container = $(this);
            var rotator = $container.data('wordRotator');
            if (rotator && rotator.animationType === 'typing') {
                rotator.calculateMaxWidth();
            }
        });
    }, 200));

	$window.on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/dinopack-for-elementor-advanced-heading.default',
			function($scope) {
				// Initialize word rotator if enabled
				if ($scope.find('.dinopack-advanced-heading-container.has-word-rotator').length > 0) {
					new WordRotator($scope);
				}
			}
		);
	});

	// Run on document ready to catch any existing elements
	$(document).ready(function() {
		initWordRotators();
	});
	
}(jQuery));