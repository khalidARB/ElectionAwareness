<?php
/**
 * The main template file for the blog listing (News Main Page).
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- 1. Hero Section (Full-Width Carousel) -->
    <section class="home-hero">
        <?php
        $hero_count = get_option('election_theme_hero_count', 3);
        $hero_query = new WP_Query(array(
            'posts_per_page' => $hero_count,
            'ignore_sticky_posts' => 1,
            // 'category_name' => 'featured' // Optional: Uncomment to filter by category
        ));

        if ($hero_query->have_posts()):
            echo '<div class="hero-slider">';
            while ($hero_query->have_posts()):
                $hero_query->the_post();
                $background_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
                $categories = get_the_category();
                $cat_name = !empty($categories) ? $categories[0]->name : 'News';
                ?>
                <div class="hero-slide">
                    <?php if ($background_image): ?>
                        <img src="<?php echo esc_url($background_image); ?>" class="hero-slide-img"
                            alt="<?php the_title_attribute(); ?>" <?php if ($hero_query->current_post === 0)
                                  echo 'fetchpriority="high" loading="eager"';
                              else
                                  echo 'loading="lazy"'; ?>>
                    <?php endif; ?>
                    <div class="hero-overlay"></div>
                    <div class="container hero-content">
                        <div class="hero-meta">
                            <span class="hero-cat">
                                <?php echo esc_html($cat_name); ?>
                            </span>
                            <span class="read-time">
                                <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
                            </span>
                        </div>
                        <h1 class="hero-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h1>
                    </div>
                </div>
            <?php endwhile;
            echo '</div>'; // .hero-slider
        
            // Custom Navigation
            echo '<div class="hero-nav container">
                    <button class="hero-prev" aria-label="Previous Slide">←</button>
                    <button class="hero-next" aria-label="Next Slide">→</button>
                  </div>';

            wp_reset_postdata();
        else:
            echo '<div class="container"><p>No featured posts found.</p></div>';
        endif;
        ?>
    </section>


    <!-- 2. "Trending Now" Ticker -->
    <section class="trending-ticker-section">
        <div class="ticker-wrapper">
            <div class="ticker-track">
                <?php
                $ticker_count = get_option('election_theme_ticker_count', 5);
                $ticker_query = new WP_Query(array(
                    'posts_per_page' => $ticker_count,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                if ($ticker_query->have_posts()):
                    while ($ticker_query->have_posts()):
                        $ticker_query->the_post(); ?>
                        <div class="ticker-item">
                            <span class="ticker-dot">•</span>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- 3. Latest Stories Grid (Masonry / Asymmetric) -->
    <section class="latest-stories container section-spacing">
        <?php
        $grid_heading = get_option('election_theme_grid_heading', 'Latest Stories');
        $grid_cta_text = get_option('election_theme_grid_cta_text', 'View All');
        $grid_cta_url = get_option('election_theme_grid_cta_url', get_post_type_archive_link('post'));
        $grid_count = get_option('election_theme_grid_count', 5);

        if (empty($grid_cta_url)) {
            $grid_cta_url = get_post_type_archive_link('post');
        }
        ?>
        <div class="section-header-wrapper">
            <h2 class="section-heading"><?php echo esc_html($grid_heading); ?></h2>
            <a href="<?php echo esc_url($grid_cta_url); ?>" class="btn-link">
                <?php echo esc_html($grid_cta_text); ?>
                <span class="arrow">→</span>
            </a>
        </div>
        <div class="stories-grid-layout">
            <?php
            // Skip the first 3 (used in hero) + 5 (used in ticker) if you want unique posts, 
            // but usually latest grid is just latest. Let's offset by 3 to avoid hero dupe.
            $grid_query = new WP_Query(array(
                'posts_per_page' => $grid_count,
                'offset' => 3
            ));

            $count = 0;
            if ($grid_query->have_posts()):
                while ($grid_query->have_posts()):
                    $grid_query->the_post();
                    $count++;
                    // First 2: Large, Rest: Small
                    $card_class = ($count <= 2) ? 'story-card-large' : 'story-card-small';
                    ?>
                    <article <?php post_class('story-card ' . $card_class . ' reveal-on-scroll'); ?>>
                        <?php if (has_post_thumbnail()): ?>
                            <div class="story-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail(($count <= 2) ? 'large' : 'medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="story-content">
                            <div class="story-meta">
                                <?php
                                $cats = get_the_category();
                                if ($cats)
                                    echo '<span class="story-cat">' . esc_html($cats[0]->name) . '</span>';
                                ?>
                                <span class="story-date">
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>
                            <h3 class="story-title"><a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a></h3>
                            <?php if ($count <= 2): ?>
                                <div class="story-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>




</main>

<?php
get_footer();
