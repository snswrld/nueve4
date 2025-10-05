/**
 * Premium Theme JavaScript
 * Enhanced functionality for premium features
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initPremiumFeatures();
    });

    /**
     * Initialize all premium features
     */
    function initPremiumFeatures() {
        initStickyHeader();
        initDarkMode();
        initLazyLoading();
        initSmoothScrolling();
        initMobileMenu();
        initSearchToggle();
        initBackToTop();
        initAnimations();
        initWooCommerceEnhancements();
        initAccessibilityFeatures();
        initPerformanceOptimizations();
    }

    /**
     * Sticky Header
     */
    function initStickyHeader() {
        if (!nueve4Premium.stickyHeader) return;

        const header = $('.site-header');
        const headerHeight = header.outerHeight();
        let lastScrollTop = 0;

        $(window).scroll(function() {
            const scrollTop = $(this).scrollTop();
            
            if (scrollTop > headerHeight) {
                header.addClass('scrolled');
                
                // Hide header on scroll down, show on scroll up
                if (scrollTop > lastScrollTop && scrollTop > headerHeight * 2) {
                    header.addClass('header-hidden');
                } else {
                    header.removeClass('header-hidden');
                }
            } else {
                header.removeClass('scrolled header-hidden');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    /**
     * Dark Mode Toggle
     */
    function initDarkMode() {
        if (!nueve4Premium.darkMode) return;

        // Create dark mode toggle button
        const toggleButton = $('<button class="dark-mode-toggle" aria-label="Toggle Dark Mode"><span class="toggle-icon"></span></button>');
        $('.site-header .container').append(toggleButton);

        // Check for saved preference or system preference
        const savedTheme = localStorage.getItem('nueve4-theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            $('body').addClass('dark-mode');
            toggleButton.addClass('active');
        }

        // Toggle dark mode
        toggleButton.on('click', function() {
            $('body').toggleClass('dark-mode');
            $(this).toggleClass('active');
            
            const isDark = $('body').hasClass('dark-mode');
            localStorage.setItem('nueve4-theme', isDark ? 'dark' : 'light');
            
            // Trigger custom event
            $(document).trigger('darkModeToggled', [isDark]);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('nueve4-theme')) {
                if (e.matches) {
                    $('body').addClass('dark-mode');
                    toggleButton.addClass('active');
                } else {
                    $('body').removeClass('dark-mode');
                    toggleButton.removeClass('active');
                }
            }
        });
    }

    /**
     * Lazy Loading Enhancement
     */
    function initLazyLoading() {
        if (!nueve4Premium.lazyLoad || !('IntersectionObserver' in window)) return;

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        $('img[data-src]').each(function() {
            imageObserver.observe(this);
        });
    }

    /**
     * Smooth Scrolling
     */
    function initSmoothScrolling() {
        if (!nueve4Premium.smoothScroll) return;

        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                const headerHeight = $('.site-header').outerHeight() || 0;
                
                $('html, body').animate({
                    scrollTop: target.offset().top - headerHeight - 20
                }, 800, 'easeInOutCubic');
            }
        });
    }

    /**
     * Enhanced Mobile Menu
     */
    function initMobileMenu() {
        const menuToggle = $('.menu-toggle');
        const mobileMenu = $('.mobile-menu');
        const body = $('body');

        // Create mobile menu if it doesn't exist
        if (!mobileMenu.length) {
            const mainNav = $('.main-navigation').clone();
            mainNav.addClass('mobile-menu').removeClass('main-navigation');
            $('.site-header').append(mainNav);
        }

        menuToggle.on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('active');
            $('.mobile-menu').toggleClass('active');
            body.toggleClass('mobile-menu-open');
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.mobile-menu, .menu-toggle').length) {
                menuToggle.removeClass('active');
                $('.mobile-menu').removeClass('active');
                body.removeClass('mobile-menu-open');
            }
        });

        // Close menu on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && body.hasClass('mobile-menu-open')) {
                menuToggle.removeClass('active');
                $('.mobile-menu').removeClass('active');
                body.removeClass('mobile-menu-open');
            }
        });
    }

    /**
     * Search Toggle
     */
    function initSearchToggle() {
        const searchToggle = $('.search-toggle');
        const searchForm = $('.search-form');

        searchToggle.on('click', function(e) {
            e.preventDefault();
            searchForm.toggleClass('active');
            
            if (searchForm.hasClass('active')) {
                searchForm.find('input[type="search"]').focus();
            }
        });

        // Close search on escape
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && searchForm.hasClass('active')) {
                searchForm.removeClass('active');
            }
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const backToTop = $('<button class="back-to-top" aria-label="Back to Top"><i class="fas fa-arrow-up"></i></button>');
        $('body').append(backToTop);

        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass('visible');
            } else {
                backToTop.removeClass('visible');
            }
        });

        backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 800);
        });
    }

    /**
     * Scroll Animations
     */
    function initAnimations() {
        if (!('IntersectionObserver' in window)) return;

        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        $('.post, .widget, .woocommerce .product').each(function() {
            this.classList.add('animate-on-scroll');
            animationObserver.observe(this);
        });
    }

    /**
     * WooCommerce Enhancements
     */
    function initWooCommerceEnhancements() {
        if (!$('body').hasClass('woocommerce')) return;

        // Product image hover effect
        $('.woocommerce .product img').on('mouseenter', function() {
            $(this).addClass('hover-effect');
        }).on('mouseleave', function() {
            $(this).removeClass('hover-effect');
        });

        // Quantity input enhancements
        $('.quantity').each(function() {
            const $this = $(this);
            const input = $this.find('input[type="number"]');
            
            if (!$this.find('.qty-btn').length) {
                $this.prepend('<button type="button" class="qty-btn qty-minus">-</button>');
                $this.append('<button type="button" class="qty-btn qty-plus">+</button>');
            }
        });

        $(document).on('click', '.qty-minus', function() {
            const input = $(this).siblings('input[type="number"]');
            const currentVal = parseInt(input.val()) || 0;
            const min = parseInt(input.attr('min')) || 1;
            
            if (currentVal > min) {
                input.val(currentVal - 1).trigger('change');
            }
        });

        $(document).on('click', '.qty-plus', function() {
            const input = $(this).siblings('input[type="number"]');
            const currentVal = parseInt(input.val()) || 0;
            const max = parseInt(input.attr('max')) || 999;
            
            if (currentVal < max) {
                input.val(currentVal + 1).trigger('change');
            }
        });

        // Add to cart animation
        $(document).on('added_to_cart', function(event, fragments, cart_hash, button) {
            button.addClass('added');
            setTimeout(() => {
                button.removeClass('added');
            }, 2000);
        });
    }

    /**
     * Accessibility Features
     */
    function initAccessibilityFeatures() {
        // Skip link functionality
        $('.skip-link').on('click', function(e) {
            const target = $($(this).attr('href'));
            if (target.length) {
                target.attr('tabindex', '-1').focus();
            }
        });

        // Keyboard navigation for dropdowns
        $('.main-navigation a').on('keydown', function(e) {
            const $this = $(this);
            const $parent = $this.parent();
            const $submenu = $parent.find('> ul');

            switch(e.keyCode) {
                case 13: // Enter
                case 32: // Space
                    if ($submenu.length) {
                        e.preventDefault();
                        $submenu.toggleClass('focus');
                    }
                    break;
                case 27: // Escape
                    $submenu.removeClass('focus');
                    $this.focus();
                    break;
                case 37: // Left arrow
                    e.preventDefault();
                    $parent.prev().find('> a').focus();
                    break;
                case 39: // Right arrow
                    e.preventDefault();
                    $parent.next().find('> a').focus();
                    break;
                case 38: // Up arrow
                    if ($submenu.length && $submenu.hasClass('focus')) {
                        e.preventDefault();
                        $submenu.find('a').last().focus();
                    }
                    break;
                case 40: // Down arrow
                    if ($submenu.length) {
                        e.preventDefault();
                        $submenu.addClass('focus').find('a').first().focus();
                    }
                    break;
            }
        });

        // Focus management for modals
        $(document).on('keydown', function(e) {
            if (e.keyCode === 9) { // Tab key
                const focusableElements = $(':focusable');
                const firstFocusable = focusableElements.first();
                const lastFocusable = focusableElements.last();

                if (e.shiftKey && document.activeElement === firstFocusable[0]) {
                    e.preventDefault();
                    lastFocusable.focus();
                } else if (!e.shiftKey && document.activeElement === lastFocusable[0]) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        });
    }

    /**
     * Performance Optimizations
     */
    function initPerformanceOptimizations() {
        // Debounce scroll events
        let scrollTimer;
        $(window).on('scroll', function() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                $(document).trigger('scrollEnd');
            }, 100);
        });

        // Preload critical images
        const criticalImages = $('.hero img, .featured-image img').slice(0, 3);
        criticalImages.each(function() {
            const img = new Image();
            img.src = this.src;
        });

        // Optimize font loading
        if ('fonts' in document) {
            const fonts = [
                'system-ui',
                // Add other fonts as needed
            ];

            fonts.forEach(font => {
                if (font !== 'system-ui') {
                    document.fonts.load(`1em ${font}`);
                }
            });
        }

        // Service Worker registration (if available)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('SW registered: ', registration);
                }).catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    }

    /**
     * Utility Functions
     */

    // Throttle function
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Debounce function
    function debounce(func, wait, immediate) {
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

    // Add easing function for smooth scrolling
    $.easing.easeInOutCubic = function(x, t, b, c, d) {
        if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
        return c / 2 * ((t -= 2) * t * t + 2) + b;
    };

    // Extend jQuery with focusable selector
    $.extend($.expr[':'], {
        focusable: function(element) {
            const map = {
                'a': true,
                'button': true,
                'input': true,
                'select': true,
                'textarea': true,
                'iframe': true
            };
            
            if (map[element.nodeName.toLowerCase()]) {
                return !element.disabled;
            }
            
            return element.tabIndex > -1;
        }
    });

    // Custom events
    $(document).on('nueve4:ready', function() {
        console.log('Nueve4 Premium features initialized');
    });

    // Trigger ready event
    $(document).trigger('nueve4:ready');

    // Expose public API
    window.Nueve4Premium = {
        init: initPremiumFeatures,
        darkMode: {
            toggle: function() {
                $('.dark-mode-toggle').trigger('click');
            },
            set: function(isDark) {
                if (isDark) {
                    $('body').addClass('dark-mode');
                    $('.dark-mode-toggle').addClass('active');
                } else {
                    $('body').removeClass('dark-mode');
                    $('.dark-mode-toggle').removeClass('active');
                }
                localStorage.setItem('nueve4-theme', isDark ? 'dark' : 'light');
            }
        },
        utils: {
            throttle: throttle,
            debounce: debounce
        }
    };

})(jQuery);