<?php
if ( ! function_exists( 'mytheme_single_template_by_post_format' ) ) {
    /**
     * Load a custom single template based on the post format.
     *
     * @param string $single The path to the single template.
     * @return string Path to the template file to use.
     */
    function mytheme_single_template_by_post_format( $single ) {
        global $post;

        // Ensure we're on a singular page and $post is a WP_Post object
        if ( ! is_singular() || ! $post instanceof WP_Post ) {
            return $single;
        }

        $format = get_post_format( $post );

        if ( $format ) {
            // Locate the template file for the post format
            $template = locate_template( 'single-' . $format . '.php' );
            if ( $template ) {
                return $template;
            }
        }

        return $single;
    }
    add_filter( 'single_template', 'mytheme_single_template_by_post_format' );
}
