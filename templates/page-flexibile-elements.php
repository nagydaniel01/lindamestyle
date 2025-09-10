<?php 
/**
 * Template Name: Flexibile Elements Template 
 * Template Post Type: page, post, knowledge_base
 */
?>

<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <main class="page page--flexibile-elements">
            <?php get_template_part('template-parts/flexibile-elements'); ?>
        </main>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
