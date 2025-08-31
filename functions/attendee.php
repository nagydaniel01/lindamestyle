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
