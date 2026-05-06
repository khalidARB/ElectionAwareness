<?php
/**
 * The main template file for the blog listing (Posts Page).
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- Page Header & Filters -->
    <section class="blog-header container section-spacing-top">
        <h1 class="page-title">
            <?php
            $heading = get_option('election_theme_all_news_heading', '');
            if (!empty($heading)) {
                echo esc_html($heading);
            } else {
                single_post_title();
            }
            ?>
        </h1>

        <!-- Filters: Horizontal Scrollable Pill List -->
        <div class="blog-filters-wrapper">
            <div class="blog-filters">
                <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="filter-pill active">All</a>
                <?php
                $target_categories = array('Elections', 'Policy', 'Opinion');
                foreach ($target_categories as $cat_name) {
                    $category = get_term_by('name', $cat_name, 'category');
                    if ($category) {
                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="filter-pill">' . esc_html($category->name) . '</a>';
                    }
                }

                // Also list other categories if needed, or stick to the core ones
                $other_categories = get_categories(array(
                    'exclude' => array_map(function ($name) {
                        $cat = get_term_by('name', $name, 'category');
                        return $cat ? $cat->term_id : 0;
                    }, $target_categories),
                    'parent' => 0,
                    'hide_empty' => true
                ));

                foreach ($other_categories as $category) {
                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="filter-pill">' . esc_html($category->name) . '</a>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-grid-section container section-spacing-bottom">
        <?php if (have_posts()): ?>
            <div class="blog-grid">
                <?php
                $delay = 0;
                while (have_posts()):
                    the_post();
                    $delay += 100; // Stagger delay
                    ?>
                    <article <?php post_class('blog-card stagger-fade-in'); ?> style="animation-delay:
                <?php echo esc_attr($delay); ?>ms;">
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
            <div class="no-results">
                <p>No posts found.</p>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php
get_footer();
