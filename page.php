<?php
/**
 * The template for displaying all pages
 */

get_header();
?>

<main id="primary" class="site-main container section-spacing-top">

    <?php
    while (have_posts()):
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title page-title">', '</h1>'); ?>
            </header>

            <div class="entry-content">
                <?php
                the_content();

                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'election-awareness'),
                    'after' => '</div>',
                ));
                ?>
            </div>
        </article>

        <?php
    endwhile; // End of the loop.
    ?>

</main>

<?php
get_footer();
