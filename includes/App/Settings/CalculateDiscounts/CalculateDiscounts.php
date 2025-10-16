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

    private function get_applicable_discount($total_quantity)
    {
        $rules = get_option('pdfw_discount_rules', []);
        if (!is_array($rules)) {
            return null;
        }

        // Sort rules by quantity in descending order to get the highest applicable discount
        usort($rules, function($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

        // Find the first rule that applies (highest discount for the quantity)
        foreach ($rules as $rule) {
            if ($total_quantity >= $rule['quantity']) {
                return $rule;
            }
        }

        return null;
    }

    public function cart_discount_rules($cart)
    {
        if (is_admin()) {
            return;
        }

        $total_quantity = 0;

        foreach ($cart->get_cart() as $item) {
            $total_quantity += $item['quantity'];
        }

        $applicable_rule = $this->get_applicable_discount($total_quantity);

        if ($applicable_rule) {
            $discount = $cart->get_subtotal() * $applicable_rule['discount'] / 100;
            $cart->add_fee(
                sprintf(
                    __('%d%% Discount for %d+ items', 'pdfw-domain'),
                    $applicable_rule['discount'],
                    $applicable_rule['quantity']
                ),
                -$discount
            );
        }
    }

}