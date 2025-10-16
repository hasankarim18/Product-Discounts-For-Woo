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

        wp_enqueue_script(
            'pdfw-admin-script',
            plugins_url('/assets/js/admin-script.js', dirname(dirname(dirname(__FILE__)))),
            ['jquery'],
            '1.0.0',
            true
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

        $redirect_url = admin_url('admin.php?page=discount-settings');

        switch ($_POST['pdfw_action']) {
            case 'add_rule':
                if (isset($_POST['quantity'], $_POST['discount'])) {
                    $this->save_rule($_POST['quantity'], $_POST['discount']);
                    wp_safe_redirect(add_query_arg('message', 'rule_added', $redirect_url));
                    exit;
                }
                break;
            case 'update_rule':
                if (isset($_POST['rule_id'], $_POST['quantity'], $_POST['discount'])) {
                    $this->update_rule($_POST['rule_id'], $_POST['quantity'], $_POST['discount']);
                    wp_safe_redirect(add_query_arg('message', 'rule_updated', $redirect_url));
                    exit;
                }
                break;
            case 'delete_rule':
                if (isset($_POST['rule_id'])) {
                    $this->delete_rule($_POST['rule_id']);
                    wp_safe_redirect(add_query_arg('message', 'rule_deleted', $redirect_url));
                    exit;
                }
                break;
        }
    }


    private function get_rule($rule_id)
    {
        $rules = $this->get_discount_rules();
        foreach ($rules as $rule) {
            if ($rule['id'] === $rule_id) {
                return $rule;
            }
        }
        return null;
    }

    private function update_rule($rule_id, $quantity, $discount)
    {
        $rules = $this->get_discount_rules();
        foreach ($rules as &$rule) {
            if ($rule['id'] === $rule_id) {
                $rule['quantity'] = absint($quantity);
                $rule['discount'] = min(100, max(0, floatval($discount)));
                break;
            }
        }
        usort($rules, function($a, $b) {
            return $a['quantity'] - $b['quantity'];
        });
        update_option('pdfw_discount_rules', $rules);
    }

    function settings_page()
    {
        $this->handle_form_submission();
        $rules = $this->get_discount_rules();
        $edit_mode = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : '';
        
        if ($edit_mode) {
            $rule = $this->get_rule($edit_mode);
            if (!$rule) {
                wp_redirect(admin_url('admin.php?page=discount-settings'));
                exit;
            }
            $this->render_edit_form($rule);
            return;
        }
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
                    case 'rule_updated':
                        echo '<div class="notice notice-success"><p>' . esc_html__('Discount rule updated successfully.', 'pdfw-domain') . '</p></div>';
                        break;
                }
            }
            ?>

            <button type="button" class="button button-primary" id="pdfw-add-new-rule">
                <?php esc_html_e('Add New Rule', 'pdfw-domain'); ?>
            </button>

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

                    <div class="pdfw-button-group">
                        <?php submit_button(__('Add Rule', 'pdfw-domain'), 'primary', 'submit', false); ?>
                        <button type="button" class="button pdfw-cancel-add"><?php esc_html_e('Cancel', 'pdfw-domain'); ?></button>
                    </div>
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
                                        <a href="<?php echo esc_url(add_query_arg(['edit' => $rule['id']])); ?>" 
                                           class="button">
                                            <?php esc_html_e('Edit', 'pdfw-domain'); ?>
                                        </a>
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
        <?php
    }

    private function render_edit_form($rule)
    {
        ?>
        <div class="wrap">
            <div class="pdfw-edit-form-container">
                <h2><?php esc_html_e('Edit Discount Rule', 'pdfw-domain'); ?></h2>
                <form method="post" action="" class="pdfw-rule-form">
                    <?php wp_nonce_field('pdfw_manage_rules'); ?>
                    <input type="hidden" name="pdfw_action" value="update_rule">
                    <input type="hidden" name="rule_id" value="<?php echo esc_attr($rule['id']); ?>">
                    
                    <div class="form-field">
                        <label for="quantity"><?php esc_html_e('Minimum Quantity', 'pdfw-domain'); ?></label>
                        <input type="number" 
                            id="quantity" 
                            name="quantity" 
                            min="1" 
                            required
                            value="<?php echo esc_attr($rule['quantity']); ?>">
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
                            value="<?php echo esc_attr($rule['discount']); ?>">
                        <p class="description"><?php esc_html_e('Discount percentage (0-100)', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-button-group">
                        <?php submit_button(__('Update Rule', 'pdfw-domain'), 'primary', 'submit', false); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=discount-settings')); ?>" 
                           class="button">
                            <?php esc_html_e('Cancel', 'pdfw-domain'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
        
    }

