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
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        }
    }
    
    public function enqueue_admin_styles($hook) 
    {
        // Only load on our settings page
        if ($hook !== 'woocommerce_page_discount-settings') {
            return;
        }
        
        wp_enqueue_style(
            'pdfw-admin-styles',
            plugins_url('/assets/css/admin-style.css', dirname(dirname(dirname(__FILE__)))),
            [],
            '1.0.0'
        );
    }

    function add_admin_menu()
    {
        add_submenu_page(
            'woocommerce',
            'Discount settings',
            'Discount settings',
            'manage_options',
            'discount-settings',
            [$this, 'settings_page']
        );
    }

    function register_settings()
    {
        register_setting('wc_discount_settings', 'wc_discount_min_quantity');
        register_setting('wc_discount_settings', 'wc_discount_percent');
    }


    function settings_page()
    {
        ?>
        <div class="wrap pdfw-settings-page">
            <div class="pdfw-settings-header">
                <h2><?php esc_html_e('WooCommerce Product Discounts', 'pdfw-domain'); ?></h2>
                <p><?php esc_html_e('Configure your WooCommerce product discount rules. Set up minimum quantity requirements and discount percentages for your store.', 'pdfw-domain'); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('wc_discount_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wc_discount_min_quantity"><?php esc_html_e('Minimum quantity', 'pdfw-domain'); ?></label>
                        </th>
                        <td>
                            <input type="number" 
                                id="wc_discount_min_quantity"
                                name="wc_discount_min_quantity"
                                min="1"
                                value="<?php echo esc_attr(get_option('wc_discount_min_quantity', 2)); ?>">
                            <p class="description">
                                <?php esc_html_e('Minimum number of products required in cart to apply the discount.', 'pdfw-domain'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_discount_percent"><?php esc_html_e('Discount Percentage', 'pdfw-domain'); ?></label>
                        </th>
                        <td>
                            <input type="number" 
                                id="wc_discount_percent"
                                name="wc_discount_percent"
                                min="0"
                                max="100"
                                value="<?php echo esc_attr(get_option('wc_discount_percent', 10)); ?>">
                            <p class="description">
                                <?php esc_html_e('Percentage discount to apply when minimum quantity is reached (0-100).', 'pdfw-domain'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Save Discount Settings', 'pdfw-domain')); ?>
            </form>
        </div>
        <?php
    }



}