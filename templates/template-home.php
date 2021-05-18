<?php
/**
 * Template Name: Home Page
 */
get_header(); ?>

<!--HOME PAGE SLIDER-->
<?php if (function_exists('home_slider_template')) {
    home_slider_template();
} ?>
<!--END of HOME PAGE SLIDER-->

<!-- BEGIN of main content -->
<div class="grid-container">
    <div class="grid-x grid-margin-x">
        <div class="cell">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) :
                    the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END of main content -->

<?php get_footer(); ?>
