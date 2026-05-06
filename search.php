<?php
/**
 * The template for displaying search results pages
 */

get_header(); ?>

<main id="primary" class="site-main search-results-page">

    <!-- 1. Massive Search Header -->
    <header class="search-hero">
        <div class="container">
            <div class="search-form-wrapper">
                <form role="search" method="get" class="search-page-form"
                    action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="search-field-massive" placeholder="Search for news or policy..."
                        value="<?php echo get_search_query(); ?>" name="s" />
                    <input type="hidden" name="post_type" value="post" />
                    <button type="submit" class="search-submit-massive" aria-label="Search">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </form>
            </div>
            <div class="search-result-count">
                <?php
                if (have_posts()) {
                    printf(esc_html__('Found %s results for \'%s\'', 'election-awareness'), '<span class="count-highlight">' . $wp_query->found_posts . '</span>', get_search_query());
                }
                ?>
            </div>
        </div>
    </header>

    <div class="container section-spacing">
        <?php if (have_posts()):
            $initial_posts = array();
            while (have_posts()):
                the_post();
                $initial_posts[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_the_permalink(),
                    'date' => get_the_date(),
                    'excerpt' => wp_trim_words(get_the_excerpt(), 25),
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                    'category' => get_the_category()[0]->name ?? ''
                );
            endwhile;

            $props = array(
                'initialPosts' => $initial_posts,
                'context' => array('searchQuery' => get_search_query()),
                'type' => 'list'
            );
            ?>

            <!-- 2. Results List (React Mounted) -->
            <div id="post-feed-root" data-props='<?php echo json_encode($props, JSON_HEX_APOS); ?>'>
                <!-- React will mount here -->
            </div>

        <?php else: ?>

            <!-- 3. No Results State -->
            <div class="no-results-container reveal-on-scroll">
                <div class="no-results-illustration">
                    <!-- Simple dark-themed search SVG -->
                    <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="1"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11" stroke-width="2" opacity="0.3"></line>
                    </svg>
                </div>
                <h2>We couldn't find that.</h2>
                <p>Maybe try these popular topics?</p>

                <div class="popular-suggestions">
                    <a href="/?s=Election" class="tag-pill">Election</a>
                    <a href="/?s=Policy" class="tag-pill">Policy</a>
                    <a href="/?s=Parties" class="tag-pill">Political Parties</a>
                    <a href="/?s=Economy" class="tag-pill">Economy</a>
                    <a href="/?s=Youth" class="tag-pill">Youth Vote</a>
                </div>
            </div>

        <?php endif; ?>
    </div>

</main>

<?php get_footer(); ?>