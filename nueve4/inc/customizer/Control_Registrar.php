<?php
/**
 * Handles registration of customizer control types.
 *
 * @package Nueve4\Customizer
 */

namespace Nueve4\Customizer;

class Control_Registrar {

    /**
     * Register customizer control types.
     *
     * @param object $customizer Customizer instance which supports the register_type method.
     *
     * @return void
     */
    public static function register_controls( $customizer ) {
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Radio_Image', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Range', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Responsive_Number', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Tabs', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Heading', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Checkbox', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Upsell_Control', 'control' );
        $customizer->register_type( 'Nueve4\\Customizer\\Controls\\Upsells\\Scroll_To_Top_Control', 'control' );
    }
}
