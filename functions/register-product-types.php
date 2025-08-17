<?php
    /**
     * Registers the 'event' product type.
     *
     * This function creates a custom product type for WooCommerce, called 'event',
     * with custom behaviors for purchasing, adding to the cart, and displaying event-specific data.
     */
    if ( ! function_exists( 'register_event_product_type' ) ) {
        function register_event_product_type() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }
            
            class WC_Product_Event extends WC_Product {
                public function __construct( $product = 0 ) {
                    $this->supports[] = 'ajax_add_to_cart';
                    parent::__construct( $product );
                }

                /**
                 * Returns the product type.
                 *
                 * @return string The type of product ('event').
                 */
                public function get_type() {
                    return 'event';
                }

                /**
                 * Checks if the product is purchasable.
                 *
                 * @return bool True if purchasable.
                 */
                public function is_purchasable() {
                    return true;
                }

                /**
                 * Checks if the product is virtual.
                 *
                 * @return bool True if virtual (non-shippable).
                 */
                public function is_virtual() {
                    return true;
                }

                /**
                 * Checks if the product is downloadable.
                 *
                 * @return bool False since this is not a downloadable product.
                 */
                public function is_downloadable() {
                    return false;
                }

                /**
                 * Returns the add to cart URL.
                 *
                 * @return string The URL to add the product to the cart.
                 */
                public function add_to_cart_url() {
                    $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg(
                        'added-to-cart',
                        add_query_arg(
                            array(
                                'add-to-cart' => $this->get_id(),
                            ),
                            ( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $this->get_permalink() : ''
                        )
                    ) : $this->get_permalink();
                    return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
                }

                /**
                 * Returns the add to cart button text.
                 *
                 * @return string The text displayed on the button (e.g., "Buy ticket").
                 */
                public function add_to_cart_text() {
                    $text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Buy ticket', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
            
                    return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
                }
            }
        }

        add_action('init', 'register_event_product_type');
    }

    /**
     * Modifies the product class based on its type.
     *
     * @param string $classname The existing product class.
     * @param string $product_type The type of product (e.g., 'event').
     * @return string The modified product class name.
     */
    if ( ! function_exists( 'woocommerce_event_product_class' ) ) {
        function woocommerce_event_product_class( $classname, $product_type ) {
            if ( $product_type == 'event' ) {
                $classname = 'WC_Product_Event';
            }
            return $classname;
        }

        add_filter('woocommerce_product_class', 'woocommerce_event_product_class', 10, 2);
    }

    /**
     * Adds 'event' to the WooCommerce product type selector.
     *
     * @param array $product_types The existing product types.
     * @return array The modified product types with 'event' added.
     */
    if ( ! function_exists( 'add_event_type' ) ) {
        function add_event_type($product_types) {
            $product_types['event'] = 'Event';
            return $product_types;
        }

        add_filter('product_type_selector', 'add_event_type');
    }

    /**
     * Adds custom JavaScript to the admin panel for event product fields.
     * This function shows and hides certain options in the admin interface based on the 'event' product type.
     */
    if ( ! function_exists( 'event_admin_custom_js' ) ) {
        function event_admin_custom_js() {
            if (get_post_type() !== 'product') {
                return;
            }
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    // Show options for the 'event' product type in the Price tab
                    $('.general_options').addClass('show_if_event').show();
                    $('#general_product_data .pricing').addClass('show_if_event').show();

                    // Show options for the 'event' product type in the Inventory tab
                    $('.inventory_options').addClass('show_if_event').show();
                    $('#inventory_product_data ._manage_stock_field').addClass('show_if_event').show();
                    $('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_event').show();
                    $('#inventory_product_data ._sold_individually_field').addClass('show_if_event').show();
                });
            </script>
            <?php
        }

        add_action('admin_footer', 'event_admin_custom_js');
    }

    /**
     * Adds a custom tab for event details to the product edit page.
     *
     * @param array $tabs The existing product tabs.
     * @return array The modified tabs with 'event_options' added.
     */
    if ( ! function_exists( 'event_product_custom_tab' ) ) {
        function event_product_custom_tab($tabs) {
            // Add a new tab
            $tabs['event_options'] = array(
                'label'    => __('Event Details', 'lindame'),
                'target'   => 'event_product_options',
                'class'    => array('show_if_event'),
                'priority' => 21,
            );
            return $tabs;
        }

        add_filter('woocommerce_product_data_tabs', 'event_product_custom_tab');
    }

    /**
     * Adds the content for the 'Event Details' tab in the product edit page.
     * This includes fields for event date, time, location, platform, and other details.
     */
    if ( ! function_exists( 'event_product_custom_tab_content' ) ) {
        function event_product_custom_tab_content() {
            ?>
            <div id='event_product_options' class='panel woocommerce_options_panel'>
                <div class='options_group'>
                    <?php
                        // Date field for event date
                        woocommerce_wp_text_input(array(
                            'id'          => '_event_date',
                            'label'       => __('Event Date', 'lindame'),
                            'placeholder' => 'YYYY-MM-DD',
                            'type'        => 'date',
                            'desc_tip'    => false
                        ));

                        // Time field for event time
                        woocommerce_wp_text_input(array(
                            'id'          => '_event_time',
                            'label'       => __('Event Time', 'lindame'),
                            'placeholder' => 'HH:MM',
                            'type'        => 'time',
                            'desc_tip'    => false
                        ));
                        
                        // Radio button group for event location type
                        woocommerce_wp_radio(array(
                            'id'      => '_event_location_type',
                            'label'   => __('Event Location Type', 'lindame'),
                            'options' => array(
                                'in-person'  => __('In-Person', 'lindame'),
                                'online'     => __('Online', 'lindame')
                            )
                        ));
                    ?>
                    
                    <div class="options_group" id="event_address_fields" style="display:none;">
                        <?php
                            woocommerce_wp_text_input(array(
                                'id'          => '_event_address',
                                'label'       => __('Address', 'lindame'),
                                'placeholder' => __('Enter address', 'lindame'),
                                'desc_tip'    => false
                            ));

                            woocommerce_wp_text_input(array(
                                'id'          => '_event_city',
                                'label'       => __('City', 'lindame'),
                                'placeholder' => __('Enter city', 'lindame'),
                                'desc_tip'    => false
                            ));
                            
                            woocommerce_wp_text_input(array(
                                'id'          => '_event_postal_code',
                                'label'       => __('Postal Code', 'lindame'),
                                'placeholder' => __('Enter postal code', 'lindame'),
                                'desc_tip'    => false
                            ));
                            
                            woocommerce_wp_text_input(array(
                                'id'          => '_event_country',
                                'label'       => __('Country / Region', 'lindame'),
                                'placeholder' => __('Enter country', 'lindame'),
                                'desc_tip'    => false
                            ));
                        ?>
                    </div>
                    
                    <div class="options_group" id="event_link_field" style="display:none;">
                        <?php
                            woocommerce_wp_radio(array(
                                'id'      => '_event_platform',
                                'label'   => __('Platform', 'lindame'),
                                'options' => array(
                                    'zoom'      		=> __('Zoom', 'lindame'),
                                    'microsoft_teams' 	=> __('Microsoft Teams', 'lindame'),
                                    'google_meet' 		=> __('Google Meet', 'lindame')
                                )
                            ));

                            woocommerce_wp_text_input(array(
                                'id'          => '_event_link',
                                'label'       => __('Event Link', 'lindame'),
                                'placeholder' => __('Enter event link', 'lindame'),
                                'desc_tip'    => false
                            ));
                        ?>
                    </div>

                    <?php
                        woocommerce_wp_textarea_input(array(
                            'id'          => '_event_details',
                            'label'       => __('Event Details', 'lindame'),
                            'placeholder' => __('Enter additional details', 'lindame'),
                            'desc_tip'    => false
                        ));
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Show the correct fields based on the selected radio button
                    function toggleLocationFields() {
                        var selectedType = $('input[name="_event_location_type"]:checked').val();
                        
                        if (selectedType === 'in-person') {
                            $('#event_address_fields').show();
                            $('#event_link_field').hide();
                        } else if (selectedType === 'online') {
                            $('#event_address_fields').hide();
                            $('#event_link_field').show();
                        } else {
                            $('#event_address_fields').hide();
                            $('#event_link_field').hide();
                        }
                    }

                    // Initialize on document ready
                    toggleLocationFields();

                    // Trigger the function on radio button change
                    $('input[name="_event_location_type"]').change(function() {
                        toggleLocationFields();
                    });
                });
            </script>
            <?php
        }

        add_action('woocommerce_product_data_panels', 'event_product_custom_tab_content');
    }

    /**
     * Saves custom fields for event products when the product is saved.
     * 
     * This function saves event-specific details such as date, time, location, platform,
     * address, and other event-related information as post meta.
     *
     * @param int $post_id The ID of the product being saved.
     */
    if ( ! function_exists( 'save_event_product_custom_fields' ) ) {
        function save_event_product_custom_fields($post_id) {
            $event_date            = isset($_POST['_event_date']) ? sanitize_text_field($_POST['_event_date']) : '';
            $event_time            = isset($_POST['_event_time']) ? sanitize_text_field($_POST['_event_time']) : '';
            $event_location_type   = isset($_POST['_event_location_type']) ? sanitize_text_field($_POST['_event_location_type']) : '';
            $event_address         = isset($_POST['_event_address']) ? sanitize_text_field($_POST['_event_address']) : '';
            $event_city            = isset($_POST['_event_city']) ? sanitize_text_field($_POST['_event_city']) : '';
            $event_postal_code     = isset($_POST['_event_postal_code']) ? sanitize_text_field($_POST['_event_postal_code']) : '';
            $event_country         = isset($_POST['_event_country']) ? sanitize_text_field($_POST['_event_country']) : '';
            $event_platform        = isset($_POST['_event_platform']) ? sanitize_text_field($_POST['_event_platform']) : '';
            $event_link            = isset($_POST['_event_link']) ? esc_url($_POST['_event_link']) : '';
            $event_details         = isset($_POST['_event_details']) ? sanitize_textarea_field($_POST['_event_details']) : '';

            // Update post meta with event data
            update_post_meta($post_id, '_event_date', $event_date);
            update_post_meta($post_id, '_event_time', $event_time);
            update_post_meta($post_id, '_event_location_type', $event_location_type);
            update_post_meta($post_id, '_event_address', $event_address);
            update_post_meta($post_id, '_event_city', $event_city);
            update_post_meta($post_id, '_event_postal_code', $event_postal_code);
            update_post_meta($post_id, '_event_country', $event_country);
            update_post_meta($post_id, '_event_platform', $event_platform);
            update_post_meta($post_id, '_event_link', $event_link);
            update_post_meta($post_id, '_event_details', $event_details);
        }

        add_action('woocommerce_process_product_meta', 'save_event_product_custom_fields');
    }

    /**
     * Triggers the WooCommerce add to cart action for event products.
     * 
     * This function allows for handling event-specific actions when adding an event product to the cart.
     */
    if ( ! function_exists('event_add_to_cart') ) {
        function event_add_to_cart() {
            do_action('woocommerce_simple_add_to_cart');
        }

        add_action('woocommerce_event_add_to_cart', 'event_add_to_cart');
    }

    /**
     * Customizes the add to cart button for event products.
     *
     * This function modifies the default add to cart button, specifically for event products,
     * including custom URL, text, and attributes.
     *
     * @param string $link The HTML for the add to cart button.
     * @param object $product The current product object.
     * @return string The modified add to cart button HTML.
     */
    if ( ! function_exists('custom_event_add_to_cart_button') ) {
        function custom_event_add_to_cart_button($link, $product) {
            if ($product && $product->is_type('event')) {
                $url = esc_url($product->add_to_cart_url());
                $label = esc_html($product->add_to_cart_text());
                $link = sprintf('<a href="%s" data-quantity="%s" class="button %s" %s>%s</a>',
                    $url,
                    esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
                    esc_attr(isset($args['class']) ? $args['class'] : 'button'),
                    isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
                    $label
                );
            }

            return $link;
        }

        // add_filter('woocommerce_loop_add_to_cart_link', 'custom_event_add_to_cart_button', 10, 2);
    }

    /**
     * Adds a custom 'Event Details' tab to the WooCommerce product page for event products.
     *
     * @param array $tabs Existing product tabs.
     * 
     * @return array The modified list of product tabs, including the new 'event_details' tab.
     */
    if ( ! function_exists('custom_product_tabs') ) {
        function custom_product_tabs($tabs) {
            global $product;
            
            // Only add the 'Event Details' tab if the product is of type 'event'
            if ($product && is_object($product) && $product->get_type() === 'event') {
                $product_id = $product->get_id();
                $event_details = get_post_meta($product_id, '_event_date', true);

                // Add the event details tab with a callback function if event details exist
                $tabs['event_details'] = array(
                    'title'    => __('Event details', 'lindame'),
                    'priority' => 5,
                    'callback' => $event_details ? 'event_details_tab' : null
                );
            }
            return $tabs;
        }

        add_filter('woocommerce_product_tabs', 'custom_product_tabs');

        /**
         * The callback function for rendering the event details tab.
         *
         * @param string $slug The slug of the tab.
         * @param array $tab The tab's configuration.
         */
        function event_details_tab($slug, $tab) {
            set_query_var('tab_title', $tab['title']);
            echo get_template_part('woocommerce/single-product/tabs/tab', 'event_details');
        }
    }

    /**
     * Displays event-specific details on the product page, such as event date, time, location, and platform.
     */
    if ( ! function_exists('display_event_product_fields') ) {
        function display_event_product_fields() {
            global $product;

            // Set locale and timezone
            setlocale(LC_TIME, get_locale());
            date_default_timezone_set(get_option('timezone_string'));

            // Only display event details for event products
            if ($product->get_type() == 'event') {
                // Retrieve custom event fields from post meta
                $event_date          = get_post_meta($product->get_id(), '_event_date', true);
                $event_time          = get_post_meta($product->get_id(), '_event_time', true);
                $event_location_type = get_post_meta($product->get_id(), '_event_location_type', true);
                $event_address       = get_post_meta($product->get_id(), '_event_address', true);
                $event_city          = get_post_meta($product->get_id(), '_event_city', true);
                $event_postal_code   = get_post_meta($product->get_id(), '_event_postal_code', true);
                $event_country       = get_post_meta($product->get_id(), '_event_country', true);
                $event_platform      = get_post_meta($product->get_id(), '_event_platform', true);
                $event_link          = get_post_meta($product->get_id(), '_event_link', true);
                $event_details       = get_post_meta($product->get_id(), '_event_details', true);

                // Format date based on locale
                $locale    = get_locale();
                $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                $formatter->setPattern('Y. MMMM d.');

                // Mapping of platform codes to labels
                $platform_options = array(
                    'zoom'           => __('Zoom', 'lindame'),
                    'microsoft_teams'=> __('Microsoft Teams', 'lindame'),
                    'google_meet'    => __('Google Meet', 'lindame')
                );

                // Display platform label
                $platform_label = isset($platform_options[$event_platform]) ? $platform_options[$event_platform] : $event_platform;
                
                echo '<div class="event-product-details">';
                echo '<table class="woocommerce-table event-details-table">';

                // Display event date and time
                if (!empty($event_date)) {
                    $date = new DateTime($event_date);
                    $formatted_date = $formatter->format($date);
                    echo '<tr><th>' . __('Date:', 'lindame') . '</th><td>' . esc_html($formatted_date) . '</td></tr>';
                }

                if (!empty($event_time)) {
                    echo '<tr><th>' . __('Time:', 'lindame') . '</th><td>' . esc_html($event_time) . '</td></tr>';
                }

                // Display event location or platform
                if ($event_location_type == 'in-person') {
                    $address = urlencode($event_address . ', ' . $event_city . ', ' . $event_postal_code . ', ' . $event_country);
                    $google_maps_url = 'https://www.google.com/maps/search/?api=1&query=' . $address;

                    echo '<tr><th>' . __('Location:', 'lindame') . '</th><td>' . esc_html($event_postal_code) . ' ' . esc_html($event_city) . ', ' . esc_html($event_address) . ',<br/>' . esc_html($event_country) . '<br/><a href="' . esc_url($google_maps_url) . '" target="_blank">' . __('View on map', 'lindame') . '</a></td></tr>';
                } elseif ($event_location_type == 'online') {
                    echo '<tr><th>' . __('Platform:', 'lindame') . '</th><td>' . esc_html($platform_label) . '</td></tr>';
                }

                echo '</table>';

                // Display event details (description)
                if (!empty($event_details)) {
                    echo '<p>' . nl2br(esc_html($event_details)) . '</p>';
                }

                echo '</div>';
            }
        }
        // Uncomment if needed: add_action('woocommerce_single_product_summary', 'display_event_product_fields', 25);
    }

    /**
     * Displays the event link on the "Thank You" page after order completion.
     * 
     * This function shows the event access link for event products when the order is completed.
     *
     * @param int $order_id The ID of the order.
     */
    if ( ! function_exists('display_event_link_on_thankyou_page') ) {
        function display_event_link_on_thankyou_page($order_id) {
            $order = wc_get_order($order_id);

            // Show a message if the order is not completed yet
            if ($order->get_status() !== 'completed') {
                echo '<p>' . __('Your order is not yet completed. You will receive the event details once your order is completed.', 'lindame') . '</p>';
                return;
            }

            // Loop through the order items and check for event products
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                
                if ($product->get_type() == 'event') {
                    $event_link = get_post_meta($product->get_id(), '_event_link', true);

                    if (!empty($event_link)) {
                        echo '<h2>' . __('Event details', 'lindame') . '</h2>';
                        echo '<p>' . __('You can access the event here:', 'lindame') . ' <a href="' . esc_url($event_link) . '" target="_blank">' . esc_html($event_link) . '</a></p>';
                    }
                    break;
                }
            }
        }
        // Uncomment if needed: add_action('woocommerce_thankyou', 'display_event_link_on_thankyou_page', 20);
    }

    /**
     * Adds the event link to the order completion email.
     *
     * This function ensures that the event access link is included in the order completion email,
     * either in plain text or HTML format.
     *
     * @param WC_Order $order The order object.
     * @param bool $sent_to_admin Whether the email is being sent to the admin.
     * @param bool $plain_text Whether the email is in plain text format.
     */
    if ( ! function_exists('add_event_link_to_order_completed_email') ) {
        function add_event_link_to_order_completed_email($order, $sent_to_admin, $plain_text) {
            // Only process if the order is completed
            if ($order->get_status() !== 'completed') {
                return;
            }
            
            // Loop through the order items and check for event products
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);

                if ($product->get_type() == 'event') {
                    $event_link = get_post_meta($product->get_id(), '_event_link', true);

                    if (!empty($event_link)) {
                        // Display event link in plain text or HTML
                        if ($plain_text) {
                            echo __('Event details', 'lindame') . ":\n";
                            echo __('You can access the event here: ', 'lindame') . esc_url($event_link) . "\n";
                        } else {
                            echo '<h2>' . __('Event details', 'lindame') . '</h2>';
                            echo '<p>' . __('You can access the event here:', 'lindame') . ' <a href="' . esc_url($event_link) . '" target="_blank">' . esc_html($event_link) . '</a></p>';
                        }
                    }
                    break;
                }
            }
        }
        
        add_action('woocommerce_email_order_details', 'add_event_link_to_order_completed_email', 20, 3);
    }