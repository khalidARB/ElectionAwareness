<?php
/**
 * Template Name: Politician Profile
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<main id="primary" class="site-main politician-profile-page section-spacing-top">
    <?php
    while ( have_posts() ) :
        the_post();
        
        // Fetch custom meta values with secure fallbacks
        $politician_title = get_post_meta(get_the_ID(), '_politician_title', true) ?: 'Representative';
        $politician_party = get_post_meta(get_the_ID(), '_politician_party', true) ?: 'Independent';
        $politician_constituency = get_post_meta(get_the_ID(), '_politician_constituency', true) ?: 'National Assembly';
        $politician_education = get_post_meta(get_the_ID(), '_politician_education', true) ?: 'Higher Education';
        $politician_term = get_post_meta(get_the_ID(), '_politician_term', true) ?: 'Active Term';
        $politician_focus = get_post_meta(get_the_ID(), '_politician_focus', true) ?: 'Democratic Reforms';
        $politician_quote = get_post_meta(get_the_ID(), '_politician_quote', true) ?: 'For a better, more transparent future for everyone.';
        $politician_video_url = get_post_meta(get_the_ID(), '_politician_video_url', true);
        
        $social_fb = get_post_meta(get_the_ID(), '_politician_social_fb', true);
        $social_x = get_post_meta(get_the_ID(), '_politician_social_x', true);
        $social_ig = get_post_meta(get_the_ID(), '_politician_social_ig', true);
        $social_ln = get_post_meta(get_the_ID(), '_politician_social_ln', true);
        $social_yt = get_post_meta(get_the_ID(), '_politician_social_yt', true);
        ?>

        <!-- Profile Hero Section -->
        <section class="politician-hero container">
            <div class="politician-hero-wrapper reveal-on-scroll">
                <div class="politician-photo-column">
                    <div class="politician-image-frame">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('class' => 'politician-main-photo')); ?>
                        <?php else : ?>
                            <img src="https://i.pravatar.cc/400?u=politician" alt="Politician Default Photo" class="politician-main-photo">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="politician-details-column">
                    <span class="politician-label"><?php echo esc_html($politician_title); ?></span>
                    <h1 class="politician-name"><?php the_title(); ?></h1>
                    
                    <div class="politician-meta-pills">
                        <span class="meta-pill party-pill">
                            <span class="pill-dot"></span>
                            <?php echo esc_html($politician_party); ?>
                        </span>
                        <span class="meta-pill constituency-pill">
                            <?php echo esc_html($politician_constituency); ?>
                        </span>
                    </div>

                    <!-- Key Stats Grid -->
                    <div class="politician-stats-grid">
                        <div class="stat-card">
                            <span class="stat-label">Term in Office</span>
                            <span class="stat-value"><?php echo esc_html($politician_term); ?></span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Education</span>
                            <span class="stat-value"><?php echo esc_html($politician_education); ?></span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Core Focus</span>
                            <span class="stat-value"><?php echo esc_html($politician_focus); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bio & Agenda Section -->
        <section class="politician-bio-section container section-spacing">
            <div class="politician-bio-grid">
                <div class="bio-main-content reveal-on-scroll">
                    <h2 class="section-heading-accent">Biography & Vision</h2>
                    <div class="bio-content-text">
                        <?php the_content(); ?>
                    </div>

                    <?php if ($politician_quote) : ?>
                        <blockquote class="politician-quote">
                            <p>"<?php echo esc_html($politician_quote); ?>"</p>
                        </blockquote>
                    <?php endif; ?>
                </div>

                <div class="agenda-sidebar reveal-on-scroll">
                    <div class="agenda-card">
                        <h3 class="agenda-title">Key Agenda & Priorities</h3>
                        
                        <?php
                        $agenda_items_json = get_post_meta(get_the_ID(), '_politician_agenda_items', true) ?: '[]';
                        $agenda_items = json_decode($agenda_items_json, true);
                        if (empty($agenda_items)) {
                            $agenda_items = array(
                                array('title' => 'Transparency', 'desc' => 'Ensuring all public accounts and votes are fully audited and open.'),
                                array('title' => 'Economic Growth', 'desc' => 'Promoting youth employment and high-tech sector investments.'),
                                array('title' => 'Public Education', 'desc' => 'Modernizing digital classrooms and improving teacher pay.')
                            );
                        }
                        ?>
                        
                        <ul class="agenda-list">
                            <?php foreach ($agenda_items as $item) : ?>
                                <li class="agenda-item">
                                    <div class="agenda-bullet"></div>
                                    <div class="agenda-text">
                                        <h4><?php echo esc_html($item['title']); ?></h4>
                                        <p><?php echo esc_html($item['desc']); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Video Campaign & Connect Section -->
        <?php if (!empty($politician_video_url) || !empty($social_fb) || !empty($social_x) || !empty($social_ig) || !empty($social_ln) || !empty($social_yt)) : ?>
            <section class="politician-media-section section-spacing-bottom container">
                <div class="politician-media-grid">
                    <?php if (!empty($politician_video_url)) : ?>
                        <div class="media-video-column reveal-on-scroll">
                            <h2 class="section-heading-accent">Campaign Video</h2>
                            <div class="video-container-wrapper">
                                <iframe width="560" height="315" src="<?php echo esc_url($politician_video_url); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="media-connect-column reveal-on-scroll">
                        <h2 class="section-heading-accent">Connect & Follow</h2>
                        <p class="connect-sub">Stay updated with our latest legislative efforts, rallies, and town hall meetings.</p>
                        
                        <div class="politician-social-links">
                            <?php if ($social_fb) : ?>
                                <a href="<?php echo esc_url($social_fb); ?>" target="_blank" rel="noopener noreferrer" class="politician-social-btn" aria-label="Facebook">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                    <span>Facebook</span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($social_ig) : ?>
                                <a href="<?php echo esc_url($social_ig); ?>" target="_blank" rel="noopener noreferrer" class="politician-social-btn" aria-label="Instagram">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                    <span>Instagram</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_x) : ?>
                                <a href="<?php echo esc_url($social_x); ?>" target="_blank" rel="noopener noreferrer" class="politician-social-btn" aria-label="X (Twitter)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
                                    <span>X / Twitter</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_ln) : ?>
                                <a href="<?php echo esc_url($social_ln); ?>" target="_blank" rel="noopener noreferrer" class="politician-social-btn" aria-label="LinkedIn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                                    <span>LinkedIn</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_yt) : ?>
                                <a href="<?php echo esc_url($social_yt); ?>" target="_blank" rel="noopener noreferrer" class="politician-social-btn" aria-label="YouTube">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                                    <span>YouTube</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php endwhile; ?>
</main>

<style>
/* Politician Profile Layout Styles */
.politician-profile-page {
    background-color: var(--color-deep-void);
    color: var(--color-text-grey);
}

.politician-hero-wrapper {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 50px;
    align-items: center;
    background: rgba(22, 31, 46, 0.4);
    border: 1px solid var(--color-steel-blue);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 50px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
}

@media (max-width: 768px) {
    .politician-hero-wrapper {
        grid-template-columns: 1fr;
        padding: 25px;
        gap: 30px;
        text-align: center;
    }
}

.politician-image-frame {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid var(--color-steel-blue);
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
}

.politician-main-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.politician-image-frame:hover .politician-main-photo {
    transform: scale(1.05);
}

.politician-label {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--color-electric-yellow);
    font-weight: 700;
    display: inline-block;
    margin-bottom: 10px;
}

.politician-name {
    font-size: 3.5rem;
    font-family: var(--font-heading);
    margin: 0 0 15px 0;
    color: var(--color-text-white);
    line-height: 1.1;
}

.politician-meta-pills {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .politician-meta-pills {
        justify-content: center;
    }
}

.meta-pill {
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.party-pill {
    background-color: rgba(255, 255, 0, 0.1);
    color: var(--color-electric-yellow);
    border-color: rgba(255, 255, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 8px;
}

.pill-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--color-electric-yellow);
}

.constituency-pill {
    background-color: rgba(255, 255, 255, 0.05);
    color: var(--color-text-white);
}

.politician-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding-top: 30px;
}

@media (max-width: 576px) {
    .politician-stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

.stat-card {
    background: rgba(8, 11, 16, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: rgba(255, 255, 0, 0.15);
    transform: translateY(-2px);
}

.stat-label {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--color-text-muted);
    margin-bottom: 6px;
    letter-spacing: 1px;
}

.stat-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-white);
}

/* Bio & Agenda Grid */
.politician-bio-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 50px;
}

@media (max-width: 992px) {
    .politician-bio-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

.section-heading-accent {
    font-family: var(--font-heading);
    font-size: 2.2rem;
    color: var(--color-text-white);
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 12px;
}

.section-heading-accent::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: var(--color-electric-yellow);
    border-radius: 2px;
}

.bio-content-text {
    font-size: 16px;
    line-height: 1.8;
    color: var(--color-text-grey);
}

.bio-content-text p {
    margin-bottom: 20px;
}

.politician-quote {
    border-left: 4px solid var(--color-electric-yellow);
    padding-left: 24px;
    margin: 40px 0 20px 0;
    font-style: italic;
}

.politician-quote p {
    font-size: 20px;
    line-height: 1.6;
    color: var(--color-text-white);
}

/* Agenda Card */
.agenda-card {
    background: rgba(22, 31, 46, 0.3);
    border: 1px solid var(--color-steel-blue);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.agenda-title {
    font-family: var(--font-heading);
    font-size: 1.6rem;
    color: var(--color-text-white);
    margin-top: 0;
    margin-bottom: 25px;
}

.agenda-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.agenda-item {
    display: flex;
    gap: 16px;
}

.agenda-bullet {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: var(--color-electric-yellow);
    border: 3px solid var(--color-deep-void);
    box-shadow: 0 0 0 2px var(--color-electric-yellow);
    flex-shrink: 0;
    margin-top: 6px;
}

.agenda-text h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text-white);
    margin: 0 0 6px 0;
}

.agenda-text p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
    color: var(--color-text-muted);
}

/* Media Grid */
.politician-media-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 50px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding-top: 50px;
}

@media (max-width: 992px) {
    .politician-media-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

.video-container-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 ratio */
    height: 0;
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid var(--color-steel-blue);
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

.video-container-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.connect-sub {
    font-size: 15px;
    color: var(--color-text-muted);
    margin-bottom: 30px;
    line-height: 1.6;
}

.politician-social-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.politician-social-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    background-color: rgba(22, 31, 46, 0.4);
    border: 1px solid var(--color-steel-blue);
    padding: 14px 20px;
    border-radius: 10px;
    color: var(--color-text-grey);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.politician-social-btn:hover {
    color: black;
    background-color: var(--color-electric-yellow);
    border-color: var(--color-electric-yellow);
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(250, 204, 21, 0.2);
}

.politician-social-btn svg {
    flex-shrink: 0;
}
</style>

<?php get_footer(); ?>
