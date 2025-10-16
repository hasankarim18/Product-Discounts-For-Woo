<?php

namespace Hasan\ProductDiscountsForWoo\App\Settings;

if (!defined('ABSPATH')) {
    exit;
}

class AdminPro
{
    use \Hasan\ProductDiscountsForWoo\App\Traits\Singleton;

    public function init()
    {
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_pro_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        }
    }

    public function enqueue_admin_styles($hook)
    {
        if ($hook !== 'woocommerce_page_discount-rules-pro') {
            return;
        }

        wp_enqueue_style(
            'pdfw-admin-styles',
            plugins_url('/assets/css/admin-style.css', dirname(dirname(dirname(__FILE__)))),
            [],
            '1.0.0'
        );

        // Enqueue Dashicons if not already loaded
        wp_enqueue_style('dashicons');
    }

    public function add_pro_menu()
    {
        add_submenu_page(
            'woocommerce',
            'Discount Rules Pro',
            'Discount Rules Pro',
            'manage_options',
            'discount-rules-pro',
            [$this, 'render_pro_page']
        );
    }

    public function render_pro_page()
    {
        ?>
        <div class="wrap pdfw-settings-page">
            <div class="pdfw-settings-header">
                <h2><?php esc_html_e('WooCommerce Product Discounts Pro', 'pdfw-domain'); ?></h2>
                <p><?php esc_html_e('Unlock powerful discount features to boost your sales and customer engagement.', 'pdfw-domain'); ?></p>
            </div>

            <div class="pdfw-pro-features">
                <h3><?php esc_html_e('Pro Features', 'pdfw-domain'); ?></h3>
                <div class="pdfw-feature-grid">
                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-groups"></span> <?php esc_html_e('User Role Based Discounts', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Set different discount rules for different user roles. Reward your wholesale customers or membership holders with special discounts.', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-calendar-alt"></span> <?php esc_html_e('Time-Based Discounts', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Create limited-time offers with start and end dates. Perfect for seasonal sales and holiday promotions.', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-category"></span> <?php esc_html_e('Category-Specific Rules', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Apply discounts to specific product categories. Different quantity rules for different product types.', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-chart-area"></span> <?php esc_html_e('Bulk Purchase Rules', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Create sophisticated bulk purchase discounts with tiered pricing for different quantity ranges.', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-testimonial"></span> <?php esc_html_e('Custom Messages', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Display custom messages to encourage customers to buy more. Show progress towards next discount tier.', 'pdfw-domain'); ?></p>
                    </div>

                    <div class="pdfw-feature-item">
                        <h4><span class="dashicons dashicons-analytics"></span> <?php esc_html_e('Discount Analytics', 'pdfw-domain'); ?></h4>
                        <p><?php esc_html_e('Track the performance of your discount rules. See which rules drive more sales.', 'pdfw-domain'); ?></p>
                    </div>
                </div>
            </div>

            <div class="pdfw-pricing-section">
                <h3><?php esc_html_e('Pricing Plans', 'pdfw-domain'); ?></h3>
                <div class="pdfw-pricing-grid">
                    <!-- Free Plan -->
                    <div class="pdfw-pricing-item">
                        <div class="pdfw-pricing-header">
                            <h4><?php esc_html_e('Free', 'pdfw-domain'); ?></h4>
                            <div class="pdfw-price">$0<span>/year</span></div>
                        </div>
                        <div class="pdfw-pricing-features">
                            <ul>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Basic Quantity Discounts', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Multiple Discount Rules', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Simple Cart Discounts', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-no"></span> <?php esc_html_e('User Role Discounts', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-no"></span> <?php esc_html_e('Time-Based Rules', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-no"></span> <?php esc_html_e('Analytics', 'pdfw-domain'); ?></li>
                            </ul>
                        </div>
                        <div class="pdfw-pricing-button">
                            <button class="button" disabled><?php esc_html_e('Current Plan', 'pdfw-domain'); ?></button>
                        </div>
                    </div>

                    <!-- Gold Plan -->
                    <div class="pdfw-pricing-item featured">
                        <div class="pdfw-pricing-header">
                            <h4><?php esc_html_e('Gold', 'pdfw-domain'); ?></h4>
                            <div class="pdfw-price">$49<span>/year</span></div>
                        </div>
                        <div class="pdfw-pricing-features">
                            <ul>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Everything in Free', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('User Role Discounts', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Time-Based Rules', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Category Rules', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-no"></span> <?php esc_html_e('Custom Messages', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-no"></span> <?php esc_html_e('Analytics Dashboard', 'pdfw-domain'); ?></li>
                            </ul>
                        </div>
                        <div class="pdfw-pricing-button">
                            <button class="button button-primary"><?php esc_html_e('Upgrade Now', 'pdfw-domain'); ?></button>
                        </div>
                    </div>

                    <!-- Platinum Plan -->
                    <div class="pdfw-pricing-item">
                        <div class="pdfw-pricing-header">
                            <h4><?php esc_html_e('Platinum', 'pdfw-domain'); ?></h4>
                            <div class="pdfw-price">$99<span>/year</span></div>
                        </div>
                        <div class="pdfw-pricing-features">
                            <ul>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Everything in Gold', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Custom Messages', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Advanced Analytics', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Priority Support', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('API Access', 'pdfw-domain'); ?></li>
                                <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Custom Development', 'pdfw-domain'); ?></li>
                            </ul>
                        </div>
                        <div class="pdfw-pricing-button">
                            <button class="button button-primary"><?php esc_html_e('Upgrade Now', 'pdfw-domain'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}