<?php
/**
 * The template for displaying all single Politician Profile posts
 */

get_header();

while (have_posts()):
    the_post();

    // Retrieve Meta Data
    $party_val = get_post_meta(get_the_ID(), '_politician_party', true);
    $party_name = election_get_politician_party_name(get_the_ID());

    $party_link = '';
    if (!empty($party_val) && is_numeric($party_val)) {
        $party_post = get_post($party_val);
        if ($party_post && $party_post->post_type === 'party') {
            $party_link = get_permalink($party_val);
        }
    }
    ?>

    <main id="primary" class="site-main politician-single-page section-spacing-top section-spacing-bottom">
        <div class="container">
            <div class="politician-two-column-layout">
                <!-- Left Column: Content -->
                <div class="politician-content-col">
                    <?php election_the_breadcrumbs(); ?>
                    <h1 class="entry-title politician-name"><?php the_title(); ?></h1>
                    
                    <div class="politician-hero-meta">
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

                    <div class="politician-biography-section">
                        <h2 class="section-heading">Biography & Legislative Agenda</h2>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <div class="politician-timeline-section" style="margin-top: 50px;">
                        <h2 class="section-heading">Career Timeline & Milestones</h2>
                        
                        <?php
                        $timeline_data = get_post_meta(get_the_ID(), '_politician_timeline', true);
                        $events = array();
                        
                        if (!empty($timeline_data)) {
                            $lines = explode("\n", $timeline_data);
                            foreach ($lines as $line) {
                                $parts = explode('|', $line);
                                if (count($parts) >= 2) {
                                    $year = trim($parts[0]);
                                    $title = trim($parts[1]);
                                    $desc = isset($parts[2]) ? trim($parts[2]) : '';
                                    $events[] = array('year' => $year, 'title' => $title, 'desc' => $desc);
                                }
                            }
                        }
                        
                        // Fallback if no timeline data
                        if (empty($events)) {
                            $events = array(
                                array(
                                    'year' => '2018',
                                    'title' => 'Entered Public Service',
                                    'desc' => 'Began working on community advocacy, local representation, and civic awareness campaigns.'
                                ),
                                array(
                                    'year' => '2021',
                                    'title' => 'Elected to Assembly',
                                    'desc' => 'Won the election with a strong mandate focused on public education, local development, and infrastructure reform.'
                                ),
                                array(
                                    'year' => '2024',
                                    'title' => 'Sponsored Legislative Reform',
                                    'desc' => 'Drafted and successfully passed the election transparency bill to ensure fairer local government representation.'
                                )
                            );
                        }
                        ?>
                        
                        <div class="timeline-container">
                            <?php foreach ($events as $event): ?>
                                <div class="timeline-item">
                                    <div class="timeline-content-card">
                                        <div class="timeline-header">
                                            <span class="timeline-year"><?php echo esc_html($event['year']); ?></span>
                                            <h3 class="timeline-title"><?php echo esc_html($event['title']); ?></h3>
                                        </div>
                                        <?php if (!empty($event['desc'])): ?>
                                            <p class="timeline-desc"><?php echo esc_html($event['desc']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Image -->
                <div class="politician-image-col">
                    <div class="politician-hero-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'politician-featured-img')); ?>
                        <?php else: ?>
                            <img src="https://i.pravatar.cc/400/800?u=<?php the_ID(); ?>" alt="Politician Photo" class="politician-featured-img">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
    /* Politician Single View Premium Styles */
    .politician-single-page {
        background-color: var(--color-deep-void);
        color: var(--color-text-grey);
    }
    
    .politician-two-column-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
        align-items: start;
    }
    .politician-image-col {
        position: sticky;
        top: 120px;
    }
    
    @media (max-width: 992px) {
        .politician-two-column-layout {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        .politician-image-col {
            position: relative;
            top: 0;
            order: -1;
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }
    }
    
    .politician-name {
        font-family: var(--font-heading);
        font-size: 3rem;
        color: var(--color-text-white);
        margin: 15px 0 25px 0;
        line-height: 1.1;
    }
    
    .politician-hero-meta {
        display: flex;
        gap: 40px;
        margin-bottom: 40px;
        padding-bottom: 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
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
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 20px;
        border: 1px solid var(--color-steel-blue);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }
    
    .politician-biography-section {
        margin-top: 20px;
    }
    
    .section-heading {
        font-family: var(--font-heading);
        color: var(--color-text-white);
        font-size: 2rem;
        margin-bottom: 30px;
        position: relative;
        display: inline-block;
    }
    
    
    .entry-content {
        font-size: 17px;
        line-height: 1.8;
        color: var(--color-text-grey);
    }
    
    .entry-content p {
        margin-bottom: 25px;
    }
    
    /* Timeline Section Styles */
    .timeline-container {
        position: relative;
        padding-left: 30px;
        margin-top: 30px;
        border-left: 2px dashed var(--color-steel-blue);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 35px;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -37px;
        top: 6px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: var(--color-electric-yellow);
        border: 3px solid var(--color-deep-void);
        box-shadow: 0 0 0 2px var(--color-electric-yellow);
    }
    
    .timeline-content-card {
        background: rgba(22, 31, 46, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 20px;
        border-radius: 12px;
        transition: border-color 0.3s ease;
    }
    
    .timeline-content-card:hover {
        border-color: rgba(250, 204, 21, 0.3);
    }
    
    .timeline-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 12px;
    }
    
    .timeline-year {
        background-color: rgba(250, 204, 21, 0.1);
        color: var(--color-electric-yellow);
        font-weight: 700;
        font-size: 13px;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid rgba(250, 204, 21, 0.2);
    }
    
    .timeline-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-text-white);
        margin: 0;
    }
    
    .timeline-desc {
        font-size: 15px;
        line-height: 1.6;
        color: var(--color-text-grey);
        margin: 0;
    }
    </style>

    <?php
endwhile; // End of the loop.

get_footer();
