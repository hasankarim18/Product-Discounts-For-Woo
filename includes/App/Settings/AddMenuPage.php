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
        }
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
        <div class="wrap">
            <h2>Discount settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('wc_discount_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Minimum quantity', 'pdfw-domain') ?></th>
                        <td>
                            <input type="number" name="wc_discount_min_quantity"
                                value="<?php echo esc_attr(get_option('wc_discount_min_quantity', 2)); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Discount Percent</th>
                        <td>
                            <input type="number" name="wc_discount_percent"
                                value="<?php echo esc_attr(get_option('wc_discount_percent', 10)); ?>">
                        </td>
                    </tr>
                </table>
                <?php
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }



}