<?php
/**
 * CTA Section Template Part
 * 
 * Displays the "Join the Awareness Movement" CTA.
 */
?>

<!-- 4. CTA Section -->
<section class="cta-section reveal-on-scroll">
    <div class="container">
        <div class="cta-content">
            <?php
            $heading = get_option('election_theme_cta_heading', 'Join the Awareness Movement');
            $subheading = get_option('election_theme_cta_subheading', 'Stay informed with the latest election updates and deep dives.');
            $btn_text = get_option('election_theme_cta_text', 'Get Involved');
            $btn_url = get_option('election_theme_cta_url', '/contact');
            ?>
            <h2><?php echo esc_html($heading); ?></h2>
            <p><?php echo esc_html($subheading); ?></p>
            <a href="<?php echo esc_url($btn_url); ?>" class="btn btn-primary btn-large">
                <?php echo esc_html($btn_text); ?> <span class="icon-arrow">→</span>
            </a>
        </div>
    </div>
</section>