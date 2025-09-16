<?php
/**
 * Font Manager namespace fix
 *
 * @package Nueve4
 */

namespace Neve\Views;

if (!function_exists('Neve\Views\neve_get_google_fonts')) {
    function neve_get_google_fonts() {
        return \neve_get_google_fonts();
    }
}