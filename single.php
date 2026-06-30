<?php
/**
 * The template for displaying all single posts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

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
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on Facebook">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                        <a href="#" data-action="copy-link" data-url="<?php echo esc_url(get_permalink()); ?>" data-platform="Instagram" class="share-icon" aria-label="Share on Instagram">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        <a href="#" data-action="copy-link" data-url="<?php echo esc_url(get_permalink()); ?>" data-platform="YouTube" class="share-icon" aria-label="Share on YouTube">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-icon" aria-label="Share on LinkedIn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        </a>
                        <a href="#" data-action="copy-link" data-url="<?php echo esc_url(get_permalink()); ?>" data-platform="TikTok" class="share-icon" aria-label="Share on TikTok">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
                        </a>
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

<style>
    /* Share Toast Notification */
    .share-toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #0A1019; color: #FFF; border: 1px solid var(--color-electric-yellow, #FFFF00); padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 500; z-index: 100000; opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    .share-toast.active { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy link to clipboard for sharing
    document.querySelectorAll('a[data-action="copy-link"]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const url = link.getAttribute('data-url');
            const platform = link.getAttribute('data-platform');
            
            navigator.clipboard.writeText(url).then(() => {
                // Create beautiful toast notification
                const toast = document.createElement('div');
                toast.className = 'share-toast';
                toast.innerText = `Link copied! Share it on ${platform}`;
                document.body.appendChild(toast);
                
                // Trigger fade in & out
                setTimeout(() => {
                    toast.classList.add('active');
                }, 50);
                
                setTimeout(() => {
                    toast.classList.remove('active');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 2500);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
    });
});
</script>

<?php get_footer(); ?>