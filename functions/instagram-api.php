<?php
    // Configuration
    $client_id          = '385619541229452';
    $client_secret      = '5fcaae96869f439cbd7f95485a77f29b';
    $redirect_uri       = 'https://lindamestyle.test/instagram/';
    $theme_directory    = get_template_directory(); // Get the path to the theme directory
    $token_file         = $theme_directory . '/instagram_access_token.json'; // Path to the token file

    /**
     * Exchange a short-lived access token for a long-lived access token.
     *
     * @param string $short_lived_token The short-lived access token.
     * @param string $client_secret The Instagram client secret.
     * @return array An array containing the long-lived access token and its expiry time.
     */
    if (!function_exists('instagram_get_long_lived_token')) {
        function instagram_get_long_lived_token($short_lived_token, $client_secret) {
            $response = wp_remote_get(
                add_query_arg(
                    array(
                        'grant_type' => 'ig_exchange_token',
                        'client_secret' => $client_secret,
                        'access_token' => $short_lived_token,
                    ),
                    'https://graph.instagram.com/access_token'
                )
            );

            if (is_wp_error($response)) {
                wp_die('Error fetching long-lived access token: ' . esc_html($response->get_error_message()));
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if (200 === $response_code) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($response_body['access_token'])) {
                    return array(
                        'access_token' => $response_body['access_token'],
                        'expires_in' => time() + (int) $response_body['expires_in'] // Store the expiry time
                    );
                } else {
                    wp_die('Error: Long-lived access token not received.');
                }
            } else {
                wp_die('Error fetching long-lived access token. HTTP response code: ' . esc_html($response_code));
            }
        }
    }

    /**
     * Refresh the long-lived access token.
     *
     * @param string $access_token The current long-lived access token.
     * @return array An array containing the refreshed access token and its expiry time.
     */
    if (!function_exists('instagram_refresh_long_lived_token')) {
        function instagram_refresh_long_lived_token($access_token) {
            $response = wp_remote_get(
                add_query_arg(
                    array(
                        'grant_type' => 'ig_refresh_token',
                        'access_token' => $access_token,
                    ),
                    'https://graph.instagram.com/refresh_access_token'
                )
            );

            if (is_wp_error($response)) {
                wp_die('Error refreshing long-lived access token: ' . esc_html($response->get_error_message()));
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if (200 === $response_code) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($response_body['access_token'])) {
                    return array(
                        'access_token' => $response_body['access_token'],
                        'expires_in' => time() + (int) $response_body['expires_in']
                    );
                } else {
                    wp_die('Error: Refreshed access token not received.');
                }
            } else {
                wp_die('Error refreshing long-lived access token. HTTP response code: ' . esc_html($response_code));
            }
        }
    }

    /**
     * Retrieve or refresh the Instagram access token.
     *
     * @param string $client_id The Instagram client ID.
     * @param string $client_secret The Instagram client secret.
     * @param string $redirect_uri The redirect URI for Instagram OAuth.
     * @param string $token_file Path to the file where the access token is stored.
     * @return string The access token.
     */
    if (!function_exists('instagram_get_access_token')) {
        function instagram_get_access_token($client_id, $client_secret, $redirect_uri, $token_file) {
            // Check if the token file exists and has a valid token
            if (file_exists($token_file)) {
                $token_data = json_decode(file_get_contents($token_file), true);
                if (isset($token_data['access_token']) && !empty($token_data['access_token'])) {
                    // Check if the token is close to expiry (e.g., within 5 days)
                    if (time() > ($token_data['expires_in'] - 5 * 24 * 60 * 60)) {
                        $new_token_data = instagram_refresh_long_lived_token($token_data['access_token']);
                        file_put_contents($token_file, json_encode($new_token_data, JSON_PRETTY_PRINT));
                        return $new_token_data['access_token'];
                    }
                    return $token_data['access_token'];
                }
            }

            // If no token is available or it's invalid, start the authorization process
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $response = wp_remote_post(
                    'https://api.instagram.com/oauth/access_token',
                    array(
                        'body' => array(
                            'client_id' => $client_id,
                            'client_secret' => $client_secret,
                            'grant_type' => 'authorization_code',
                            'redirect_uri' => $redirect_uri,
                            'code' => sanitize_text_field($_GET['code']),
                        ),
                    )
                );

                if (is_wp_error($response)) {
                    wp_die('Error fetching access token: ' . esc_html($response->get_error_message()));
                }

                $response_code = wp_remote_retrieve_response_code($response);
                if (200 === $response_code) {
                    $response_body = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($response_body['access_token'])) {
                        $short_lived_access_token = $response_body['access_token'];
                        
                        // Get the long-lived access token using the short-lived one
                        $long_lived_token_data = instagram_get_long_lived_token($short_lived_access_token, $client_secret);
                        
                        // Save the new long-lived token in JSON format
                        file_put_contents($token_file, json_encode($long_lived_token_data, JSON_PRETTY_PRINT));
                        
                        return $long_lived_token_data['access_token'];
                    } else {
                        wp_die('Error: Access token not received.');
                    }
                } else {
                    wp_die('Error fetching access token. HTTP response code: ' . esc_html($response_code));
                }
            }
            
            // Redirect to Instagram authorization if no code is available
            $redirect = add_query_arg(
                array(
                    'client_id' => $client_id,
                    'redirect_uri' => $redirect_uri,
                    'scope' => 'user_profile,user_media',
                    'response_type' => 'code',
                ),
                'https://api.instagram.com/oauth/authorize'
            );
            
            echo '<a href="' . esc_url($redirect) . '" class="btn btn-primary">Show Instagram Photos</a>';
            exit;
        }
    }

    /**
     * Fetch all media from the Instagram account.
     *
     * @param string $client_id The Instagram client ID.
     * @param string $client_secret The Instagram client secret.
     * @param string $redirect_uri The redirect URI for Instagram OAuth.
     * @param string $token_file Path to the file where the access token is stored.
     * @return array The array of user data.
     */
    if (!function_exists('instagram_get_user_data')) {
        function instagram_get_user_data($client_id, $client_secret, $redirect_uri, $token_file) {
            // Retrieve access token
            $access_token = instagram_get_access_token($client_id, $client_secret, $redirect_uri, $token_file);

            // Instagram API endpoint to fetch user data
            $user_url = add_query_arg(
                array(
                    'fields' => 'id,username,account_type,media_count',
                    'access_token' => $access_token,
                ),
                'https://graph.instagram.com/me'
            );

            // Make the API request
            $response = wp_remote_get($user_url);

            if (is_wp_error($response)) {
                wp_die('Error fetching user data: ' . esc_html($response->get_error_message()));
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if (200 === $response_code) {
                $user_data = json_decode(wp_remote_retrieve_body($response), true);
            } else {
                wp_die('Failed to retrieve user data. HTTP response code: ' . esc_html($response_code));
            }

            return $user_data;
        }
    }

    /**
     * Fetch all media from the Instagram account.
     *
     * @param string $client_id The Instagram client ID.
     * @param string $client_secret The Instagram client secret.
     * @param string $redirect_uri The redirect URI for Instagram OAuth.
     * @param string $token_file Path to the file where the access token is stored.
     * @return array The array of all media items.
     */
    if (!function_exists('instagram_get_all_media')) {
        function instagram_get_all_media($client_id, $client_secret, $redirect_uri, $token_file) {
            // Retrieve access token
            $access_token = instagram_get_access_token($client_id, $client_secret, $redirect_uri, $token_file);

            // Initialize an array to hold all media items
            $all_media = array();

            // Fetch all media from Instagram with pagination
            $media_url = add_query_arg(
                array(
                    'fields' => 'id,media_type,media_url,thumbnail_url,permalink,caption,timestamp,username,children',
                    'access_token' => $access_token,
                ),
                'https://graph.instagram.com/me/media'
            );

            do {
                $response = wp_remote_get($media_url);

                if (is_wp_error($response)) {
                    wp_die('Error fetching media: ' . esc_html($response->get_error_message()));
                }

                $response_code = wp_remote_retrieve_response_code($response);
                if (200 === $response_code) {
                    $response_body = json_decode(wp_remote_retrieve_body($response), true);

                    if (!empty($response_body['data'])) {
                        $all_media = array_merge($all_media, $response_body['data']);
                    }

                    // Check if there is a next page URL
                    if (!empty($response_body['paging']['next'])) {
                        $media_url = $response_body['paging']['next'];
                    } else {
                        $media_url = null; // No more pages
                    }
                } else {
                    wp_die('Failed to retrieve media. HTTP response code: ' . esc_html($response_code));
                }

            } while ($media_url);

            return $all_media;
        }
    }
