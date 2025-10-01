/**
 * Progress Bar Widget Frontend JS
 *
 * @package DinoPack
 * @since 1.0.0
 */

(function($) {
    "use strict";

    /**
     * DinoPackProgressBarHandler
     * Handles the progress bar animations and interactions
     */
    var DinoPackProgressBarHandler = function($scope, $) {
        var $progressBar;
        
        // If scope is already a progress bar element, use it directly
        if ($scope && $scope.hasClass && $scope.hasClass('dinopack-progress-bar')) {
            $progressBar = $scope;
        }
        // If scope is a jQuery object, try to find progress bar within it
        else if ($scope && typeof $scope.find === 'function') {
            $progressBar = $scope.find('.dinopack-progress-bar');
        } 
        // If scope is a jQuery object, filter for progress bar
        else if ($scope && $scope.length && $scope.length > 0) {
            $progressBar = $scope.filter('.dinopack-progress-bar');
        } 
        // If scope is a single DOM element, wrap it and search within
        else if ($scope && $scope[0] && $scope[0].nodeType === 1) {
            $progressBar = $($scope[0]).find('.dinopack-progress-bar');
        } 
        // Fallback: search globally
        else {
            $progressBar = $('.dinopack-progress-bar');
        }
        
        if (!$progressBar.length) {
            return;
        }

        var progressValue = parseFloat($progressBar.data('progress')) || 0;
        var labelText = $progressBar.data('label-text') || '0';
        var labelPrefix = $progressBar.data('label-prefix') || '';
        var labelSuffix = $progressBar.data('label-suffix') || '';
        var animationDuration = parseInt($progressBar.data('animation-duration')) || 1500;
        var progressType = $progressBar.hasClass('dinopack-progress-bar-line') ? 'line' : 'circle';
        
        // Set CSS custom properties for animation
        $progressBar.css({
            '--progress-transition-duration': animationDuration + 'ms',
            '--progress-percentage': progressValue + '%'
        });
        
        // Initialize animation when element comes into viewport
        var animateProgress = function() {
            if ($progressBar.hasClass('dinopack-progress-bar-animated')) {
                return; // Already animated
            }
            
            $progressBar.addClass('dinopack-progress-bar-animated');
            
            // Start counter animation
            animateCounter();
            
            if (progressType === 'line') {
                animateLineProgress();
            } else {
                animateCircleProgress();
            }
        };
        
        // Animate counter from 0 to target value
        function animateCounter() {
            var $labelText = $progressBar.find('.dinopack-progress-bar-label-text');
            if (!$labelText.length) {
                return;
            }
            
            var targetValue = parseFloat(labelText) || 0;
            var startValue = 0;
            var startTime = null;
            
            function updateCounter(timestamp) {
                if (!startTime) startTime = timestamp;
                var elapsed = timestamp - startTime;
                var progress = Math.min(elapsed / animationDuration, 1);
                
                // Easing function for smooth animation
                var easeOutQuart = 1 - Math.pow(1 - progress, 4);
                var currentValue = Math.round(startValue + (targetValue - startValue) * easeOutQuart);
                
                // Firefox-safe text update
                try {
                    $labelText.text(currentValue);
                } catch (e) {
                    $labelText[0].innerHTML = currentValue;
                }
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    // Ensure final value is exact
                    try {
                        $labelText.text(targetValue);
                    } catch (e) {
                        $labelText[0].innerHTML = targetValue;
                    }
                }
            }
            
            // Firefox-safe requestAnimationFrame
            if (window.requestAnimationFrame) {
                requestAnimationFrame(updateCounter);
            } else {
                // Fallback for older browsers
                setTimeout(function() {
                    updateCounter(Date.now());
                }, 16);
            }
        }
        
        // Animate line progress bar
        function animateLineProgress() {
            var $progress = $progressBar.find('.dinopack-progress-bar-progress');
            
            // Firefox-safe animation
            try {
                // Use requestAnimationFrame for smooth animation
                requestAnimationFrame(function() {
                    $progress.css('width', progressValue + '%');
                });
            } catch (e) {
                setTimeout(function() {
                    $progress.css('width', progressValue + '%');
                }, 16);
            }
        }
        
        // Animate circle progress bars
        function animateCircleProgress() {
            var $circle = $progressBar.find('svg .path');
            var $track = $progressBar.find('svg .track');
            
            if (!$circle.length) {
                return;
            }
            
            // Get circle dimensions
            var size = $progressBar.find('svg').attr('width') || 150;
            var strokeWidth = parseFloat($circle.attr('stroke-width')) || 10;
            var radius = (size / 2) - (strokeWidth / 2);
            
            // Calculate circumference
            var circumference = 2 * Math.PI * radius;
            var progressOffset = circumference - (progressValue / 100) * circumference;
            
            // Set CSS custom properties
            $progressBar.css({
                '--circumference': circumference,
                '--progress-offset': progressOffset
            });
            
            // Set initial state
            $circle.css({
                'stroke-dasharray': circumference + ' ' + circumference,
                'stroke-dashoffset': circumference
            });
            
            // Firefox-safe animation
            try {
                // Animate
                requestAnimationFrame(function() {
                    $circle.css('stroke-dashoffset', progressOffset);
                });
            } catch (e) {
                setTimeout(function() {
                    $circle.css('stroke-dashoffset', progressOffset);
                }, 16);
            }
        }
        
        // Use Intersection Observer for viewport detection
        function initIntersectionObserver() {
            if ('IntersectionObserver' in window && $progressBar.length > 0) {
                try {
                    var observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                animateProgress();
                                observer.unobserve(entry.target);
                            }
                        });
                    }, {
                        threshold: 0.1,
                        rootMargin: '0px 0px -20% 0px'
                    });
                    
                    var element = $progressBar.get(0);
                    if (element && element.nodeType === 1) {
                        observer.observe(element);
                    } else {
                        // Fallback: Animate immediately
                        setTimeout(animateProgress, 100);
                    }
                } catch (e) {
                    // Fallback: Animate immediately
                    setTimeout(animateProgress, 100);
                }
            } else {
                // Fallback: Animate immediately
                setTimeout(animateProgress, 100);
            }
        }
        
        // Check if we're in Elementor editor mode
        var isEditorMode = false;
        try {
            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) {
                isEditorMode = true;
            }
        } catch (e) {
            // Silent fail
        }
        
        if (isEditorMode) {
            // In editor mode, animate immediately
            setTimeout(animateProgress, 200);
        } else {
            // On frontend, use Intersection Observer
            initIntersectionObserver();
            
            // Fallback timer in case observer doesn't work
            setTimeout(function() {
                if (!$progressBar.hasClass('dinopack-progress-bar-animated')) {
                    animateProgress();
                }
            }, 2000); // 2 second fallback
        }
    };

    // Initialize progress bars when Elementor frontend is ready
    $(window).on('elementor/frontend/init', function() {
        try {
            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-progress-bar.default', DinoPackProgressBarHandler);
            }
        } catch (e) {
            // Silent fail
        }
    });

    // Initialize progress bars when document is ready
    $(document).ready(function() {
        try {
            var progressBars = $('.dinopack-progress-bar');
            progressBars.each(function() {
                var $element = $(this);
                // Pass the element itself as the progress bar
                DinoPackProgressBarHandler($element, $);
            });
        } catch (e) {
            // Silent fail
        }
    });
    
    // Handle dynamic content loading (for popups, etc.)
    $(document).on('elementor/popup/show', function() {
        setTimeout(function() {
            try {
                $('.dinopack-progress-bar').each(function() {
                    var $element = $(this);
                    DinoPackProgressBarHandler($element, $);
                });
            } catch (e) {
                // Silent fail
            }
        }, 100);
    });

})(jQuery);