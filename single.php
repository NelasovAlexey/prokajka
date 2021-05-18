<?php
/**
 * Single
 *
 * Loop container for single post content
 */
get_header(); ?>


<?php if (have_posts()) : ?>
<?php while (have_posts()) :
the_post(); ?>

<main class="main-content">
    <section class="single__bg text-center" <?php bg( get_attached_img_url(get_the_ID()), 'full_hd');  ?> >
        <div class="grid-container">
            <div class="grid-x">
                <h1 class="page-title cell entry__title"><?php the_title(); ?></h1>
            </div>
        </div>
    </section>

    <section class="grid-container">
        <div class="grid-x">
            <div class="cell">
                <h3 class="text-center">Cookies Form</h3>
                <form action="single.php" class="">
                    <input type="number" placeholder="your age">
                    <input type="button" value="send">
                </form>
            </div>
        </div>
    </section>



    <section class="single__content">
        <div class="grid-container">
            <div class="grid-x grid-margin-x">
                <!-- BEGIN of post content -->
                <div class="small-12 cell">
                    <article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                        <p class="entry__meta"><?php echo time_ago()?></p>
                        <div class="entry__content clearfix">
                            <?php the_content('', true); ?>
                        </div>
                        <h6 class="entry__cat"><?php the_category(', '); ?></h6>
                    </article>
                </div>
            </div>
        </div>

    </section>



</main>

    <?php endwhile; ?>
<?php endif; ?>




<?php get_footer(); ?>
