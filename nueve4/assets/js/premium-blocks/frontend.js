/**
 * Frontend JavaScript for Nueve4 Premium Blocks
 */

(function($) {
    'use strict';

    // Initialize when DOM is ready
    $(document).ready(function() {
        initTestimonialsSlider();
        initPricingTables();
        initTeamMembers();
        initAnimations();
    });

    /**
     * Initialize testimonials slider
     */
    function initTestimonialsSlider() {
        $('.nueve4-testimonials-block.layout-slider').each(function() {
            var $slider = $(this);
            var $items = $slider.find('.testimonial-item');
            var currentIndex = 0;
            var totalItems = $items.length;

            if (totalItems <= 1) return;

            // Hide all items except first
            $items.hide().first().show();

            // Add navigation
            $slider.append('<div class="testimonials-nav"><button class="prev">‹</button><button class="next">›</button></div>');

            // Add dots
            var dotsHtml = '<div class="testimonials-dots">';
            for (var i = 0; i < totalItems; i++) {
                dotsHtml += '<button class="dot' + (i === 0 ? ' active' : '') + '" data-index="' + i + '"></button>';
            }
            dotsHtml += '</div>';
            $slider.append(dotsHtml);

            // Navigation click handlers
            $slider.on('click', '.prev', function() {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                showSlide(currentIndex);
            });

            $slider.on('click', '.next', function() {
                currentIndex = (currentIndex + 1) % totalItems;
                showSlide(currentIndex);
            });

            $slider.on('click', '.dot', function() {
                currentIndex = parseInt($(this).data('index'));
                showSlide(currentIndex);
            });

            // Auto-play
            var autoPlay = setInterval(function() {
                currentIndex = (currentIndex + 1) % totalItems;
                showSlide(currentIndex);
            }, 5000);

            // Pause on hover
            $slider.hover(
                function() { clearInterval(autoPlay); },
                function() {
                    autoPlay = setInterval(function() {
                        currentIndex = (currentIndex + 1) % totalItems;
                        showSlide(currentIndex);
                    }, 5000);
                }
            );

            function showSlide(index) {
                $items.fadeOut(300).eq(index).fadeIn(300);
                $slider.find('.dot').removeClass('active').eq(index).addClass('active');
            }
        });
    }

    /**
     * Initialize pricing tables
     */
    function initPricingTables() {
        $('.pricing-plan').hover(
            function() {
                $(this).addClass('hovered');
            },
            function() {
                $(this).removeClass('hovered');
            }
        );

        // Add click tracking for pricing buttons
        $('.plan-button').on('click', function(e) {
            var planTitle = $(this).closest('.pricing-plan').find('h3').text();
            
            // Track with Google Analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Pricing',
                    'event_label': planTitle
                });
            }
        });
    }

    /**
     * Initialize team members
     */
    function initTeamMembers() {
        $('.team-member').each(function() {
            var $member = $(this);
            var $image = $member.find('.member-image img');
            var $info = $member.find('.member-info');

            // Add hover effects
            $member.hover(
                function() {
                    $image.addClass('hovered');
                    $info.addClass('hovered');
                },
                function() {
                    $image.removeClass('hovered');
                    $info.removeClass('hovered');
                }
            );
        });

        // Social links tracking
        $('.social-links a').on('click', function(e) {
            var platform = $(this).attr('href');
            
            // Track with Google Analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Social',
                    'event_label': platform
                });
            }
        });
    }

    /**
     * Initialize animations
     */
    function initAnimations() {
        // Intersection Observer for animations
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('nueve4-animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe premium blocks
            document.querySelectorAll('.nueve4-testimonials-block, .nueve4-pricing-block, .nueve4-team-block').forEach(function(block) {
                observer.observe(block);
            });
        }
    }

    /**
     * Utility function to debounce events
     */
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Handle responsive behavior
     */
    function handleResponsive() {
        var windowWidth = $(window).width();
        
        // Adjust pricing table columns on mobile
        if (windowWidth < 768) {
            $('.nueve4-pricing-block').addClass('mobile-view');
        } else {
            $('.nueve4-pricing-block').removeClass('mobile-view');
        }

        // Adjust team member columns on mobile
        if (windowWidth < 768) {
            $('.nueve4-team-block').addClass('mobile-view');
        } else {
            $('.nueve4-team-block').removeClass('mobile-view');
        }
    }

    // Handle window resize
    $(window).on('resize', debounce(handleResponsive, 250));
    
    // Initial responsive check
    handleResponsive();

})(jQuery);