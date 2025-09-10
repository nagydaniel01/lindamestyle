        <?php get_template_part('template-parts/global/footer', ''); ?>

        <?php
            $dialog_template_dir     = get_template_directory().'/template-parts/dialogs/';
            $sidebar_template_dir = get_template_directory().'/template-parts/sidebars/';

            if (is_dir($dialog_template_dir)) {
                $dialog_files = scandir($dialog_template_dir);
        
                foreach ($dialog_files as $key => $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $dialog_slug = pathinfo($file, PATHINFO_FILENAME);
                        get_template_part('template-parts/dialogs/'.$dialog_slug, '');
                    }
                }
            }

            if (is_dir($sidebar_template_dir)) {
                $sidebar_files = scandir($sidebar_template_dir);
        
                foreach ($sidebar_files as $key => $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $sidebar_slug = pathinfo($file, PATHINFO_FILENAME);
                        get_template_part('template-parts/sidebars/'.$sidebar_slug, '');
                    }
                }
            }
        ?>

        <?php wp_footer(); ?>
    </body>
</html>