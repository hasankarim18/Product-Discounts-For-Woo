<?php

namespace Hasan\ProductDiscountsForWoo;


if (!defined('ABSPATH')) {
    exit;
}


class ProductDiscountsForWoo
{
    use \Hasan\ProductDiscountsForWoo\App\Traits\Singleton;
    public function init()
    {
        $this->define_constants();
        add_action('plugins_loaded', [$this, 'pluginsLoaded']);
    }

    public function define_constants()
    {
        define('PDFW_PATH', plugin_dir_path(__DIR__));
        define('PDFW_URL', plugin_dir_url(__DIR__));

    }

    public function pluginsLoaded()
    {
        $this->includes();
        $this->init_hooks();
    }



    public function includes()
    {


        App\Settings::instance()->init();
    }

    public function init_hooks()
    {
        load_textdomain('pdfw-domain', false, plugin_dir_path(__DIR__) . 'i18n/');
    }


}





