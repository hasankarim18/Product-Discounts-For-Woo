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
        register_setting('wc_discount_settings', 'pdfw_discount_rules');
    }

    private function get_discount_rules()
    {
        $rules = get_option('pdfw_discount_rules', []);
        return is_array($rules) ? $rules : [];
    }

    private function save_rule($quantity, $discount)
    {
        $rules = $this->get_discount_rules();
        $rules[] = [
            'id' => uniqid(),
            'quantity' => absint($quantity),
            'discount' => min(100, max(0, floatval($discount)))
        ];
        usort($rules, function($a, $b) {
            return $a['quantity'] - $b['quantity'];
        });
        update_option('pdfw_discount_rules', $rules);
    }

    private function delete_rule($rule_id)
    {
        $rules = $this->get_discount_rules();
        $rules = array_filter($rules, function($rule) use ($rule_id) {
            return $rule['id'] !== $rule_id;
        });
        update_option('pdfw_discount_rules', array_values($rules));
    }

    public function handle_form_submission()
    {
        if (!isset($_POST['pdfw_action'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'pdfw_manage_rules')) {
            wp_die('Security check failed');
        }

        switch ($_POST['pdfw_action']) {
            case 'add_rule':
                if (isset($_POST['quantity'], $_POST['discount'])) {
                    $this->save_rule($_POST['quantity'], $_POST['discount']);
                    wp_redirect(add_query_arg(['message' => 'rule_added']));
                    exit;
                }
                break;
            case 'delete_rule':
                if (isset($_POST['rule_id'])) {
                    $this->delete_rule($_POST['rule_id']);
                    wp_redirect(add_query_arg(['message' => 'rule_deleted']));
                    exit;
                }
                break;
        }
    }


    function settings_page()
    {
        $this->handle_form_submission();
        $rules = $this->get_discount_rules();
        ?>
        <div class="wrap pdfw-settings-page">
            <div class="pdfw-settings-header">
                <h2><?php esc_html_e('WooCommerce Product Discounts', 'pdfw-domain'); ?></h2>
                <p><?php esc_html_e('Configure discount rules based on cart quantity. Add multiple rules for different quantity thresholds.', 'pdfw-domain'); ?></p>
            </div>

            <?php
            if (isset($_GET['message'])) {
                switch ($_GET['message']) {
                    case 'rule_added':
                        echo '<div class="notice notice-success"><p>' . esc_html__('Discount rule added successfully.', 'pdfw-domain') . '</p></div>';
                        break;
                    case 'rule_deleted':
                        echo '<div class="notice notice-success"><p>' . esc_html__('Discount rule deleted successfully.', 'pdfw-domain') . '</p></div>';
                        break;
                }
            }
            ?>

            <div class="pdfw-add-rule">
                <h3><?php esc_html_e('Add New Discount Rule', 'pdfw-domain'); ?></h3>
                <form method="post" action="" class="pdfw-rule-form">
                    <?php wp_nonce_field('pdfw_manage_rules'); ?>
                    <input type="hidden" name="pdfw_action" value="add_rule">
                    
                    <div class="form-field">
                        <label for="quantity"><?php esc_html_e('Minimum Quantity', 'pdfw-domain'); ?></label>
                        <input type="number" 
                            id="quantity" 
                            name="quantity" 
                            min="1" 
                            required
                            value="2">
                        <p class="description"><?php esc_html_e('Minimum number of items in cart', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="form-field">
                        <label for="discount"><?php esc_html_e('Discount Percentage', 'pdfw-domain'); ?></label>
                        <input type="number" 
                            id="discount" 
                            name="discount" 
                            min="0" 
                            max="100" 
                            required
                            value="10">
                        <p class="description"><?php esc_html_e('Discount percentage (0-100)', 'pdfw-domain'); ?></p>
                    </div>

                    <?php submit_button(__('Add Rule', 'pdfw-domain')); ?>
                </form>
            </div>

            <?php if (!empty($rules)) : ?>
                <div class="pdfw-rules-table">
                    <h3><?php esc_html_e('Current Discount Rules', 'pdfw-domain'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Minimum Quantity', 'pdfw-domain'); ?></th>
                                <th><?php esc_html_e('Discount', 'pdfw-domain'); ?></th>
                                <th class="column-actions"><?php esc_html_e('Actions', 'pdfw-domain'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rules as $rule) : ?>
                                <tr>
                                    <td><?php echo esc_html($rule['quantity']); ?></td>
                                    <td><?php echo esc_html($rule['discount']); ?>%</td>
                                    <td>
                                        <form method="post" action="" style="display:inline;">
                                            <?php wp_nonce_field('pdfw_manage_rules'); ?>
                                            <input type="hidden" name="pdfw_action" value="delete_rule">
                                            <input type="hidden" name="rule_id" value="<?php echo esc_attr($rule['id']); ?>">
                                            <button type="submit" 
                                                class="button delete-rule" 
                                                onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this rule?', 'pdfw-domain'); ?>');">
                                                <?php esc_html_e('Delete', 'pdfw-domain'); ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('.pdfw-rule-form').on('submit', function(e) {
                var quantity = parseInt($('#quantity').val());
                var discount = parseInt($('#discount').val());
                
                if (isNaN(quantity) || quantity < 1) {
                    alert('<?php esc_html_e('Please enter a valid quantity (minimum 1)', 'pdfw-domain'); ?>');
                    e.preventDefault();
                    return false;
                }
                
                if (isNaN(discount) || discount < 0 || discount > 100) {
                    alert('<?php esc_html_e('Please enter a valid discount percentage (0-100)', 'pdfw-domain'); ?>');
                    e.preventDefault();
                    return false;
                }
            });
        });
        </script>
        <?php
    }

}