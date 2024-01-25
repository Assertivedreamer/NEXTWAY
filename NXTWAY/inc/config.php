<?php

/**
 * Careerfy Theme Config.
 *
 * @package Careerfy
 */
define("CAREERFY_VERSION", "9.4.0");
define("WP_JOBSEARCH_VERSION", "2.3.2");

function careerfy_framework_options() {
    global $careerfy_framework_options;
    if (empty($careerfy_framework_options)) {
        $careerfy_framework_options = get_option('careerfy_framework_options');
    }
    return $careerfy_framework_options;
}