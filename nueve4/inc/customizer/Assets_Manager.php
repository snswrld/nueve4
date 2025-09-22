<?php
/**
 * Handles enqueuing customizer assets.
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

use Nueve4\Core\Settings\Config;
use Nueve4\Customizer\Colors_Background;
use HFG\Core\Components\Utility\SearchIconButton;
use Nueve4\Core\Limited_Offers;

class Assets_Manager {

    public static function enqueue_assets() {
        // Register and enqueue customizer style
        wp_register_style(
            'nueve4-customizer-style',
            NUEVE4_ASSETS_URL . 'css/customizer-style' . ( ( NUEVE4_DEBUG ) ? '' : '.min' ) . '.css',
            array(),
            NUEVE4_VERSION
        );
        wp_style_add_data( 'nueve4-customizer-style', 'rtl', 'replace' );
        wp_style_add_data( 'nueve4-customizer-style', 'suffix', '.min' );
        wp_enqueue_style( 'nueve4-customizer-style' );

        // Enqueue customizer controls script
        wp_enqueue_script(
            'nueve4-customizer-controls',
            NUEVE4_ASSETS_URL . 'js/build/all/customizer-controls.js',
            array(
                'jquery',
                'wp-color-picker'
            ),
            NUEVE4_VERSION,
            true
        );

        $offer = new Limited_Offers();
        $bundle_path = get_template_directory_uri() . '/assets/apps/customizer-controls/build/';
        $dependencies = include get_template_directory() . '/assets/apps/customizer-controls/build/controls.asset.php';

        wp_register_script(
            'react-controls',
            $bundle_path . 'controls.js',
            $dependencies['dependencies'],
            $dependencies['version'],
            true
        );

        wp_localize_script(
            'react-controls',
            'Nueve4ReactCustomize',
            apply_filters(
                'nueve4_react_controls_localization',
                array(
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                    'headerControls' => array(),
                    'instructionalVid' => esc_url( get_template_directory_uri() . '/header-footer-grid/assets/images/customizer/hfg.mp4' ),
                    'dynamicTags' => array(
                        'controls' => array(),
                        'options' => array(),
                    ),
                    'upsellComponentsLink' => tsdk_utmify( 'https://themeisle.com/themes/nueve4/upgrade/', 'hfgcomponents' ),
                    'fonts' => array(
                        'System' => nueve4_get_standard_fonts(),
                        'Google' => nueve4_get_google_fonts(),
                    ),
                    'fontVariants' => nueve4_get_google_fonts( true ),
                    'systemFontVariants' => nueve4_get_standard_fonts( true ),
                    'hideConditionalHeaderSelector' => ! nueve4_can_use_conditional_header(),
                    'dashUpdatesMessage' => sprintf( 'Please %s to the latest version of Nueve4 Pro to manage the conditional headers.', '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">' . __( 'update', 'nueve4' ) . '</a>' ),
                    'bundlePath' => get_template_directory_uri() . '/assets/apps/customizer-controls/build/',
                    'localGoogleFonts' => array(
                        'learnMore' => apply_filters( 'nueve4_external_link', 'https://docs.themeisle.com/article/1349-how-to-load-nueve4-fonts-locally', esc_html__( 'Learn more', 'nueve4' ) ),
                        'key' => Config::OPTION_LOCAL_GOOGLE_FONTS_HOSTING,
                    ),
                    'fontPairs' => get_theme_mod( Config::MODS_TYPOGRAPHY_FONT_PAIRS, Config::$typography_default_pairs ),
                    'allowedGlobalCustomColor' => Colors_Background::CUSTOM_COLOR_LIMIT,
                    'constants' => array(
                        'HFGSearch' => array(
                            'defaultIconKey' => SearchIconButton::DEFAULT_ICON,
                            'customIconKey'  => SearchIconButton::CUSTOM_ICON,
                        ),
                    ),
                    'deal' => ! defined( 'NUEVE4_PRO_VERSION' ) ? $offer->get_localized_data() : array(),
                )
            )
        );

        wp_enqueue_script( 'react-controls' );

        if ( function_exists( 'wp_set_script_translations' ) ) {
            // Set script translations if available
            wp_set_script_translations( 'react-controls', 'nueve4' );
        }
    }
}
