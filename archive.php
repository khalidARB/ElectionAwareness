<?php
/**
 * The template for displaying archive pages (Categories).
 */

get_header();

$current_object = get_queried_object();
$current_term_id = isset($current_object->term_id) ? $current_object->term_id : 0;
?>

<main id="primary" class="site-main archive-feed-page">

    <!-- 1. Minimalist Header -->
    <header class="archive-header container section-spacing-top">
        <div class="header-content-left">
            <?php election_the_breadcrumbs(); ?>
            <h1 class="page-title reveal-item"><?php single_term_title(); ?></h1>
            <?php if (get_the_archive_description()): ?>
                <div class="archive-description reveal-item">
                    <?php echo get_the_archive_description(); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- 2. Tag Pills (Horizontal Scroll) -->
    <div class="tag-cloud-wrapper container">
        <div class="tag-pills-scroll">
            <?php
            $tags = get_tags(array('hide_empty' => true, 'number' => 15));
            if ($tags):
                foreach ($tags as $tag):
                    $is_active = (is_tag() && $current_term_id === $tag->term_id) ? 'active' : '';
                    echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-pill ' . esc_attr($is_active) . '">' . esc_html($tag->name) . '</a>';
                endforeach;
            endif;
            ?>
        </div>
    </div>

    <!-- 3. Post Feed Container -->
    <section class="archive-feed container section-spacing-bottom">
        <?php if (have_posts()): ?>
            <div class="blog-grid">
                <?php
                $delay = 0;
                while (have_posts()):
                    the_post();
                    $delay += 100; // Stagger delay
                    ?>
                    <article <?php post_class('blog-card stagger-fade-in'); ?>
                        style="animation-delay: <?php echo esc_attr($delay); ?>ms;">
                        <!-- Image -->
                        <div class="blog-card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('medium_large'); ?>
                                <?php else: ?>
                                    <div class="placeholder-image"></div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <!-- Content -->
                        <div class="blog-card-content">
                            <h2 class="blog-card-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            <div class="blog-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="blog-read-more">Read More</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="blog-pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                ));
                ?>
            </div>

        <?php else: ?>
            <div class="no-results container">
                <p>We couldn't find any stories in this collection.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Return Home</a>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php
get_footer();
