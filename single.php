<?php
/**
 * The template for displaying all single posts
 */

get_header(); ?>

<div id="reading-progress-bar" class="reading-progress-bar"></div>

<main id="primary" class="site-main single-post-experience">

    <?php while (have_posts()):
        the_post();
        $reading_time = election_calculate_reading_time(get_the_ID());
        $featured_img = get_the_post_thumbnail_url(get_the_ID(), 'full');
        ?>

        <!-- 1. Massive Hero Header -->
        <header class="post-hero">
            <?php if ($featured_img): ?>
                <img src="<?php echo esc_url($featured_img); ?>" class="post-hero-img" alt="<?php the_title_attribute(); ?>"
                    fetchpriority="high" loading="eager">
            <?php endif; ?>
            <div class="post-hero-overlay"></div>
            <div class="container post-hero-content">
                <?php
                $cats = get_the_category();
                if ($cats): ?>
                    <span class="post-cat reveal-item">
                        <?php echo esc_html($cats[0]->name); ?>
                    </span>
                <?php endif; ?>

                <h1 class="post-title reveal-item">
                    <?php the_title(); ?>
                </h1>

                <div class="post-meta-bar reveal-item">
                    <div class="author-info">
                        <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                        <span class="author-name">By
                            <?php the_author(); ?>
                        </span>
                    </div>
                    <span class="meta-sep">|</span>
                    <span class="post-date">
                        <?php echo get_the_date(); ?>
                    </span>
                    <span class="meta-sep">|</span>
                    <span class="reading-time">
                        <?php echo esc_html($reading_time); ?> min read
                    </span>
                </div>
            </div>
        </header>

        <div class="container post-layout">
            <!-- 2. Sticky Sidebar (Left) -->
            <aside class="post-sidebar">
                <div class="sticky-share-bar">
                    <span class="share-label">Share</span>
                    <div class="social-share-icons">
                        <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                            target="_blank" class="share-icon" aria-label="Share on X">X</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"
                            class="share-icon" aria-label="Share on Facebook">Fb</a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>"
                            target="_blank" class="share-icon" aria-label="Share on WhatsApp">Wa</a>
                    </div>
                </div>
            </aside>

            <!-- 3. Post Content (Centered & Narrow) -->
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-content-body'); ?>>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- 4. Author Box -->
                <footer class="entry-footer">
                    <div class="author-box">
                        <div class="author-box-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 100); ?>
                        </div>
                        <div class="author-box-content">
                            <span class="author-label">About the Author</span>
                            <h3>
                                <?php the_author(); ?>
                            </h3>
                            <p>
                                <?php echo get_the_author_meta('description'); ?>
                            </p>
                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"
                                class="btn-follow">Follow Author</a>
                        </div>
                    </div>

                    <!-- 5. Navigation -->
                    <div class="post-navigation-minimal">
                        <div class="nav-prev">
                            <?php previous_post_link('%link', '&larr; Previous Story'); ?>
                        </div>
                        <div class="nav-next">
                            <?php next_post_link('%link', 'Next Story &rarr;'); ?>
                        </div>
                    </div>


                    <!-- 5b. Related Posts Section -->
                    <section class="related-posts-section">
                        <div class="related-header">
                            <div class="header-left">
                                <h3 class="related-title">Read Next</h3>
                                <div class="title-divider"></div>
                            </div>
                            <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="view-all-link">View
                                All</a>
                        </div>

                        <?php
                        $categories = get_the_category();
                        $cat_ids = array();
                        if ($categories) {
                            foreach ($categories as $cat) {
                                $cat_ids[] = $cat->term_id;
                            }
                        }

                        $related_query = new WP_Query(array(
                            'category__in' => $cat_ids,
                            'post__not_in' => array(get_the_ID()),
                            'posts_per_page' => 3,
                            'ignore_sticky_posts' => 1,
                            'orderby' => 'rand'
                        ));

                        if ($related_query->have_posts()): ?>
                            <div class="related-posts-grid scroll-snap-trigger">
                                <?php while ($related_query->have_posts()):
                                    $related_query->the_post(); ?>
                                    <article <?php post_class('related-mini-card'); ?>>
                                        <div class="mini-card-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()):
                                                    the_post_thumbnail('medium');
                                                else: ?>
                                                    <div class="placeholder-image"></div><?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="mini-card-content">
                                            <span class="mini-card-date"><?php echo get_the_date(); ?></span>
                                            <h4 class="mini-card-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                        </div>
                                    </article>
                                <?php endwhile;
                                wp_reset_postdata(); ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- 5c. FAQ Section -->
                    <?php
                    $faqs = get_post_meta(get_the_ID(), '_election_post_faqs', true);
                    if (!empty($faqs) && is_array($faqs)):
                        ?>
                        <section class="post-faq-section">
                            <h3 class="faq-title">Frequently Asked Questions</h3>
                            <div class="faq-container">
                                <?php foreach ($faqs as $faq): ?>
                                    <details class="faq-item">
                                        <summary class="faq-question">
                                            <?php echo esc_html($faq['question']); ?>
                                            <span class="faq-icon"></span>
                                        </summary>
                                        <div class="faq-answer">
                                            <?php echo wpautop(wp_kses_post($faq['answer'])); ?>
                                        </div>
                                    </details>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>


                    <!-- 6. Comments -->
                    <?php if (comments_open() || get_comments_number()): ?>
                        <div class="comments-area-wrapper">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>
        </div>

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>