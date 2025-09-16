/**
 * Block Editor JavaScript for Neve Premium Blocks
 */

(function() {
    'use strict';

    // Wait for WordPress to be ready
    wp.domReady(function() {
        // Add custom styles to editor
        addEditorStyles();
        
        // Initialize block variations
        initBlockVariations();
        
        // Add block transforms
        addBlockTransforms();
    });

    /**
     * Add custom styles to the editor
     */
    function addEditorStyles() {
        var editorStyles = `
            .nueve4-testimonials-block .testimonial-item {
                border: 1px solid #e0e0e0;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
            }
            
            .nueve4-pricing-block .pricing-plan {
                border: 2px solid #e0e0e0;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                text-align: center;
            }
            
            .nueve4-pricing-block .pricing-plan.featured {
                border-color: #0073aa;
                background: #f0f8ff;
            }
            
            .nueve4-team-block .team-member {
                border: 1px solid #e0e0e0;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                text-align: center;
            }
            
            .nueve4-team-block .member-image img {
                max-width: 100px;
                border-radius: 50%;
            }
            
            .social-item {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
                align-items: center;
            }
            
            .feature-item {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
                align-items: center;
            }
        `;

        var styleElement = document.createElement('style');
        styleElement.textContent = editorStyles;
        document.head.appendChild(styleElement);
    }

    /**
     * Initialize block variations
     */
    function initBlockVariations() {
        // Testimonials variations
        wp.blocks.registerBlockVariation('nueve4/testimonials', {
            name: 'testimonials-grid',
            title: 'Testimonials Grid',
            description: 'Display testimonials in a grid layout',
            attributes: {
                layout: 'grid'
            },
            isDefault: true
        });

        wp.blocks.registerBlockVariation('nueve4/testimonials', {
            name: 'testimonials-slider',
            title: 'Testimonials Slider',
            description: 'Display testimonials in a slider',
            attributes: {
                layout: 'slider'
            }
        });

        // Pricing table variations
        wp.blocks.registerBlockVariation('nueve4/pricing-table', {
            name: 'pricing-3-columns',
            title: '3 Column Pricing',
            description: 'Three column pricing table',
            attributes: {
                columns: 3
            },
            isDefault: true
        });

        wp.blocks.registerBlockVariation('nueve4/pricing-table', {
            name: 'pricing-2-columns',
            title: '2 Column Pricing',
            description: 'Two column pricing table',
            attributes: {
                columns: 2
            }
        });

        // Team members variations
        wp.blocks.registerBlockVariation('nueve4/team-members', {
            name: 'team-3-columns',
            title: '3 Column Team',
            description: 'Three column team layout',
            attributes: {
                columns: 3
            },
            isDefault: true
        });

        wp.blocks.registerBlockVariation('nueve4/team-members', {
            name: 'team-4-columns',
            title: '4 Column Team',
            description: 'Four column team layout',
            attributes: {
                columns: 4
            }
        });
    }

    /**
     * Add block transforms
     */
    function addBlockTransforms() {
        // Transform core/quote to nueve4/testimonials
        wp.blocks.registerBlockTransform('from', 'nueve4/testimonials', {
            type: 'block',
            blocks: ['core/quote'],
            transform: function(attributes) {
                return wp.blocks.createBlock('nueve4/testimonials', {
                    testimonials: [{
                        content: attributes.value,
                        name: attributes.citation || 'Anonymous',
                        position: '',
                        image: ''
                    }]
                });
            }
        });

        // Transform core/columns to nueve4/pricing-table
        wp.blocks.registerBlockTransform('from', 'nueve4/pricing-table', {
            type: 'block',
            blocks: ['core/columns'],
            transform: function(attributes, innerBlocks) {
                var plans = innerBlocks.map(function(block, index) {
                    return {
                        title: 'Plan ' + (index + 1),
                        price: '29',
                        currency: '$',
                        period: '/month',
                        features: ['Feature 1', 'Feature 2', 'Feature 3'],
                        button_text: 'Get Started',
                        button_url: '#',
                        featured: false
                    };
                });

                return wp.blocks.createBlock('nueve4/pricing-table', {
                    plans: plans,
                    columns: innerBlocks.length
                });
            }
        });
    }

    /**
     * Add custom block styles
     */
    wp.blocks.registerBlockStyle('nueve4/testimonials', {
        name: 'boxed',
        label: 'Boxed Style'
    });

    wp.blocks.registerBlockStyle('nueve4/testimonials', {
        name: 'minimal',
        label: 'Minimal Style'
    });

    wp.blocks.registerBlockStyle('nueve4/pricing-table', {
        name: 'modern',
        label: 'Modern Style'
    });

    wp.blocks.registerBlockStyle('nueve4/pricing-table', {
        name: 'classic',
        label: 'Classic Style'
    });

    wp.blocks.registerBlockStyle('nueve4/team-members', {
        name: 'card',
        label: 'Card Style'
    });

    wp.blocks.registerBlockStyle('nueve4/team-members', {
        name: 'overlay',
        label: 'Overlay Style'
    });

})();