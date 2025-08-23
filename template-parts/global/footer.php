<?php
    $locations   = get_nav_menu_locations();
    $footer_logo = get_field('site_logo', 'option');
    $social      = get_field('social', 'option');
    $copyright   = get_field('copyright', 'option');
?>

<footer class="footer">
    <div class="footer__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="footer__title"><?php echo esc_html('Iratkozz fel legújabb híreinkért!', TEXT_DOMAIN); ?></h3>
                    <p><?php echo esc_html('Stílustippek egyenesen a postaládádba.', TEXT_DOMAIN); ?></p>
                </div>
                <div class="col-lg-6">
                    <?php get_template_part('template-parts/blocks/block', 'subscribe'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row pt-4">
                <div class="col-12 col-md-6 col-xl">
                    <div class="footer__block">
                        <?php if ($footer_logo) : ?>
                            <div class="logo logo--footer">
                                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo__link">
                                    <?php
                                        $logo_args = array(
                                            'class'     => 'logo__image',
                                            'alt'       => get_bloginfo('name'),
                                        );
                                        
                                        echo wp_get_attachment_image($footer_logo['ID'], array(80, 80), false, $logo_args);
                                    ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
        
                <div class="col-12 col-md-6 col-xl">
                    <div class="footer__block">
                        <?php if (!empty($social) && is_array($social)) : ?>
                            <h3 class="footer__title"><?php echo esc_html('Közösségi média', TEXT_DOMAIN); ?></h3>
                            <?php
                                $custom_names = [
                                    'linkedin'     => 'LinkedIn',
                                    'youtube'      => 'YouTube',
                                    'tiktok'       => 'TikTok'
                                ];
                            ?>
                            <nav class="footer__nav nav nav--footer nav--social">
                                <ul class="nav__list">
                                    <?php foreach ($social as $key => $row) : ?>
                                        <?php
                                            $social_url    = $row['social_link']['url'];
                                            $social_title  = $row['social_link']['title'];
                                            $social_target = $row['social_link']['target'] ?: '_self';
                                            $host          = parse_url($social_url, PHP_URL_HOST);
                                            $parts         = explode('.', $host);
                                            $base          = ($parts[0] === 'www') ? $parts[1] : $parts[0];
                                            $social_name   = $social_title ?: $custom_names[$base] ?? ucfirst($base);
                                        ?>

                                        <?php if (isset($social_url)) : ?>
                                            <li class="nav__item">
                                                <a href="<?php echo esc_url($social_url); ?>" target="<?php echo esc_attr($social_target); ?>" class="nav__link">
                                                    <svg class="icon icon-<?php esc_attr_e($base); ?>"><use xlink:href="#icon-<?php esc_attr_e($base); ?>"></use></svg>
                                                    <span><?php echo esc_html($social_name); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
        
                <div class="col-12 col-md-6 col-xl">
                    <div class="footer__block">
                        <?php 
                        $theme_location = 'footer_menu_1';
                        if ($locations && has_nav_menu($theme_location)) : ?>
                            <?php 
                                $menu_id = $locations[$theme_location];
                                $menu = wp_get_nav_menu_object($menu_id);
                            ?>
                            <?php if ( is_object($menu) && isset($menu->name) ) : ?>
                                <h3 class="footer__title"><?php echo esc_html($menu->name); ?></h3>
                            <?php endif; ?>
                            <nav class="footer__nav nav nav--footer">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location'    => $theme_location,
                                        'container'         => false,
                                        'menu_class'        => 'nav__list level0',
                                        'walker'            => new Default_Menu_Walker()
                                    ));
                                ?>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
        
                <div class="col-12 col-md-6 col-xl">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'category',
                        'hide_empty' => false,
                    ));

                    if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <div class="footer__block">
                            <h3 class="footer__title"><?php echo esc_html('Cikkeink', TEXT_DOMAIN); ?></h3>
                            <nav class="footer__nav nav nav--footer">
                                <ul class="nav__list">
                                    <?php
                                        wp_list_categories(array(
                                            'title_li'   => '',
                                            'orderby'    => 'name',
                                            'order'      => 'ASC',
                                            'show_count' => false,
                                            'hide_empty' => true,
                                        ));
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 col-xl">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'knowledge_base_cat',
                        'hide_empty' => false,
                    ));

                    if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <div class="footer__block">
                            <h3 class="footer__title"><?php echo esc_html('Tudástár', TEXT_DOMAIN); ?></h3>
                            <nav class="footer__nav nav nav--footer">
                                <ul class="nav__list">
                                    <?php
                                        wp_list_categories(array(
                                            'taxonomy'   => 'knowledge_base_cat',
                                            'title_li'   => '',
                                            'orderby'    => 'name',
                                            'order'      => 'ASC',
                                            'show_count' => false,
                                            'hide_empty' => true,
                                        ));
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center py-4 mt-4 border-top">
        <?php echo wpautop( wp_kses_post( $copyright ) ); ?>
    </div>
</footer>