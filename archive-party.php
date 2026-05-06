<?php
/**
 * The template for displaying Party archives.
 */

get_header(); ?>

<main id="primary" class="site-main">
    <header class="page-header container section-spacing-top" style="text-align: center;">
        <h1 class="page-title">
            <?php echo esc_html(get_option('election_theme_parties_heading', 'All Political Parties')); ?>
        </h1>
        <p class="page-subtitle">
            <?php echo esc_html(get_option('election_theme_parties_subheading', 'A comprehensive directory of active political groups and their manifestos.')); ?>
        </p>
    </header>

    <section class="party-archive-section container section-spacing-bottom">
        <?php if (have_posts()): ?>
            <div class="party-list">
                <?php
                while (have_posts()):
                    the_post();
                    $leader = get_post_meta(get_the_ID(), '_party_leader', true);
                    $year = get_post_meta(get_the_ID(), '_party_year', true);
                    $seats = get_post_meta(get_the_ID(), '_party_seats', true);
                    $popularity = get_post_meta(get_the_ID(), '_party_popularity', true);

                    // Ensure numeric values to prevent PHP 8 fatal errors
                    $seats_val = floatval($seats);
                    $popularity_val = floatval($popularity);
                    $seats_percent = ($seats_val > 0) ? ($seats_val / 500) * 100 : 0;
                    ?>
                    <article <?php post_class('party-card reveal-on-scroll'); ?> id="party-<?php the_ID(); ?>">
                        <div class="party-card-main">
                            <!-- Left: Logo -->
                            <div class="party-logo">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                <?php else: ?>
                                    <div class="party-logo-placeholder"></div>
                                <?php endif; ?>
                            </div>

                            <!-- Middle: Info -->
                            <div class="party-info">
                                <h2 class="party-name">
                                    <?php the_title(); ?>
                                </h2>
                                <div class="party-meta">
                                    <span class="meta-item"><strong>Leader:</strong>
                                        <?php echo esc_html($leader); ?>
                                    </span>
                                    <span class="meta-item"><strong>Founded:</strong>
                                        <?php echo esc_html($year); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Right: Stats -->
                            <div class="party-stats">
                                <div class="stat-group">
                                    <label>Poll Popularity</label>
                                    <div class="progress-container">
                                        <div class="progress-bar" style="width: <?php echo esc_attr($popularity_val); ?>%;">
                                        </div>
                                        <span class="stat-value">
                                            <?php echo esc_html($popularity_val); ?>%
                                        </span>
                                    </div>
                                </div>
                                <div class="stat-group">
                                    <label>Current Seats</label>
                                    <div class="progress-container">
                                        <div class="progress-bar" style="width: <?php echo esc_attr($seats_percent); ?>%;">
                                        </div>
                                        <span class="stat-value">
                                            <?php echo esc_html($seats); ?> / 500
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Expand Trigger -->
                            <button class="party-expand-btn" aria-expanded="false"
                                aria-controls="party-content-<?php the_ID(); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                        </div>

                        <!-- Accordion Content -->
                        <div class="party-card-expanded" id="party-content-<?php the_ID(); ?>" hidden>
                            <div class="expanded-inner">
                                <div class="manifesto-section">
                                    <div class="manifesto-header"
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                        <h3 style="margin: 0;">Manifesto Summary</h3>
                                        <a href="<?php the_permalink(); ?>" class="btn-party-view" style="margin-top: 0;">View
                                            Full Profile</a>
                                    </div>
                                    <div class="manifesto-text">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </div>
                            </div>
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
            <p>No political parties found.</p>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>