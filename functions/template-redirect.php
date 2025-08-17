<?php
    if ( ! function_exists('custom_base_archive_redirect') ) {
        /**
         * Intercepts requests to /tag/ and /category/ base URLs
         * and serves custom archive templates instead of 404.
         *
         * @return void
         */
        function custom_base_archive_redirect() {
            $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

            // Get current taxonomy base slugs
            $category_base = get_option('category_base') ?: 'category';
            $tag_base = get_option('tag_base') ?: 'tag';

            // Taxonomy base slugs mapping
            $tax_mappings = [ 
                'event_cat'       => 'esemeny-kategoria', 
                'podcast_cat'     => 'podcast-kategoria', 
                'video_cat'       => 'video-kategoria', 
                'publication_cat' => 'kiadvany-kategoria',
                'art'             => 'muveszeti-agak'
            ];

            // Check for category base and load the custom category template
            if ($request_uri === $category_base) {
                $category_template = get_template_directory() . '/templates/listings/category-list.php';
                if (file_exists($category_template)) {
                    include $category_template;
                    exit;
                }
            }

            // Check for tag base and load the custom tag template
            if ($request_uri === $tag_base) {
                $tag_template = get_template_directory() . '/templates/listings/tag-list.php';
                if (file_exists($tag_template)) {
                    include $tag_template;
                    exit;
                }
            }

            // Loop through tax_mappings and check for matching base
            foreach ($tax_mappings as $taxonomy => $base_slug) {
                // Check if taxonomy exists
                if (taxonomy_exists($taxonomy)) {
                    if ($request_uri === $base_slug) {
                        // Construct the path to the custom taxonomy template
                        $taxonomy_template = get_template_directory() . "/templates/listings/{$taxonomy}-list.php";

                        // Check if the custom taxonomy template exists, otherwise fallback
                        if (file_exists($taxonomy_template)) {
                            // Pass the taxonomy name as a query variable for use in the template
                            set_query_var('taxonomy', $taxonomy);

                            include $taxonomy_template;
                            exit;
                        } else {
                            // Fallback to the generic taxonomy archive template
                            $taxonomy_template_fallback = get_template_directory() . "/templates/listings/taxonomy-list.php";
                            if (file_exists($taxonomy_template_fallback)) {
                                // Pass the taxonomy name as a query variable for use in the fallback template
                                set_query_var('taxonomy', $taxonomy);

                                include $taxonomy_template_fallback;
                                exit;
                            }
                        }
                    }
                }
            }
        }
        //add_action('template_redirect', 'custom_base_archive_redirect');
    }