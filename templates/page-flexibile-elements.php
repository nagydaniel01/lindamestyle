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
            <section class="section section--page-title">
                <div class="container">
                    <header class="section__header">
                        <h1 class="section__title"><?php the_title(); ?></h1>
                    </header>
                </div>
            </section>
            <?php get_template_part('template-parts/flexibile-elements'); ?>
        </main>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
