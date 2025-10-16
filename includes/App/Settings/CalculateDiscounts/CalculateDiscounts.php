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
        // apply_filters('pdfw_product_discount_for_3', 15);
        // apply_filters('pdfw_product_discount_for_2', 10);

        // $discount_for_2 = apply_filters('pdfw_product_discount_for_2', get_option('wc_discount_percent', 10));
        // $discount_for_3 = apply_filters('pdfw_product_discount_for_3', 15);


        // if ($total > 1 && $total < 3) {
        //     $discount = $cart->get_subtotal() * $discount_for_2 / 100;
        //     $cart->add_fee("{$discount_for_2}% Discount for 2", -$discount);
        // } elseif ($total >= 3) {
        //     $discount = $cart->get_subtotal() * $discount_for_3 / 100;
        //     $cart->add_fee("{$discount_for_3}% Discount for 3", -$discount);
        // }

        if ($total >= 2) {
            $discount_percent = get_option('wc_discount_percent');
            $quantity = get_option('wc_discount_min_quantity');

            $discount = $cart->get_subtotal() * $discount_percent / 100;
            $cart->add_fee("{$discount_percent}% Discount for {$quantity}", -$discount);
        }

    }

}