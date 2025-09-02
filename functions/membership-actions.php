<?php
    if ( ! class_exists( 'WC_Memberships' ) ) return;

    if ( ! function_exists( 'my_redirect_cpt_to_landing' ) ) {

        /**
         * Redirect Knowledge Base CPT archive and single pages to a landing page
         * if the user is not logged in, not an administrator, or does not have an optional active membership.
         *
         * @return void
         */
        function my_redirect_cpt_to_landing() {
            // CPT slug to protect
            $cpt_slug = 'knowledge_base';

            // Landing page URL (fallback to home if empty)
            $landing_page_url = home_url( '/content-restricted/' );
            if ( empty( $landing_page_url ) ) {
                $landing_page_url = home_url( '/' );
            }

            // Get current user ID
            $user_id = get_current_user_id();

            // Optional: define required membership plans (slugs or IDs)
            $required_membership_plans = array(); // leave empty to allow all active members

            // Default: assume user has no membership access
            $has_membership_access = false;

            // Check memberships only if WooCommerce Memberships is active and user is logged in
            if ( function_exists( 'wc_memberships_is_user_active_member' ) && $user_id ) {

                // If no specific plans are defined, any active membership counts
                if ( empty( $required_membership_plans ) ) {
                    $has_membership_access = wc_memberships_is_user_active_member( $user_id );
                } else {
                    // Check for specific plans
                    foreach ( $required_membership_plans as $plan ) {
                        if ( wc_memberships_is_user_active_member( $user_id, $plan ) ) {
                            $has_membership_access = true;
                            break;
                        }
                    }
                }
            }

            // Redirect if user is not logged in OR (not admin and no membership access)
            if ( ! is_user_logged_in() || ( ! current_user_can( 'administrator' ) && ! $has_membership_access ) ) {

                // Redirect CPT archive
                if ( is_post_type_archive( $cpt_slug ) ) {
                    wp_safe_redirect( $landing_page_url );
                    exit;
                }

                // Redirect CPT single pages
                if ( is_singular( $cpt_slug ) ) {
                    wp_safe_redirect( $landing_page_url );
                    exit;
                }
            }
        }

        add_action( 'template_redirect', 'my_redirect_cpt_to_landing' );
    }
