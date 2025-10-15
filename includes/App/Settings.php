<?php

namespace Hasan\ProductDiscountsForWoo\App;

if (!defined('ABSPATH')) {
    exit;
}

class Settings
{
    use \Hasan\ProductDiscountsForWoo\App\Traits\Singleton;

    public function init()
    {

        Settings\AddMenuPage::instance()->init();
        Settings\CalculateDiscounts\CalculateDiscounts::instance()->init();
    }

}



