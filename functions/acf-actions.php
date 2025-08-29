<?php
    /** 
     * Licenckulcs:
     * b3JkZXJfaWQ9MTA2Mzk3fHR5cGU9ZGV2ZWxvcGVyfGRhdGU9MjAxNy0wNS0xNSAxMTowNzozMw==
     */

    if ( ! function_exists( 'acf_admin_notice' ) ) {
        /**
         * Checks if the Advanced Custom Fields (ACF) plugin is active.
         * If not active, displays an admin notice and prevents dependent theme code from running.
         *
         * @return void
         */
        function acf_admin_notice() {
            // If ACF's get_field() function does not exist, warn the admin.
            if ( ! function_exists( 'get_field' ) && is_admin() ) {
                echo '<div class="notice notice-error"><p><strong>Advanced Custom Fields</strong> is required for this theme. Please install and activate it.</p></div>';
            }
        }
        //add_action( 'admin_notices', 'acf_admin_notice' );
    }

    if ( ! function_exists( 'check_acf_before_theme_activation' ) ) {
        /**
         * Prevent theme activation if ACF is not active.
         *
         * @return void
         */
        function check_acf_before_theme_activation() {
            if ( ! function_exists( 'get_field' ) ) {
                // Switch back to previous theme
                switch_theme( WP_DEFAULT_THEME );
                
                // Remove 'Theme activated' message
                unset( $_GET['activated'] );

                // Show admin error
                add_action( 'admin_notices', function() {
                    echo '<div class="notice notice-error"><p><strong>Advanced Custom Fields</strong> is required for this theme. The theme has been deactivated.</p></div>';
                });
            }
        }
        add_action( 'after_switch_theme', 'check_acf_before_theme_activation' );
    }

    if ( ! function_exists( 'mytheme_register_acf_options_pages' ) ) {
        /**
         * Register ACF options pages for Sablon beállítások.
         *
         * This function adds a main "Sablon beállítások" options page,
         * along with "Header" and "Footer" subpages, using
         * Advanced Custom Fields (ACF) Pro's Options Page feature.
         *
         * @return void
         */
        function mytheme_register_acf_options_pages() {
            if ( function_exists( 'acf_add_options_page' ) ) {

                // Main options page
                acf_add_options_page( array(
                    'page_title'    => 'Sablon beállítások',
                    'menu_title'    => 'Sablon beállítások',
                    'menu_slug'     => 'theme-settings',
                    'capability'    => 'edit_posts',
                    'redirect'      => false,
                ) );
            }
        }
        add_action( 'acf/init', 'mytheme_register_acf_options_pages' );
    }

    if ( ! function_exists( 'add_theme_settings_link' ) ) {
        /**
         * Add a Theme Settings link with a gear icon to the WordPress Admin Bar.
         *
         * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
         * 
         * @return void
         */
        function add_theme_settings_link( $wp_admin_bar ) {
            if ( ! class_exists( 'WP_Admin_Bar' ) ) {
                return;
            }

            if ( ! is_object( $wp_admin_bar ) || ! method_exists( $wp_admin_bar, 'add_node' ) ) {
                return;
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $args = array(
                'id'    => 'theme-settings',
                'title' => '<span class="ab-icon dashicons dashicons-admin-generic"></span><span class="ab-label">Sablon beállítások</span>',
                'href'  => esc_url( admin_url( 'themes.php?page=theme-settings' ) ),
                'meta'  => array(
                    'class' => 'theme-settings-link',
                    'title' => esc_attr__( '', TEXT_DOMAIN )
                )
            );

            $wp_admin_bar->add_node( $args );
        }
        add_action( 'admin_bar_menu', 'add_theme_settings_link', 999 );
    }

    if ( ! function_exists( 'add_post_to_relationship_field' ) ) {
        /**
         * Add a related post ID to an ACF relationship field.
         *
         * @param int    $post_id         The ID of the post containing the relationship field.
         * @param string $field_key       The field key or field name of the relationship field.
         * @param int    $related_post_id The ID of the related post to add.
         *
         * @return void
         */
        function add_post_to_relationship_field( $post_id, $field_key, $related_post_id ) {
            $current_value = get_field( $field_key, $post_id );

            if ( ! is_array( $current_value ) ) {
                $current_value = array();
            }

            if ( ! in_array( $related_post_id, $current_value ) ) {
                $current_value[] = $related_post_id;
            }

            update_field( $field_key, $current_value, $post_id );
        }
    }

    if ( ! function_exists( 'remove_post_from_relationship_field' ) ) {
        /**
         * Remove a related post ID from an ACF relationship field.
         *
         * @param int    $post_id         The ID of the post containing the relationship field.
         * @param string $field_key       The field key or field name of the relationship field.
         * @param int    $related_post_id The ID of the related post to remove.
         *
         * @return void
         */
        function remove_post_from_relationship_field( $post_id, $field_key, $related_post_id ) {
            $current_value = get_field( $field_key, $post_id );

            if ( is_array( $current_value ) ) {
                $key = array_search( $related_post_id, $current_value );

                if ( $key !== false ) {
                    unset( $current_value[ $key ] );

                    // Reindex array after removal
                    $current_value = array_values( $current_value );

                    update_field( $field_key, $current_value, $post_id );
                }
            }
        }
    }

    // Populate ACF select field options with Gravity Forms forms
    if ( ! function_exists( 'acf_populate_gform_ids' ) ) {
        /**
         * Populate ACF select field options with available Gravity Forms forms.
         *
         * @param array $field The ACF field array.
         * @return array Modified field array with form choices.
         */
        function acf_populate_gform_ids( $field ) {
            if ( ! class_exists( 'GFFormsModel' ) ) {
                return $field;
            }

            $choices = [ 'none' => __( 'None', TEXT_DOMAIN ) ];
            $forms   = GFFormsModel::get_forms();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $form ) {
                    $choices[ $form->id ] = $form->title;
                }
            }

            $field['choices'] = $choices;

            return $field;
        }
        add_filter( 'acf/load_field/name=gform', 'acf_populate_gform_ids' );
    }