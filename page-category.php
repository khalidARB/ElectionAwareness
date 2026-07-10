<?php
/**
 * Template Name: Category Main Page
 *
 * This template is used when the user creates a page with the slug "category"
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- Page Header & Filters -->
    <section class="blog-header container section-spacing-top">

        <!-- Filters: Horizontal Scrollable Pill List with Arrows -->
        <div class="blog-filters-container">
            <button class="scroll-arrow scroll-arrow-left" id="blog-filters-left" aria-label="Scroll Left">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="blog-filters-wrapper">
                <div class="blog-filters">
                    <a href="<?php echo esc_url(home_url('/category/')); ?>" class="filter-pill active">All</a>
                    <?php
                    $target_categories = array('Elections', 'Policy', 'Opinion');
                    foreach ($target_categories as $cat_name) {
                        $category = get_term_by('name', $cat_name, 'category');
                        if ($category) {
                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="filter-pill">' . esc_html($category->name) . '</a>';
                        }
                    }

                    // Also list other categories if needed
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
            <button class="scroll-arrow scroll-arrow-right" id="blog-filters-right" aria-label="Scroll Right">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="blog-grid-section container section-spacing-bottom">
        <?php 
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $category_query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => $paged
        ));
        
        if ($category_query->have_posts()): ?>
            <div class="blog-grid">
                <?php
                $delay = 0;
                while ($category_query->have_posts()):
                    $category_query->the_post();
                    $delay += 100; // Stagger delay
                    ?>
                    <article <?php post_class('blog-card stagger-fade-in'); ?> style="animation-delay: <?php echo esc_attr($delay); ?>ms;">
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
                echo paginate_links(array(
                    'total' => $category_query->max_num_pages,
                    'mid_size' => 2,
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                ));
                ?>
            </div>

            <?php wp_reset_postdata(); ?>

        <?php else: ?>
            <div class="no-results">
                <p>No posts found.</p>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php
get_footer();
