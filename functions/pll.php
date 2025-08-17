<?php
    if ( ! function_exists( 'get_pll_languages' ) ) {
        /**
         * Get language switcher data from Polylang.
         *
         * Returns an array of available languages with their slugs, names, URLs,
         * current status, and optional flag URLs.
         *
         * @return array Array of languages.
         */
        function get_pll_languages() {
            // Check if Polylang function exists
            if ( ! function_exists( 'pll_the_languages' ) ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log('Polylang function pll_the_languages() not found.');
                }
                return array();
            }

            $langs_array = pll_the_languages( array( 'raw' => 1 ) );

            // Validate data
            if ( ! is_array( $langs_array ) || empty( $langs_array ) ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log('pll_the_languages() returned an invalid or empty array.');
                }
                return array();
            }

            $languages = array();

            foreach ( $langs_array as $lang ) {
                if (
                    ! isset( $lang['slug'], $lang['name'], $lang['url'] )
                ) {
                    continue; // skip incomplete data
                }

                $languages[] = array(
                    'slug'         => $lang['slug'],
                    'locale'       => $lang['locale'],
                    'name'         => $lang['name'],
                    'url'          => $lang['url'],
                    'flag_url'     => isset( $lang['flag'] ) ? $lang['flag'] : null,
                    'current_lang' => ! empty( $lang['current_lang'] ),
                );
            }

            return $languages;
        }
    }

    if ( ! function_exists( 'append_pll_switcher_to_menu' ) ) {
        /**
         * Appends a Polylang language switcher (with flags) to a specific WordPress menu.
         *
         * @param string   $items Menu items HTML.
         * @param stdClass $args  Menu arguments object.
         * @return string Modified menu items HTML with language switcher appended.
         */
        function append_pll_switcher_to_menu( $items, $args ) {
            // Only target a specific menu location (e.g., 'primary_menu').
            if ( isset( $args->theme_location ) && $args->theme_location === 'primary_menu' ) {
                
                // Check if language data function is available.
                if ( ! function_exists( 'get_pll_languages' ) ) {
                    return $items;
                }

                $languages = get_pll_languages();

                if ( empty( $languages ) ) {
                    return $items;
                }

                // Begin language switcher menu item.
                $output = '<li class="menu-item menu-item-lang nav__item level0">';

                foreach ( $languages as $lang ) {
                    $classes = 'nav__link' . ( $lang['current_lang'] ? ' nav__link--active' : '' );
                    $output .= '<a href="' . esc_url( $lang['url'] ) . '" class="' . esc_attr( $classes ) . '">';

                    /*
                    // Add flag image if available.
                    if ( ! empty( $lang['flag_url'] ) ) {
                        $output .= '<img src="' . esc_url( $lang['flag_url'] ) . '" alt="' . esc_attr( $lang['name'] ) . '" class="nav__image" />';
                    }
                    */

                    // Add flag SVG if available.
                    if ( ! empty( $lang['slug'] ) ) {
                        $output .= '<svg class="icon icon-' . $lang['slug'] . '"><use xlink:href="#icon-' . $lang['slug'] . '"></use></svg>';
                    }

                    $output .= '<span class="visually-hidden">' . esc_html( $lang['name'] ) . '</span>';
                    $output .= '</a>';
                }

                $output .= '</li>';

                // Append language switcher to menu items.
                $items .= $output;
            }

            return $items;
        }

        //add_filter( 'wp_nav_menu_items', 'append_pll_switcher_to_menu', 10, 2 );
    }

    if ( ! function_exists( 'pll_language_selector' ) ) {
        /**
         * Outputs the Polylang language selector HTML using a template partial.
         *
         * @return string Language selector HTML markup.
         */
        function pll_language_selector() {
            // Ensure the helper function exists
            if ( ! function_exists( 'get_pll_languages' ) ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log('Function get_pll_languages() not found.');
                }
                return '';
            }

            $languages = get_pll_languages();

            if ( empty( $languages ) || ! is_array( $languages ) ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log('No languages available for the language selector.');
                }
                return '';
            }

            // Load template part and return output
            ob_start();
            set_query_var( 'languages', $languages );
            get_template_part( 'functions/partials/language-switcher' );
            return ob_get_clean();
        }
    }

    // Safely call the function
    /*
    if ( function_exists( 'pll_language_selector' ) ) {
        echo pll_language_selector();
    }
    */
