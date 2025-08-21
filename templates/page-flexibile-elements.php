<?php /* Template Name: Flexibile Elements Template */ ?>

<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <main class="page page--default">
            <div class="container">
                <div class="page__header">
                    <h1 class="page__title"><?php the_title(); ?></h1>
                </div>

                <?php get_template_part('template-parts/flexibile-elements'); ?>
            </div>
        </main>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
