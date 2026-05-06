<?php
/**
 * The template for displaying all single Political Party posts
 */

get_header();

while (have_posts()):
    the_post();

    // Retrieve Meta Data
    $leader = get_post_meta(get_the_ID(), '_party_leader', true);
    $year = get_post_meta(get_the_ID(), '_party_year', true);
    $seats = get_post_meta(get_the_ID(), '_party_seats', true);
    $popularity = get_post_meta(get_the_ID(), '_party_popularity', true);

    // Ensure numeric values
    $seats_val = floatval($seats);
    $popularity_val = floatval($popularity);
    $seats_percent = ($seats_val > 0) ? ($seats_val / 500) * 100 : 0;
    ?>

    <main id="primary" class="site-main">

        <!-- Hero Section -->
        <div class="party-single-hero section-spacing-top">
            <div class="container">
                <div class="party-hero-layout">
                    <div class="party-hero-content">
                        <?php election_the_breadcrumbs(); ?>
                        <h1 class="entry-title party-title"><?php the_title(); ?></h1>
                        <div class="party-hero-meta">
                            <?php if ($leader): ?>
                                <div class="hero-meta-item">
                                    <span class="label">Party Leader</span>
                                    <span class="value"><?php echo esc_html($leader); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($year): ?>
                                <div class="hero-meta-item">
                                    <span class="label">Founded</span>
                                    <span class="value"><?php echo esc_html($year); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="party-hero-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'party-featured-img')); ?>
                        <?php else: ?>
                            <div class="party-placeholder-large"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Party Stats Bar -->
        <div class="party-stats-bar">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-label">Poll Popularity</div>
                        <div class="stat-graph-container">
                            <div class="stat-graph-bar" style="width: <?php echo esc_attr($popularity_val); ?>%"></div>
                        </div>
                        <div class="party-stat-number"><?php echo esc_html($popularity_val); ?>%</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Parliamentary Seats</div>
                        <div class="stat-graph-container">
                            <div class="stat-graph-bar" style="width: <?php echo esc_attr($seats_percent); ?>%"></div>
                        </div>
                        <div class="party-stat-number"><?php echo esc_html($seats); ?> <span class="stat-total">/ 500</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content & Sidebar -->
        <div class="container party-content-section section-spacing-bottom">
            <!-- Main Content (Full Width) -->
            <article class="party-entry-content" style="grid-column: 1 / -1; width: 100%;">
                <h2 class="section-heading">Manifesto & Values</h2>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        </div>

    </main>

    <?php
endwhile; // End of the loop.

get_footer();
