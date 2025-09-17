<?php
    /**
     * Register custom meta box for the 'attendee' post type.
     */
    if ( ! function_exists( 'attendee_add_meta_box' ) ) {
        function attendee_add_meta_box() {
            add_meta_box(
                'attendee_details',
                __( 'Résztvevő adatai', TEXT_DOMAIN ),
                'attendee_meta_box_callback',
                'attendee',
                'normal',
                'high'
            );
        }
        add_action( 'add_meta_boxes', 'attendee_add_meta_box' );
    }

    /**
     * Render the meta box content for the 'attendee' post type.
     *
     * @param WP_Post $post The post object.
     */
    if ( ! function_exists( 'attendee_meta_box_callback' ) ) {
        function attendee_meta_box_callback( $post ) {
            // Add nonce for security
            wp_nonce_field( 'attendee_save_meta_box', 'attendee_meta_box_nonce' );

            // Retrieve existing values
            $email     = get_post_meta( $post->ID, 'attendee_email', true );
            $name      = get_post_meta( $post->ID, 'attendee_name', true );
            $event_id  = get_post_meta( $post->ID, 'event_id', true );
            $event_url = get_permalink( $event_id );
            ?>
            <p>
                <label for="attendee_name"><strong><?php _e( 'Név:', TEXT_DOMAIN ); ?></strong></label><br />
                <input type="text" id="attendee_name" name="attendee_name" value="<?php echo esc_attr( $name ); ?>" class="widefat" readonly />
            </p>
            <p>
                <label for="attendee_email"><strong><?php _e( 'Email:', TEXT_DOMAIN ); ?></strong></label><br />
                <input type="email" id="attendee_email" name="attendee_email" value="<?php echo esc_attr( $email ); ?>" class="widefat" readonly />
            </p>
            <p>
                <label for="event_id"><strong><?php _e( 'Esemény ID:', TEXT_DOMAIN ); ?></strong></label><br />
                <input type="text" id="event_id" name="event_id" value="<?php echo esc_attr( $event_id ); ?>" class="widefat" readonly />
            </p>
                    <p>
            <label for="event_url"><strong><?php _e( 'Esemény URL:', TEXT_DOMAIN ); ?></strong></label><br />
            <input type="url" id="event_url" value="<?php echo esc_url( $event_url ); ?>" class="widefat" readonly />
        </p>
            <?php
        }
    }

    /**
     * Save the meta box fields when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    if ( ! function_exists( 'attendee_save_meta_box_data' ) ) {
        function attendee_save_meta_box_data( $post_id ) {
            // Check nonce
            if ( ! isset( $_POST['attendee_meta_box_nonce'] ) ||
                ! wp_verify_nonce( $_POST['attendee_meta_box_nonce'], 'attendee_save_meta_box' ) ) {
                return;
            }

            // Don't autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Verify user capability
            if ( isset( $_POST['post_type'] ) && 'attendee' === $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }

            // Save each field
            if ( isset( $_POST['attendee_name'] ) ) {
                update_post_meta( $post_id, 'attendee_name', sanitize_text_field( $_POST['attendee_name'] ) );
            }

            if ( isset( $_POST['attendee_email'] ) ) {
                update_post_meta( $post_id, 'attendee_email', sanitize_email( $_POST['attendee_email'] ) );
            }

            if ( isset( $_POST['event_id'] ) ) {
                update_post_meta( $post_id, 'event_id', sanitize_text_field( $_POST['event_id'] ) );
            }
        }
        add_action( 'save_post', 'attendee_save_meta_box_data' );
    }

    if ( ! function_exists( 'get_attendee_data' ) ) {
        /**
         * Retrieve attendee data from the database.
         *
         * @return array List of attendees with their details.
         */
        function get_attendee_data() {
            $args = [
                'post_type'      => 'attendee',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ];

            $attendees = get_posts($args);
            $data = [];

            foreach ($attendees as $attendee) {
                $data[] = [
                    'id'              => $attendee->ID,
                    'submission_date' => $attendee->post_date,
                    'name'            => get_post_meta($attendee->ID, 'attendee_name', true),
                    'email'           => get_post_meta($attendee->ID, 'attendee_email', true),
                    'time_slot'       => get_post_meta($attendee->ID, 'attendee_time_slot', true),
                    'persons'         => get_post_meta($attendee->ID, 'attendee_persons', true),
                    'privacy_policy'  => get_post_meta($attendee->ID, 'attendee_privacy_policy', true),
                ];
            }

            return $data;
        }
    }

    if ( ! function_exists( 'export_attendees_to_csv' ) ) {
        /**
         * Export attendees as a CSV file.
         *
         * This function outputs a CSV with all attendee data
         * and forces the browser to download it.
         *
         * @return void
         */
        function export_attendees_to_csv() {
            $attendees = get_attendee_data();
            $filename  = "attendees_" . date('Ymd') . ".csv";

            // Headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $fp = fopen('php://output', 'w');

            // Column headers
            fputcsv($fp, [
                'Registration ID',
                'Submission Date',
                'Name',
                'Email',
                'Privacy policy'
            ]);

            // Rows
            foreach ($attendees as $attendee) {
                fputcsv($fp, [
                    $attendee['id'],
                    $attendee['submission_date'],
                    $attendee['name'],
                    $attendee['email'],
                    $attendee['privacy_policy'],
                ]);
            }

            fclose($fp);
            exit;
        }
        add_action('admin_post_export_attendees', 'export_attendees_to_csv');
    }

    if ( ! function_exists( 'add_export_button_after_attendee_filters' ) ) {
        /**
         * Add "Export Attendees to CSV" button after the filter/search form
         * on the attendee list page.
         *
         * Hooked into 'manage_posts_extra_tablenav'.
         *
         * @param string $which The location of the extra table nav ('top' or 'bottom').
         *
         * @return void
         */
        function add_export_button_after_attendee_filters( $which ) {
            $screen = get_current_screen();

            if ( $screen && $screen->post_type === 'attendee' && $which === 'top' ) {
                // Only show the button if there are attendee posts
                $attendee_count = wp_count_posts('attendee')->publish;

                if ( $attendee_count > 0 ) {
                    $export_url = add_query_arg(
                        [ 'action' => 'export_attendees' ],
                        admin_url('admin-post.php')
                    );

                    echo '<a href="' . esc_url($export_url) . '" class="button button-primary">' 
                        . __('Résztvevők exportálása', 'text-domain') 
                        . '</a>';
                }
            }
        }
        add_action( 'manage_posts_extra_tablenav', 'add_export_button_after_attendee_filters' );
    }