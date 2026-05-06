<?php
/**
 * Template Name: About Page
 */

get_header(); ?>

<main id="primary" class="site-main about-page section-spacing-top">

    <!-- Centered Header -->
    <header class="about-header container">
        <h1 class="split-title">
            <?php echo wp_kses_post(get_option('election_theme_about_header_title', 'We are the <span class="highlight">voice</span> of clear elections.')); ?>
        </h1>
    </header>

    <!-- Dynamic Sections -->
    <div class="about-sections">

        <!-- Our Mission: Left Content, Right Image -->
        <section class="about-split-section mission section-spacing">
            <div class="container split-wrapper">
                <div class="split-content reveal-on-scroll">
                    <span
                        class="section-label"><?php echo esc_html(get_option('election_theme_about_mission_label', 'Purpose')); ?></span>
                    <h2><?php echo esc_html(get_option('election_theme_about_mission_title', 'Our Mission')); ?></h2>
                    <div class="split-text">
                        <?php echo wp_kses_post(get_option('election_theme_about_mission_content', 'At Election Awareness, we believe that informed voters are the bedrock of democracy. Our platform provides unbiased data, real-time analytics, and deep investigative journalism to ensure every citizen has the tools they need to make the right choice.')); ?>
                    </div>
                </div>
                <div class="split-image reveal-on-scroll">
                    <div class="image-frame">
                        <img src="<?php echo esc_url(get_option('election_theme_about_mission_image', 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?auto=format&fit=crop&q=80&w=1200')); ?>"
                            alt="Mission Data Transparency">
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Vision: Right Content, Left Image -->
        <section class="about-split-section vision section-spacing-bottom">
            <div class="container split-wrapper">
                <div class="split-image reveal-on-scroll">
                    <div class="image-frame">
                        <img src="<?php echo esc_url(get_option('election_theme_about_vision_image', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200')); ?>"
                            alt="Democratic Future">
                    </div>
                </div>
                <div class="split-content reveal-on-scroll">
                    <span
                        class="section-label"><?php echo esc_html(get_option('election_theme_about_vision_label', 'Future')); ?></span>
                    <h2><?php echo esc_html(get_option('election_theme_about_vision_title', 'Our Vision')); ?></h2>
                    <div class="split-text">
                        <?php echo wp_kses_post(get_option('election_theme_about_vision_content', 'Our vision is a world where every election is conducted with absolute clarity and every vote is cast with confidence. We aspire to be the global standard for electoral intelligence, bridging the gap between complex data and public understanding.')); ?>
                    </div>
                </div>
            </div>
        </section>

    </div>

</main>

<?php get_footer(); ?>