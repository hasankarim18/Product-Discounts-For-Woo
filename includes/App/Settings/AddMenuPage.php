<?php

namespace Hasan\ProductDiscountsForWoo\App\Settings;

if (!defined('ABSPATH')) {
    exit;
}


class AddMenuPage
{
    use \Hasan\ProductDiscountsForWoo\App\Traits\Singleton;
    public function init()
    {
        $this->add_menu_page();
    }

    public function add_menu_page()
    {
        add_menu_page(
            'product-discounts-for-woo',
            'P. D. for WC.',
            'manage_options',
            'pdfw',
            [$this, 'add_settings_page'],
            '',
            100

        );

    }

    public function add_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Product Discounts for WooCommerce</h1>
        </div>
        <?php


    }

}