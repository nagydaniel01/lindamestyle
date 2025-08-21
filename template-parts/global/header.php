<?php
    $logo                         = get_field('site_logo', 'option');
    $header_partner_logo_repeater = get_field('header_partner_logo_repeater', 'option');
?>

<header class="header">
    <div class="container">
        <div class="header__inner">
            <nav class="navbar navbar-expand-lg">
                <!-- Brand -->
                <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php if ($logo) : ?>
                        <?php echo wp_get_attachment_image($logo['ID'], array(60, 60), false, array('alt' => esc_attr($logo['alt'] ?: get_bloginfo('name')))); ?>
                    <?php else : ?>
                        <?php bloginfo('name'); ?>
                    <?php endif; ?>
                </a>

                <!-- Mobile header actions -->
                <div class="d-flex align-items-center d-lg-none">
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <!-- Mobile My Account / Login -->
                        <?php if ( is_user_logged_in() ) : ?>
                            <?php 
                                $current_user = wp_get_current_user();
                                $avatar       = get_avatar( $current_user->ID, 32 );
                                $display_name = $current_user->display_name;
                            ?>
                            <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="d-flex h-100 align-items-center">
                                <?php echo $avatar; ?>
                                <span class="ms-2"><?php echo sprintf( esc_html__( 'Hello, %s', TEXT_DOMAIN ), esc_html( $display_name ) ); ?></span>
                            </a>
                        <?php else : ?>
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#login_formModal">
                                <svg class="icon icon-user"><use xlink:href="#icon-user"></use></svg>
                                <span><?php echo esc_html__( 'Login / Register', TEXT_DOMAIN ); ?></span>
                            </button>
                        <?php endif; ?>

                        <!-- Mobile Cart Trigger -->
                        <button class="btn btn-outline-secondary position-relative me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#minicartCanvas" aria-controls="minicartCanvas">
                            <svg class="icon icon-bag-shopping"><use xlink:href="#icon-bag-shopping"></use></svg>
                            <span class="visually-hidden"><?php echo esc_html__( 'Cart', TEXT_DOMAIN ); ?></span>
                            <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                <span class="cart_contents_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                            </div>
                        </button>
                    <?php endif; ?>

                    <!-- Navbar Toggler -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Offcanvas container (mobile right, desktop inline) -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
                    <div class="offcanvas-header d-lg-none">
                        <h5 class="offcanvas-title" id="mainMenuLabel"><?php echo esc_html__( 'Menu', TEXT_DOMAIN ); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body w-100 p-3 p-lg-0">
                        <div class="d-flex mb-3 d-lg-none">
                            <?php get_search_form(); ?>
                        </div>

                        <?php if ( has_nav_menu( 'primary_menu' ) ) : ?>
                            <?php
                                wp_nav_menu( array(
                                    'theme_location' => 'primary_menu',
                                    'container'      => false,
                                    'menu_class'     => 'navbar-nav align-items-lg-center',
                                    'fallback_cb'    => false,
                                ) );
                            ?>
                        <?php else : ?>
                            <p class="no-menu-assigned"><?php echo esc_html__( 'Please assign a menu in Appearance → Menus.', TEXT_DOMAIN ); ?></p>
                        <?php endif; ?>

                        <!-- Desktop header actions -->
                        <div class="header-actions d-none d-lg-flex ms-lg-auto">
                            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                <div class="nav-item ms-lg-3">
                                    <!-- My Account / Login -->
                                    <?php if ( is_user_logged_in() ) : ?>
                                        <?php 
                                            $current_user = wp_get_current_user();
                                            $avatar       = get_avatar( $current_user->ID, 32 );
                                            $display_name = $current_user->display_name;
                                        ?>
                                        <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="d-flex h-100 align-items-center">
                                            <?php echo $avatar; ?>
                                            <span class="ms-2"><?php echo sprintf( esc_html__( 'Hello, %s', TEXT_DOMAIN ), esc_html( $display_name ) ); ?></span>
                                        </a>
                                    <?php else : ?>
                                        <button type="button" class="btn btn-outline-primary btn-link" data-bs-toggle="modal" data-bs-target="#login_formModal">
                                            <svg class="icon icon-user"><use xlink:href="#icon-user"></use></svg>
                                            <span class="visually-hidden"><?php echo esc_html__( 'Login / Register', TEXT_DOMAIN ); ?></span>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <div class="nav-item ms-lg-3">
                                    <!-- Cart Trigger -->
                                    <button class="btn btn-outline-primary btn-link position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#minicartCanvas" aria-controls="minicartCanvas">
                                        <svg class="icon icon-bag-shopping"><use xlink:href="#icon-bag-shopping"></use></svg>
                                        <span class="visually-hidden"><?php echo esc_html__( 'Cart', TEXT_DOMAIN ); ?></span>
                                        <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            <span class="cart_contents_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                        </div>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="nav-item ms-lg-3">
                                <!-- Search bar Trigger -->
                                <button class="btn btn-outline-primary btn-link" data-bs-toggle="modal" data-bs-target="#searchModal">
                                    <svg class="icon icon-magnifying-glass"><use xlink:href="#icon-magnifying-glass"></use></svg>
                                    <span class="visually-hidden"><?php echo esc_html__('Search', TEXT_DOMAIN); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>