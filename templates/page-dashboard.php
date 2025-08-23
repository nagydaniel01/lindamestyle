<?php /* Template Name: Dashboard */ ?>

<?php get_header(); ?>

<?php
    /**
     * Displays a personalized dashboard for logged-in users
     */

    // Get the page ID using this template
    $current_template = 'templates/page-dashboard.php';
    $page_template = get_pages([
        'meta_key'   => '_wp_page_template',
        'meta_value' => $current_template,
    ]);

    $page_id = $page_template[0]->ID ?? 0;

    // Redirect non-logged-in users to the homepage
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url());
        exit;
    }

    // Set timezone based on WordPress settings
    $timezone_string = get_option('timezone_string') ?: 'UTC';
    date_default_timezone_set($timezone_string);

    // Get current user data
    $current_user = wp_get_current_user();
    if (!$current_user instanceof WP_User) {
        wp_die(__('Unable to get current user data.', 'your-textdomain'));
    }

    $current_user_id = $current_user->ID;
    $first_name      = $current_user->first_name ?? '';
    $last_name       = $current_user->last_name ?? '';
    $user_name       = $first_name ? $first_name : $current_user->display_name;

    // Last login and date range for new content
    $last_login = function_exists('get_last_login') ? get_last_login($current_user_id) : null;
    $today      = current_time('Y-m-d H:i');

    $start_date = $last_login ? date('Y-m-d H:i', strtotime($last_login)) : '1970-01-01 00:00';
    $end_date   = $today;

    // Posts per page
    $posts_per_page = (int) get_option('posts_per_page');

    // Fetch latest posts and knowledge base articles that were published after the user's last login until now. Posts are randomly ordered to provide variety on each visit.
    $latest_post = new WP_Query([
        'post_type'      => ['post', 'knowledge_base'],
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'date_query'    => [
            [
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ],
        ],
        'orderby'        => 'rand'
    ]);

    // Fetch recently viewed posts
    $recently_viewed_posts_ids = function_exists('get_recently_viewed') ? get_recently_viewed() : [];

    // Ensure the array is never empty to prevent WP_Query errors
    if (empty($recently_viewed_posts_ids)) {
        $recently_viewed_posts_ids = [0]; // 0 ensures no posts are matched
    }

    // Get all public post types
    $public_post_types = get_post_types(['public' => true], 'names');

    // Exclude 'page' post type
    $public_post_types = array_diff($public_post_types, ['page']);

    if (!empty($recently_viewed_posts_ids)) {
        $recently_viewed_posts = new WP_Query([
            'post_type'      => $public_post_types,
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'post__in'       => $recently_viewed_posts_ids,
            'orderby'        => 'post__in',
        ]);
    }

    // Helper function to render memberships table
    function render_membership_table($user_membership, $current_user_id = 0) {
        if (!is_a($user_membership, 'WC_Memberships_User_Membership')) return;

        $plan_name = $user_membership->plan ? $user_membership->plan->get_name() : __('N/A', 'woocommerce-memberships');
        $status = wc_memberships_get_user_membership_status_name($user_membership->get_status());
        $start_date = $user_membership->has_start_date() ? date_i18n(wc_date_format(), $user_membership->get_local_start_date('timestamp')) : __('N/A', 'woocommerce-memberships');
        $end_date = $user_membership->has_end_date() ? date_i18n(wc_date_format(), $user_membership->get_local_end_date('timestamp')) : __('N/A', 'woocommerce-memberships');
        $actions = function_exists('wc_memberships_get_members_area_action_links') ? wc_memberships_get_members_area_action_links('my-membership-details', $user_membership) : '';

        // Default next bill date
        $next_bill_on = __('N/A', 'woocommerce-memberships');

        // Get subscription associated with this membership, if WC_Subscriptions exists
        if (class_exists('WC_Subscriptions') && function_exists('wcs_get_users_subscriptions')) {
            $subscriptions = wcs_get_users_subscriptions($current_user_id);
            if (!empty($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    if (is_a($subscription, 'WC_Subscription')) {
                        $next_payment = $subscription->get_date_to_display('next_payment');
                        if ($next_payment) {
                            $next_bill_on = $next_payment;
                            break; // Use the first active subscription
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
        echo '<tr><td>' . esc_html__('Next Bill On', 'woocommerce-memberships') . '</td><td>' . esc_html($next_bill_on) . '</td></tr>'; // Added Next Bill On
        echo '<tr><td>' . esc_html__('Actions', 'woocommerce-memberships') . '</td><td>' . $actions . '</td></tr>';
        echo '</tbody></table>';
    }

    // Helper function to render subscriptions table
    function render_subscription_table($subscriptions) {
        if (empty($subscriptions)) return;

        echo '<table class="shop_table shop_table_responsive my_account_subscriptions">';
        echo '<thead><tr><th colspan="2">' . esc_html__('Subscription Details', 'woocommerce-subscriptions') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($subscriptions as $subscription) {
            if (!is_a($subscription, 'WC_Subscription')) continue;

            $status        = wcs_get_subscription_status_name($subscription->get_status());
            $start_date    = $subscription->get_date_to_display('start_date') ?: __('N/A', 'woocommerce-subscriptions');
            $last_order    = $subscription->get_date_to_display('last_order_date_created') ?: __('N/A', 'woocommerce-subscriptions');
            $next_payment  = $subscription->get_date_to_display('next_payment') ?: __('N/A', 'woocommerce-subscriptions');
            $payment_method= $subscription->get_payment_method_to_display() ?: __('N/A', 'woocommerce-subscriptions');
            $actions       = wcs_get_all_user_actions_for_subscription( $subscription, get_current_user_id() );

            echo '<tr><td>' . esc_html__('Status', 'woocommerce-subscriptions') . '</td><td>' . esc_html($status) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Start date', 'woocommerce-subscriptions') . '</td><td>' . esc_html($start_date) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Last order', 'woocommerce-subscriptions') . '</td><td>' . esc_html($last_order) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Next payment', 'woocommerce-subscriptions') . '</td><td>' . esc_html($next_payment) . '</td></tr>';
            echo '<tr><td>' . esc_html__('Payment method', 'woocommerce-subscriptions') . '</td><td>' . esc_html($payment_method) . '</td></tr>';

            // Render actions
            echo '<tr><td>' . esc_html__('Actions', 'woocommerce-subscriptions') . '</td><td>';
            if ( ! empty( $actions ) ) {
                foreach ( $actions as $key => $action ) {
                    $classes   = [ 'woocommerce-button', 'button', sanitize_html_class( $key ) ];
                    $classes[] = isset( $action['block_ui'] ) && $action['block_ui'] ? 'wcs_block_ui_on_click' : '';

                    if ( wc_wp_theme_get_element_class_name( 'button' ) ) {
                        $classes[] = wc_wp_theme_get_element_class_name( 'button' );
                    }

                    echo '<a href="' . esc_url( $action['url'] ) . '" class="' . esc_attr( trim( implode( ' ', $classes ) ) ) . '">';
                    echo esc_html( $action['name'] );
                    echo '</a> ';
                }
            } else {
                echo esc_html__('N/A', 'woocommerce-subscriptions');
            }
            echo '</td></tr>';
        }

        echo '</tbody></table>';
    }
?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <main class="page page--default">
            <div class="container">
                <div class="page__header">
                    <h1 class="page__title"><?php printf(esc_html__('Hello %s!', TEXT_DOMAIN), esc_html($user_name)); ?></h1>
                    <?php 
                        if ( ! empty( $last_login ) ) {
                            $formatted_date = date_i18n( 'Y. F d., H:i', strtotime($last_login) );
                            echo wpautop( esc_html__('Your last login was on: ', TEXT_DOMAIN) . esc_html($formatted_date) );
                        }
                    ?>
                </div>

                <div class="page__content">
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        
                        <?php
                            if ( class_exists( 'WC_Memberships' ) ) {
                                $memberships = wc_memberships_get_user_active_memberships( $current_user_id );

                                if ( empty( $memberships ) ) {
                                    wc_add_notice( __( 'You have no active membership.', 'woocommerce-memberships' ), 'notice' );
                                    wc_print_notices();
                                }
                            }
                        ?>

                        <h2><?php echo esc_html('My Account', TEXT_DOMAIN); ?></h2>
                        <div class="row">
                            <?php
                            $account_links = wc_get_account_menu_items(); // Get all My Account endpoints
                            foreach ($account_links as $endpoint => $label) :
                                $url = wc_get_account_endpoint_url($endpoint);
                            ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card text-center h-100 shadow-sm">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <h5 class="card-title"><?php echo esc_html__($label, TEXT_DOMAIN); ?></h5>
                                            <a href="<?php echo esc_url($url); ?>" class="btn btn-primary mt-3">
                                                <?php echo esc_html('Go', TEXT_DOMAIN); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>

                    <?php if ( $latest_post->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__popular-posts">
                            <h2 class="section__title"><?php _e('Have you read these?', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="popular-post-slider">
                                <div class="slider__list">
                                    <?php while ( $latest_post->have_posts() ) : $latest_post->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
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

                    <?php if ( $recently_viewed_posts->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__recently-viewed-posts">
                            <h2 class="section__title"><?php _e('Recently viewed', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="recently-viewed-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $recently_viewed_posts->have_posts() ) : $recently_viewed_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
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

                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <?php
                            // Memberships
                            if (class_exists('WC_Memberships')) {
                                foreach ($memberships as $membership) {
                                    render_membership_table($membership);
                                }
                            }

                            // Subscriptions
                            if (class_exists('WC_Subscriptions')) {
                                $subscriptions = wcs_get_users_subscriptions($current_user_id);
                                render_subscription_table($subscriptions);
                            }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
