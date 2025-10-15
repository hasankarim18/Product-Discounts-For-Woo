<?php

/**
 * Plugin Name: Nth Product discounts for woocommerce
 * Plugin URI: https://example.com/
 * Description: Product discounts.
 * Version: 1.0.0
 * Author: Hasan
 * Author URI: https://hasantech.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pdfw-domain
 * Domain Path: /i18n
 */

namespace Hasan\ProductDiscountsForWoo;


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists(ProductDiscountsForWoo::class) && is_readable(__DIR__ . './vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}


class_exists(ProductDiscountsForWoo::class) && ProductDiscountsForWoo::instance()->init();


