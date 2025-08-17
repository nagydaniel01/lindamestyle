<?php
    if (!function_exists('move_comment_field')) {
        /**
         * Moves the comment textarea to the bottom of the comment form fields.
         *
         * This function reorders the fields in the WordPress comment form
         * so that the comment textarea appears after other fields like
         * name, email, and website.
         *
         * @param array $fields An array of comment form fields.
         * @return array Modified array of comment form fields with comment at the end.
         */
        function move_comment_field($fields) {
            if ( isset( $fields['comment'] ) ) {
                $comment_field = $fields['comment'];
                unset( $fields['comment'] );
                $fields['comment'] = $comment_field;
            }
            return $fields;
        }
        add_filter('comment_form_fields', 'move_comment_field');
    }
