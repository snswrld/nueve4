<?php
/**
 * Font Manager namespace fix
 *
 * @package Nueve4
 */

namespace Nueve4\Views;

if (!function_exists('Nueve4\Views\nueve4_get_google_fonts')) {
    function nueve4_get_google_fonts() {
        return \nueve4_get_google_fonts();
    }
}