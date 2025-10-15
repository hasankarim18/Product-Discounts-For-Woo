<?php

namespace Hasan\ProductDiscountsForWoo\App\Traits;

if (!defined('ABSPATH')) {
    exit;
}


trait Singleton
{
    private static $instance = null;
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

