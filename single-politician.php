<?php
/**
 * The template for displaying all single Politician Profile posts
 */

get_header();

while (have_posts()):
    the_post();

    // Retrieve Meta Data
    $title = get_post_meta(get_the_ID(), '_politician_title', true) ?: 'Representative';
    $party_val = get_post_meta(get_the_ID(), '_politician_party', true);
    $party_name = election_get_politician_party_name(get_the_ID());
    $constituency = get_post_meta(get_the_ID(), '_politician_constituency', true) ?: 'National Assembly';
    $focus = get_post_meta(get_the_ID(), '_politician_focus', true) ?: 'General Reform';

    $party_link = '';
    if (!empty($party_val) && is_numeric($party_val)) {
        $party_post = get_post($party_val);
        if ($party_post && $party_post->post_type === 'party') {
            $party_link = get_permalink($party_val);
        }
    }
    ?>

    <main id="primary" class="site-main politician-single-page">

        <!-- Hero Section -->
        <div class="politician-single-hero section-spacing-top">
            <div class="container">
                <div class="politician-hero-layout">
                    <div class="politician-hero-content">
                        <?php election_the_breadcrumbs(); ?>
                        <span class="politician-role-label"><?php echo esc_html($title); ?></span>
                        <h1 class="entry-title politician-name"><?php the_title(); ?></h1>
                        <div class="politician-hero-meta">
                            <div class="hero-meta-item">
                                <span class="label">Constituency</span>
                                <span class="value"><?php echo esc_html($constituency); ?></span>
                            </div>
                            <div class="hero-meta-item">
                                <span class="label">Political Party</span>
                                <span class="value">
                                    <?php if (!empty($party_link)): ?>
                                        <a href="<?php echo esc_url($party_link); ?>" class="party-link-highlight"><?php echo esc_html($party_name); ?></a>
                                    <?php else: ?>
                                        <?php echo esc_html($party_name); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="politician-hero-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'politician-featured-img')); ?>
                        <?php else: ?>
                            <img src="https://i.pravatar.cc/400?u=<?php the_ID(); ?>" alt="Politician Photo" class="politician-featured-img">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Politician Stats / Detail Bar -->
        <div class="politician-stats-bar">
            <div class="container">
                <div class="details-grid">
                    <div class="detail-box">
                        <div class="detail-label">Focus Area</div>
                        <div class="detail-value"><?php echo esc_html($focus); ?></div>
                    </div>
                    <div class="detail-box">
                        <div class="detail-label">Affiliation Status</div>
                        <div class="detail-value">
                            <?php echo esc_html($party_name === 'Independent' ? 'Independent Representative' : 'Party Member'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content & Sidebar -->
        <div class="container politician-content-section section-spacing-bottom">
            <article class="politician-entry-content" style="grid-column: 1 / -1; width: 100%;">
                <h2 class="section-heading">Biography & Legislative Agenda</h2>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        </div>

    </main>

    <style>
    /* Politician Single View Premium Styles */
    .politician-single-page {
        background-color: var(--color-deep-void);
        color: var(--color-text-grey);
    }
    
    .politician-single-hero {
        background: linear-gradient(180deg, rgba(22, 31, 46, 0.6) 0%, rgba(11, 17, 28, 0.8) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 50px;
    }
    
    .politician-hero-layout {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 50px;
        align-items: center;
    }
    
    @media (max-width: 992px) {
        .politician-hero-layout {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .politician-hero-image {
            order: -1;
            margin: 0 auto;
            max-width: 350px;
        }
    }
    
    .politician-role-label {
        display: inline-block;
        background-color: rgba(250, 204, 21, 0.1);
        color: var(--color-electric-yellow);
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 6px 16px;
        border-radius: 50px;
        border: 1px solid rgba(250, 204, 21, 0.2);
        margin-bottom: 20px;
    }
    
    .politician-name {
        font-family: var(--font-heading);
        font-size: 3rem;
        color: var(--color-text-white);
        margin: 0 0 25px 0;
        line-height: 1.1;
    }
    
    .politician-hero-meta {
        display: flex;
        gap: 40px;
    }
    
    @media (max-width: 992px) {
        .politician-hero-meta {
            justify-content: center;
            flex-wrap: wrap;
        }
    }
    
    .hero-meta-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .hero-meta-item .label {
        font-size: 12px;
        text-transform: uppercase;
        color: var(--color-text-muted);
        letter-spacing: 1px;
    }
    
    .hero-meta-item .value {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text-white);
    }
    
    .party-link-highlight {
        color: var(--color-electric-yellow);
        text-decoration: none;
        transition: border-bottom 0.2s ease;
        border-bottom: 1px dashed transparent;
    }
    
    .party-link-highlight:hover {
        border-bottom-color: var(--color-electric-yellow);
    }
    
    .politician-hero-image img {
        width: 100%;
        height: auto;
        aspect-ratio: 1;
        object-fit: cover;
        border-radius: 20px;
        border: 1px solid var(--color-steel-blue);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }
    
    .politician-stats-bar {
        background-color: rgba(22, 31, 46, 0.4);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 25px 0;
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
    
    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .detail-box {
        background: rgba(11, 17, 28, 0.6);
        border: 1px solid var(--color-steel-blue);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
    }
    
    .detail-label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--color-text-muted);
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    
    .detail-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-text-white);
    }
    
    .politician-content-section {
        margin-top: 50px;
    }
    
    .section-heading {
        font-family: var(--font-heading);
        color: var(--color-text-white);
        font-size: 2rem;
        margin-bottom: 30px;
        position: relative;
        display: inline-block;
    }
    
    .section-heading::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 50px;
        height: 2px;
        background-color: var(--color-electric-yellow);
    }
    
    .entry-content {
        font-size: 17px;
        line-height: 1.8;
        color: var(--color-text-grey);
    }
    
    .entry-content p {
        margin-bottom: 25px;
    }
    </style>

    <?php
endwhile; // End of the loop.

get_footer();
