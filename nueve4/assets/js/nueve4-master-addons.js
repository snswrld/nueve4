/**
 * Nueve4 Master Addons JavaScript
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initNueve4Addons();
    });

    // Initialize when Elementor frontend is ready
    $(window).on('elementor/frontend/init', function() {
        initElementorWidgets();
    });

    function initNueve4Addons() {
        initAccordion();
        initCountdown();
        initCounterUp();
        initProgressBars();
        initParallax();
        initAnimations();
    }

    function initElementorWidgets() {
        // Initialize widgets when Elementor loads them
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            var widgetType = $scope.data('widget_type');
            
            switch(widgetType) {
                case 'nueve4-accordion.default':
                    initAccordion($scope);
                    break;
                case 'nueve4-countdown.default':
                    initCountdown($scope);
                    break;
                case 'nueve4-counter.default':
                    initCounterUp($scope);
                    break;
                case 'nueve4-progress.default':
                    initProgressBars($scope);
                    break;
            }
        });
    }

    // Accordion Widget
    function initAccordion($scope) {
        var $accordion = $scope ? $scope.find('.nueve4-widget-accordion') : $('.nueve4-widget-accordion');
        
        $accordion.each(function() {
            var $this = $(this);
            
            $this.find('.accordion-header').on('click', function() {
                var $item = $(this).parent('.accordion-item');
                var $content = $item.find('.accordion-content');
                
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    $content.slideUp(300);
                } else {
                    $this.find('.accordion-item.active').removeClass('active');
                    $this.find('.accordion-content').slideUp(300);
                    $item.addClass('active');
                    $content.slideDown(300);
                }
            });
        });
    }

    // Countdown Timer Widget
    function initCountdown($scope) {
        var $countdown = $scope ? $scope.find('.nueve4-widget-countdown-timer') : $('.nueve4-widget-countdown-timer');
        
        $countdown.each(function() {
            var $this = $(this);
            var endDate = $this.data('end-date') || new Date().getTime() + (24 * 60 * 60 * 1000); // Default: 24 hours from now
            
            var timer = setInterval(function() {
                var now = new Date().getTime();
                var distance = endDate - now;
                
                if (distance < 0) {
                    clearInterval(timer);
                    $this.find('.countdown').html('<div class="countdown-ended">Time\'s up!</div>');
                    return;
                }
                
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                $this.find('.days .countdown-number').text(days);
                $this.find('.hours .countdown-number').text(hours);
                $this.find('.minutes .countdown-number').text(minutes);
                $this.find('.seconds .countdown-number').text(seconds);
            }, 1000);
        });
    }

    // Counter Up Widget
    function initCounterUp($scope) {
        var $counters = $scope ? $scope.find('.nueve4-widget-counter-up') : $('.nueve4-widget-counter-up');
        
        $counters.each(function() {
            var $this = $(this);
            var $number = $this.find('.counter-number');
            var target = parseInt($number.data('target')) || parseInt($number.text()) || 100;
            var duration = parseInt($this.data('duration')) || 2000;
            
            // Use Intersection Observer for better performance
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            animateCounter($number, target, duration);
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe($this[0]);
            } else {
                // Fallback for older browsers
                $(window).on('scroll', function() {
                    if (isElementInViewport($this[0])) {
                        animateCounter($number, target, duration);
                    }
                });
            }
        });
    }

    function animateCounter($element, target, duration) {
        var start = 0;
        var increment = target / (duration / 16); // 60fps
        
        var timer = setInterval(function() {
            start += increment;
            if (start >= target) {
                $element.text(target);
                clearInterval(timer);
            } else {
                $element.text(Math.floor(start));
            }
        }, 16);
    }

    // Progress Bars Widget
    function initProgressBars($scope) {
        var $progressBars = $scope ? $scope.find('.nueve4-widget-progressbar') : $('.nueve4-widget-progressbar');
        
        $progressBars.each(function() {
            var $this = $(this);
            
            // Use Intersection Observer
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            animateProgressBars($this);
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe($this[0]);
            } else {
                // Fallback
                $(window).on('scroll', function() {
                    if (isElementInViewport($this[0])) {
                        animateProgressBars($this);
                    }
                });
            }
        });
    }

    function animateProgressBars($container) {
        $container.find('.progress-fill').each(function() {
            var $this = $(this);
            var percentage = $this.data('percentage') || 75;
            
            setTimeout(function() {
                $this.css('width', percentage + '%');
            }, 200);
        });
    }

    // Parallax Effects
    function initParallax() {
        if (!window.requestAnimationFrame) return;
        
        var $parallaxElements = $('.nueve4-parallax');
        
        if ($parallaxElements.length === 0) return;
        
        var ticking = false;
        
        function updateParallax() {
            var scrollTop = $(window).scrollTop();
            
            $parallaxElements.each(function() {
                var $this = $(this);
                var speed = $this.data('speed') || 0.5;
                var yPos = -(scrollTop * speed);
                
                $this.css('transform', 'translateY(' + yPos + 'px)');
            });
            
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }
        
        $(window).on('scroll', requestTick);
    }

    // Animation on Scroll
    function initAnimations() {
        var $animatedElements = $('.nueve4-animate');
        
        if ($animatedElements.length === 0) return;
        
        // Use Intersection Observer for better performance
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var $element = $(entry.target);
                        var animation = $element.data('animation') || 'fade-in';
                        
                        $element.addClass('nueve4-' + animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            $animatedElements.each(function() {
                observer.observe(this);
            });
        } else {
            // Fallback for older browsers
            $(window).on('scroll', function() {
                $animatedElements.each(function() {
                    var $this = $(this);
                    
                    if (isElementInViewport(this) && !$this.hasClass('animated')) {
                        var animation = $this.data('animation') || 'fade-in';
                        $this.addClass('nueve4-' + animation + ' animated');
                    }
                });
            });
        }
    }

    // Utility function to check if element is in viewport
    function isElementInViewport(element) {
        var rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Sticky Elements Extension
    function initStickyElements() {
        var $stickyElements = $('.nueve4-sticky');
        
        if ($stickyElements.length === 0) return;
        
        $stickyElements.each(function() {
            var $this = $(this);
            var offset = $this.data('offset') || 0;
            var originalTop = $this.offset().top;
            
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                
                if (scrollTop >= originalTop - offset) {
                    $this.addClass('nueve4-stuck');
                } else {
                    $this.removeClass('nueve4-stuck');
                }
            });
        });
    }

    // Image Comparison Widget
    function initImageComparison() {
        $('.nueve4-image-comparison').each(function() {
            var $this = $(this);
            var $slider = $this.find('.comparison-slider');
            var $beforeImage = $this.find('.before-image');
            var $afterImage = $this.find('.after-image');
            
            var isDragging = false;
            
            function updateComparison(percentage) {
                $beforeImage.css('clip-path', 'inset(0 ' + (100 - percentage) + '% 0 0)');
                $slider.css('left', percentage + '%');
            }
            
            $slider.on('mousedown touchstart', function(e) {
                isDragging = true;
                e.preventDefault();
            });
            
            $(document).on('mousemove touchmove', function(e) {
                if (!isDragging) return;
                
                var containerOffset = $this.offset().left;
                var containerWidth = $this.width();
                var mouseX = (e.pageX || e.originalEvent.touches[0].pageX) - containerOffset;
                var percentage = (mouseX / containerWidth) * 100;
                
                percentage = Math.max(0, Math.min(100, percentage));
                updateComparison(percentage);
            });
            
            $(document).on('mouseup touchend', function() {
                isDragging = false;
            });
            
            // Initialize at 50%
            updateComparison(50);
        });
    }

    // Logo Slider Widget
    function initLogoSlider() {
        $('.nueve4-logo-slider').each(function() {
            var $this = $(this);
            var autoplay = $this.data('autoplay') !== false;
            var speed = $this.data('speed') || 3000;
            
            if (typeof $.fn.slick !== 'undefined') {
                $this.find('.logos-container').slick({
                    infinite: true,
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    autoplay: autoplay,
                    autoplaySpeed: speed,
                    arrows: false,
                    dots: false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 4
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 3
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 2
                            }
                        }
                    ]
                });
            }
        });
    }

    // Tabs Widget
    function initTabs() {
        $('.nueve4-widget-tabs').each(function() {
            var $this = $(this);
            
            $this.find('.tab-nav-item').on('click', function(e) {
                e.preventDefault();
                
                var $navItem = $(this);
                var targetTab = $navItem.data('tab');
                
                // Update navigation
                $this.find('.tab-nav-item').removeClass('active');
                $navItem.addClass('active');
                
                // Update content
                $this.find('.tab-content-item').removeClass('active');
                $this.find('.tab-content-item[data-tab="' + targetTab + '"]').addClass('active');
            });
        });
    }

    // Initialize additional widgets
    $(document).ready(function() {
        initStickyElements();
        initImageComparison();
        initLogoSlider();
        initTabs();
    });

    // Expose functions globally for Elementor
    window.Nueve4MasterAddons = {
        initAccordion: initAccordion,
        initCountdown: initCountdown,
        initCounterUp: initCounterUp,
        initProgressBars: initProgressBars,
        initParallax: initParallax,
        initAnimations: initAnimations
    };

})(jQuery);