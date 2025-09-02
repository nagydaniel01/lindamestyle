<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set timezone
$timezone_string = get_option('timezone_string') ?: 'UTC';
date_default_timezone_set($timezone_string);

// Get current user
$current_user = wp_get_current_user();
if ( ! $current_user instanceof WP_User ) {
    wp_die(__('Unable to get current user data.', TEXT_DOMAIN));
}

$current_user_id = $current_user->ID;
$user_name       = $current_user->display_name ?: $current_user->first_name;

// Last login
$last_login = function_exists('get_last_login') ? get_last_login($current_user_id) : null;
$today      = current_time('Y-m-d H:i');
$start_date = $last_login ? date('Y-m-d H:i', strtotime($last_login)) : '1970-01-01 00:00';
$end_date   = $today;

// Posts per page
$posts_per_page = (int) get_option('posts_per_page');

// Latest posts
$latest_post = new WP_Query([
    'post_type'      => ['post', 'knowledge_base'],
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'date_query'     => [
        [
            'after'     => $start_date,
            'before'    => $end_date,
            'inclusive' => true,
        ],
    ],
    'orderby'        => 'rand',
]);

// Recently viewed posts
$recently_viewed_ids = function_exists('get_recently_viewed') ? get_recently_viewed() : [0];
$public_post_types   = array_diff(get_post_types(['public' => true], 'names'), ['page']);

$recently_viewed_posts = new WP_Query([
    'post_type'      => $public_post_types,
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'post__in'       => $recently_viewed_ids,
    'orderby'        => 'post__in',
]);

if ( ! function_exists('render_membership_table') ) {
    function render_membership_table($user_membership, $current_user_id = 0) {
        if ( ! is_a($user_membership, 'WC_Memberships_User_Membership') ) return;

        $plan_name  = $user_membership->plan ? $user_membership->plan->get_name() : __('N/A', 'woocommerce-memberships');
        $status     = wc_memberships_get_user_membership_status_name($user_membership->get_status());
        $start_date = $user_membership->has_start_date() ? date_i18n(wc_date_format(), $user_membership->get_local_start_date('timestamp')) : __('N/A', 'woocommerce-memberships');
        $end_date   = $user_membership->has_end_date() ? date_i18n(wc_date_format(), $user_membership->get_local_end_date('timestamp')) : __('N/A', 'woocommerce-memberships');
        $actions    = function_exists('wc_memberships_get_members_area_action_links') ? wc_memberships_get_members_area_action_links('my-membership-details', $user_membership) : '';

        $next_bill_on = __('N/A', 'woocommerce-memberships');

        if ( class_exists('WC_Subscriptions') && function_exists('wcs_get_users_subscriptions') ) {
            $subscriptions = wcs_get_users_subscriptions($current_user_id);
            if ( ! empty($subscriptions) ) {
                foreach ( $subscriptions as $subscription ) {
                    if ( is_a($subscription, 'WC_Subscription') ) {
                        $next_payment = $subscription->get_date_to_display('next_payment');
                        if ( $next_payment ) {
                            $next_bill_on = $next_payment;
                            break;
                        }
                    }
                }
            }
        }

        echo '<table class="shop_table shop_table_responsive my_account_memberships">';
        echo '<thead><tr><th colspan="2">' . esc_html__('Membership Details', 'woocommerce-memberships') . '</th></tr></thead>';
        echo '<tbody>';
        echo '<tr><td>' . esc_html__('Status', 'woocommerce-memberships') . '</td><td>' . esc_html($status) . '</td></tr>';
        echo '<tr><td>' . esc_html__('Start date', 'woocommerce-memberships') . '</td><td>' . esc_html($start_date) . '</td></tr>';
        echo '<tr><td>' . esc_html__('Expires', 'woocommerce-memberships') . '</td><td>' . esc_html($end_date) . '</td></tr>';
        echo '<tr><td>' . esc_html__('Next Bill On', 'woocommerce-memberships') . '</td><td>' . esc_html($next_bill_on) . '</td></tr>';
        echo '<tr><td>' . esc_html__('Actions', 'woocommerce-memberships') . '</td><td>' . $actions . '</td></tr>';
        echo '</tbody></table>';
    }
}

if ( ! function_exists('render_subscription_table') ) {
    function render_subscription_table($subscriptions) {
        if ( empty($subscriptions) ) return;

        echo '<table class="shop_table shop_table_responsive my_account_subscriptions">';
        echo '<thead><tr><th colspan="2">' . esc_html__('Subscription Details', 'woocommerce-subscriptions') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ( $subscriptions as $subscription ) {
            if ( ! is_a($subscription, 'WC_Subscription') ) continue;

            $status         = wcs_get_subscription_status_name($subscription->get_status());
            $start_date     = $subscription->get_date_to_display('start_date') ?: __('N/A', 'woocommerce-subscriptions');
            $last_order     = $subscription->get_date_to_display('last_order_date_created') ?: __('N/A', 'woocommerce-subscriptions');
            $next_payment   = $subscription->get_date_to_display('next_payment') ?: __('N/A', 'woocommerce-subscriptions');
            $payment_method = $subscription->get_payment_method_to_display() ?: __('N/A', 'woocommerce-subscriptions');
            $actions        = wcs_get_all_user_actions_for_subscription($subscription, get_current_user_id());

            echo '<tr><td>' . esc_html__('Status', 'woocommerce-subscriptions') . '</td><td>' . esc_html($status) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Start date', 'woocommerce-subscriptions') . '</td><td>' . esc_html($start_date) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Last order', 'woocommerce-subscriptions') . '</td><td>' . esc_html($last_order) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Next payment', 'woocommerce-subscriptions') . '</td><td>' . esc_html($next_payment) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Payment method', 'woocommerce-subscriptions') . '</td><td>' . esc_html($payment_method) . '</td></tr>';

            echo '<tr><td>' . esc_html__('Actions', 'woocommerce-subscriptions') . '</td><td>';
            if ( ! empty($actions) ) {
                foreach ( $actions as $key => $action ) {
                    $classes   = ['woocommerce-button', 'button', sanitize_html_class($key)];
                    $classes[] = isset($action['block_ui']) && $action['block_ui'] ? 'wcs_block_ui_on_click' : '';
                    echo '<a href="' . esc_url($action['url']) . '" class="' . esc_attr(trim(implode(' ', $classes))) . '">';
                    echo esc_html($action['name']);
                    echo '</a> ';
                }
            } else {
                echo esc_html__('N/A', 'woocommerce-subscriptions');
            }
            echo '</td></tr>';
        }

        echo '</tbody></table>';
    }
}

// Last login display
if ( $last_login ) {
    echo wpautop( esc_html__('Your last login was on: ', TEXT_DOMAIN) . esc_html(date_i18n('Y. F d., H:i', strtotime($last_login))) );
}

// Memberships notices
if ( class_exists('WC_Memberships') ) {
    echo '<div class="woocommerce-notices-wrapper">';
    $memberships = wc_memberships_get_user_active_memberships($current_user_id);

    if ( empty($memberships) ) {
        wc_add_notice(__('You have no active membership.', 'woocommerce-memberships'), 'notice');
    } else {
        foreach ($memberships as $membership) {
            $plan_name = $membership->get_plan()->get_name();
            wc_add_notice(sprintf(__('Your active membership plan: %s', 'woocommerce-memberships'), esc_html($plan_name)), 'notice');
        }
    }

    wc_print_notices();
    echo '</div>';
}

// Display latest posts slider
if ( $latest_post->have_posts() ) : 
    $template_args = ['post_type' => esc_attr($post_type)];
    $template      = locate_template("template-parts/cards/card-related.php");
    ?>
    <div class="section__popular-posts">
        <h2 class="section__title"><?php _e('Have you read these?', TEXT_DOMAIN); ?></h2>
        <div class="slider slider--related" id="popular-post-slider">
            <div class="slider__list">
                <?php while ($latest_post->have_posts()) : $latest_post->the_post(); ?>
                    <div class="slider__item">
                        <?php
                        if ( ! empty($template) ) {
                            get_template_part('template-parts/cards/card', 'related', $template_args);
                        } else {
                            get_template_part('template-parts/cards/card', 'default', $template_args);
                        }
                        ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="slider__controls"></div>
        </div>
    </div>
<?php endif; ?>

<?php
// Display recently viewed posts slider
if ( $recently_viewed_posts->have_posts() ) :
    $template_args = ['post_type' => esc_attr($post_type)];
    $template      = locate_template("template-parts/cards/card-related.php");
    ?>
    <div class="section__recently-viewed-posts">
        <h2 class="section__title"><?php _e('Recently viewed', TEXT_DOMAIN); ?></h2>
        <div class="slider slider--related" id="recently-viewed-posts-slider">
            <div class="slider__list">
                <?php while ($recently_viewed_posts->have_posts()) : $recently_viewed_posts->the_post(); ?>
                    <div class="slider__item">
                        <?php
                        if ( ! empty($template) ) {
                            get_template_part('template-parts/cards/card', 'related', $template_args);
                        } else {
                            get_template_part('template-parts/cards/card', 'default', $template_args);
                        }
                        ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="slider__controls"></div>
        </div>
    </div>
<?php endif; ?>

<?php
// Membership tables
if ( class_exists('WC_Memberships') ) {
    foreach ($memberships as $membership) {
        render_membership_table($membership, $current_user_id);
    }
}

// Subscription tables
if ( class_exists('WC_Subscriptions') ) {
    $subscriptions = wcs_get_users_subscriptions($current_user_id);
    render_subscription_table($subscriptions);
}
?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
