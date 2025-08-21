<?php
    // Get section values
    $section_title = $section['oembed_section_title'] ?? '';
    $section_slug  = sanitize_title($section_title);
    $section_lead  = $section['oembed_section_lead'] ?? '';
    $oembed        = $section['oembed'] ?? '';

    // Extract iframe src
    preg_match('/src="([^"]+)"/', $oembed, $matches);
    $src = $matches[1] ?? '';

    // Safe declare of helper function
    if (!function_exists('detect_oembed_type')) {
        function detect_oembed_type($url) {
            $host = parse_url($url, PHP_URL_HOST);

            $map = [
                'youtube'    => ['youtube.com', 'youtu.be'],
                'vimeo'      => ['vimeo.com'],
                'spotify'    => ['spotify.com'],
                'soundcloud' => ['soundcloud.com'],
                'tiktok'     => ['tiktok.com'],
            ];

            foreach ($map as $type => $domains) {
                foreach ($domains as $domain) {
                    if (strpos($host, $domain) !== false) {
                        return $type;
                    }
                }
            }
            return 'unknown';
        }
    }

    $oembed_type = !empty($src) ? detect_oembed_type($src) : 'unknown';

    // Update iframe with parameters and attributes
    if (!empty($src)) {
        $params   = ['controls' => 0, 'hd' => 1, 'autohide' => 1];
        $new_src  = add_query_arg($params, $src);
        $oembed   = str_replace($src, $new_src, $oembed);

        $oembed   = str_replace(
            '></iframe>',
            ' frameborder="0"></iframe>',
            $oembed
        );
    }
?>

<?php if (!empty($oembed)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>"
             class="section section--oembed section--<?php echo esc_attr($oembed_type); ?>">
        <div class="container">
            <div class="section__header">
                <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                <?php if (!empty($section_lead)) : ?>
                    <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                <?php endif; ?>
            </div>
            <div class="section__content">
                <?php if ($oembed_type === 'youtube') : ?>
                    <?php
                        $video_id    = get_youtube_video_id($src) ?? '';
                    ?>
                    <div class="section__video-wrapper"> 
                        <div class="youtube-player" data-id="<?php echo esc_attr($video_id); ?>"></div>
                    </div>
                <?php else : ?>
                    <?php echo $oembed; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
