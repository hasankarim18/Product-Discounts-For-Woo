<?php

namespace Hasan\ProductDiscountsForWoo\App\Settings\CalculateDiscounts;

if (!defined('ABSPATH')) {
    exit;
}


class CalculateDiscounts
{
    use \Hasan\ProductDiscountsForWoo\App\Traits\Singleton;
    public function init()
    {
        //  error_log('calculate discounts init');
        add_action('woocommerce_cart_calculate_fees', [$this, 'cart_discount_rules'], 10, 1);
    }

    public function cart_discount_rules($cart)
    {
        if (is_admin()) {
            return;
        }
        //   var_dump($cart);

        $total = 0;

        foreach ($cart->get_cart() as $item) {
            $total += $item['quantity'];
        }

        // echo 'total:' . $total;


        if ($total >= 2) {
            $discount = $cart->get_subtotal() * 10 / 100;
            $cart->add_fee("10% Discount for 2", -$discount);
        }
    }


}