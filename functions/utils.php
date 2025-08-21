<?php
    if ( ! function_exists( 'get_template_id' ) ) {
        /**
         * Retrieves the ID of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return int|null ID of the page if found, null otherwise.
         */
        function get_template_id( $template_name ) {
            $page = get_pages(
                array(
                    'hierarchical' => false,
                    'meta_key'     => '_wp_page_template',
                    'meta_value'   => $template_name,
                )
            );
            if ( $page ) {
                $page_id = $page[0]->ID;
                return $page_id;
            }
            return null;
        }
    }

    if ( ! function_exists( 'get_template_url' ) ) {
        /**
         * Retrieves the permalink of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return string|null Permalink of the page if found, null otherwise.
         */
        function get_template_url( $template_name ) {
            $page = get_pages(
                array(
                    'hierarchical' => false,
                    'meta_key'     => '_wp_page_template',
                    'meta_value'   => $template_name,
                )
            );
            if ( $page ) {
                $page_id = $page[0]->ID;
                return get_permalink( $page_id );
            }
            return null;
        }
    }

    if ( ! function_exists( 'get_template_name' ) ) {
        /**
         * Get the human-readable template name for a given post ID.
         *
         * @param int $post_id The ID of the post.
         * @return string The template name or 'Default template' if not custom.
         */
        function get_template_name( $post_id ) {
            if (!get_post($post_id)) {
                return '';
            }

            $template = get_post_meta($post_id, '_wp_page_template', true);

            if ($template === 'default' || empty($template)) {
                return __('Default template');
            }

            $template_path = locate_template($template);

            if (file_exists($template_path)) {
                $template_data = get_file_data($template_path, array('name' => 'Template Name'));
                return !empty($template_data['name']) ? $template_data['name'] : basename($template);
            }

            return basename($template); // fallback if file not found
        }
    }

    if ( ! function_exists( 'load_template_part' ) ) {
        /**
         * Loads a template part into a variable instead of displaying it.
         *
         * @param string $template_name Template slug.
         * @param string|null $part_name Optional. Template part name.
         * @return string Template part contents.
         */
        function load_template_part( $template_name, $part_name = null ) {
            ob_start();
            get_template_part( $template_name, $part_name );
            $var = ob_get_contents();
            ob_end_clean();
            return $var;
        }
    }

    if ( ! function_exists( 'wp_safe_format_date' ) ) {
        /**
         * Safely format a date string into WordPress date format.
         *
         * @param mixed  $date_str     The input date string.
         * @param string $input_format The format of the input date string. Default is 'd/m/Y'.
         * @param string $fallback     Optional fallback string if date is invalid. Default is 'Invalid date.'.
         * @return string Formatted date or fallback message.
         */
        function wp_safe_format_date( $date_str, $input_format = 'd/m/Y', $fallback = 'Invalid date.' ) {
            // Check if input is empty or not a string
            if ( empty($date_str) || !is_string($date_str) ) {
                return $fallback;
            }

            // Try to create a DateTime object from the input string
            try {
                $date = DateTime::createFromFormat( $input_format, $date_str );

                // Check for parsing errors
                $errors = DateTime::getLastErrors();
                if ( $date === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) {
                    return $fallback;
                }

                // Format date according to WordPress settings
                return date_i18n( get_option('date_format'), $date->getTimestamp() );

            } catch ( Exception $e ) {
                // Catch any unexpected exceptions
                return $fallback;
            }
        }
    }

    if ( ! function_exists( 'is_external_url' ) ) {
        /**
         * Check if a given URL is external.
         *
         * @param string      $url      The URL to check.
         * @param string|null $site_url Optional. The base site URL. Defaults to get_home_url().
         * @return bool True if the URL is external, false otherwise.
         */
        function is_external_url( $url, $site_url = null ) {
            if ( ! $site_url ) {
                $site_url = get_home_url();
            }

            $url = trim( $url );
            $site_url = rtrim( trim( $site_url ), '/' );

            // Ensure the URL is absolute
            if ( ! $url || strpos( $url, 'http' ) !== 0 ) {
                return false;
            }

            $url_host      = parse_url( $url, PHP_URL_HOST );
            $site_url_host = parse_url( $site_url, PHP_URL_HOST );

            return $url_host && $site_url_host && strcasecmp( $url_host, $site_url_host ) !== 0;
        }
    }

    if ( ! function_exists( 'get_youtube_video_id' ) ) {
        /**
         * Extracts the YouTube video ID from a string containing a YouTube URL or iframe.
         *
         * Supports the following URL formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $input A YouTube iframe HTML string or URL.
         * @return string|false The extracted YouTube video ID, or false if not found.
         */
        function get_youtube_video_id( $input ) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];
    
            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $input, $matches ) ) {
                    return $matches[1] ?? '';
                }
            }
    
            return false;
        }
    }

    if ( ! function_exists( 'normalize_youtube_url' ) ) {
        /**
         * Normalizes any valid YouTube URL (embed, shortlink, watch, etc.)
         * into a standard YouTube watch URL.
         *
         * Supported formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $url The YouTube URL in any supported format.
         * @return string|false The normalized watch URL, or false if no video ID found.
         */
        function normalize_youtube_url($url) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&\?\/]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\&\?\/]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\&\?\/]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    return 'https://www.youtube.com/watch?v=' . $matches[1] ?? '';
                }
            }

            return false; // No match found
        }
    }

    if ( ! function_exists( 'get_youtube_thumbnail_url' ) ) {
        /**
         * Extract YouTube video ID from various URL formats and return a thumbnail URL.
         *
         * @param string $url     The YouTube video URL (can be embed, share, watch, etc.)
         * @param string $quality The desired thumbnail quality: default, mqdefault, hqdefault, sddefault, maxresdefault, 0, 1, 2, 3
         *
         * @return string|null    The full URL to the thumbnail image, or null if ID not found.
         */
        function get_youtube_thumbnail_url($url, $quality = 'maxresdefault') {
            // Define acceptable quality levels
            $valid_qualities = ['default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault', '0', '1', '2', '3'];
            $quality = in_array($quality, $valid_qualities) ? $quality : 'maxresdefault';

            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $url, $matches ) ) {
                    $video_id = $matches[1];
                    return "//img.youtube.com/vi/{$video_id}/{$quality}.jpg";
                }
            }

            return null;
        }
    }

    if ( ! function_exists( 'get_map_link' ) ) {
        /**
         * Generates a map link based on the user's device:
         * - Google Maps for desktop
         * - Waze for mobile devices
         *
         * @param string $address The address to be mapped.
         * @return string HTML anchor tag with the appropriate map link.
         */
        function get_map_link($address) {
            $encodedAddress = urlencode($address);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Basic mobile detection via User-Agent
            $isMobile = preg_match('/(android|iphone|ipad|ipod|mobile)/i', $userAgent);

            if ($isMobile) {
                // Waze for mobile users
                $wazeLink = "https://waze.com/ul?q={$encodedAddress}";
                return "<a href=\"{$wazeLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$address}</a>";
            } else {
                // Google Maps for desktop users
                $googleMapsLink = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
                return "<a href=\"{$googleMapsLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$address}</a>";
            }
        }
    }

    if ( ! function_exists( 'get_route_link' ) ) {
        /**
         * Generates a route link using the user's current location as the start.
         * - Google Maps for desktop
         * - Waze for mobile
         *
         * @param string $destination The destination address.
         * @return string HTML anchor tag with the route link.
         */
        function get_route_link($destination) {
            $encodedDest = urlencode($destination);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $isMobile = preg_match('/(android|iphone|ipad|ipod|mobile)/i', $userAgent);

            if ($isMobile) {
                // Waze route from current location to destination
                $wazeLink = "https://waze.com/ul?q={$encodedDest}&navigate=yes";
                return "<a href=\"{$wazeLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$destination}</a>";
            } else {
                // Google Maps route from current location to destination
                $googleLink = "https://www.google.com/maps/dir/?api=1&destination={$encodedDest}&travelmode=driving";
                return "<a href=\"{$googleLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$destination}</a>";
            }
        }
    }
    
    if ( ! function_exists( 'format_file_size' ) ) {
        /**
         * Format bytes into a human-readable file size string.
         *
         * @param int $bytes    The file size in bytes.
         * @param int $decimals The number of decimal places to include (default is 0).
         * @return string       The formatted file size string (e.g., "2 MB").
         */
        function format_file_size($bytes, $decimals = 0) {
            $size = ['B','KB','MB','GB','TB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
        }
    }

    if ( ! function_exists( 'format_file_type' ) ) {
        /**
         * Format a MIME subtype into a common file extension.
         *
         * @param string $subtype The MIME subtype (e.g., 'vnd.ms-excel').
         * @return string         The corresponding file extension in uppercase (e.g., 'XLS').
         */
        function format_file_type($subtype) {
            $map = [
                // Excel
                'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'XLSX',
                'vnd.ms-excel' => 'XLS',
                'vnd.ms-excel.sheet.macroenabled.12' => 'XLSM',
                'vnd.ms-excel.sheet.binary.macroenabled.12' => 'XLSB',

                // Word
                'msword' => 'DOC',
                'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                'vnd.ms-word.document.macroenabled.12' => 'DOCM',

                // PowerPoint
                'vnd.ms-powerpoint' => 'PPT',
                'vnd.openxmlformats-officedocument.presentationml.presentation' => 'PPTX',
                'vnd.ms-powerpoint.presentation.macroenabled.12' => 'PPTM',

                // OneNote
                'onenote' => 'ONE',

                // Visio
                'vnd.visio' => 'VSD',
                'vnd.ms-visio.drawing' => 'VSDX',

                // Other common formats
                'pdf' => 'PDF',
                'jpeg' => 'JPG',
                'png' => 'PNG',
                'zip' => 'ZIP',
                'plain' => 'TXT',
                'csv' => 'CSV',
            ];

            if (isset($map[$subtype])) {
                return $map[$subtype];
            }

            // Fallback: extract last part after a dot or slash and uppercase it
            if (strpos($subtype, '.') !== false) {
                $parts = explode('.', $subtype);
                return strtoupper(end($parts));
            }

            return strtoupper($subtype);
        }
    }

    if ( ! function_exists( 'hu_article_helper' ) ) {
        /**
         * Determines the correct Hungarian article ("a" or "az") for a given word or phrase.
         *
         * Rules:
         * - "az" precedes vowels (including accented)
         * - "a" precedes consonants
         * - Handles leading/trailing spaces, punctuation, and UTF-8 characters
         *
         * @param string $phrase The word or phrase to analyze.
         * @return string "a" or "az"
         */
        function hu_article_helper(string $phrase): string {
            // Normalize and sanitize input
            $phrase = mb_strtolower(trim($phrase), 'UTF-8');

            if ($phrase === '') {
                return 'a';
            }

            // Remove leading non-letters (punctuation, dashes, quotes, etc.)
            $phrase = preg_replace('/^[^a-záéíóöőúüű]+/iu', '', $phrase);

            if ($phrase === '') {
                return 'a';
            }

            // Extract first meaningful word
            $firstWord = preg_split('/\s+/u', $phrase)[0] ?? '';

            if ($firstWord === '') {
                return 'a';
            }

            // Hungarian vowels (including accented forms)
            static $vowels = ['a', 'á', 'e', 'é', 'i', 'í', 'o', 'ó', 'ö', 'ő', 'u', 'ú', 'ü', 'ű'];

            // Get first letter
            $firstLetter = mb_substr($firstWord, 0, 1, 'UTF-8');

            return in_array($firstLetter, $vowels, true) ? 'az' : 'a';
        }
    }

    if ( ! function_exists( 'hu_article_word_helper' ) ) {
        /**
         * Returns the Hungarian article ("a" or "az") combined with the given word
         * only if the current site locale is Hungarian. Otherwise returns the word as-is.
         *
         * Example: alma -> "az alma" (if Hungarian locale)
         *          kert -> "a kert" (if Hungarian locale)
         *          apple -> "apple" (if not Hungarian locale)
         *
         * @param string $word Word or phrase to prepend with correct article if Hungarian.
         * @return string
         */
        function hu_article_word_helper(string $word): string {
            // Check if locale is Hungarian
            if (function_exists('get_locale') && strpos(get_locale(), 'hu') === 0) {
                return hu_article_helper($word) . ' ' . mb_strtolower(trim($word), 'UTF-8');
            }

            return $word;
        }
    }