<?php
// Global CTA Section (exclude on Contact page to avoid redundancy)
if (!is_page('contact') && !is_page_template('template-contact.php')) {
    get_template_part('template-parts/cta-section');
}
?>

<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Column 1: Brand & Mission -->
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <?php
                    if (has_custom_logo()) {
                        $retina_logo = get_theme_mod('retina_logo');
                        if ($retina_logo) {
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home">';
                            echo '<img src="' . esc_url($logo[0]) . '" srcset="' . esc_url($logo[0]) . ' 1x, ' . esc_url($retina_logo) . ' 2x" class="custom-logo" alt="' . esc_attr(get_bloginfo('name')) . '">';
                            echo '</a>';
                        } else {
                            the_custom_logo();
                        }
                    } else {
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="logo-link">
                            <span class="logo-text-white">Election</span><span class="logo-text-yellow">Awareness</span>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <p class="mission-statement">
                    <?php echo nl2br(esc_html(get_theme_mod('footer_mission_text', 'Empowering voters with clear, unbiased insights into the political landscape. Modernizing the way you consume news.'))); ?>
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="footer-col links-col">
                <h4 class="footer-heading"><?php esc_html_e('Quick Links', 'election-awareness'); ?></h4>
                <?php
                if (has_nav_menu('footer_quick')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_quick',
                        'container' => false,
                        'menu_class' => 'footer-links',
                        'depth' => 1,
                    ));
                } else {
                    // Fallback if no menu assigned
                    echo '<ul class="footer-links"><li><a href="#">' . esc_html__('Please assign a menu', 'election-awareness') . '</a></li></ul>';
                }
                ?>
            </div>

            <!-- Column 3: Categories -->
            <div class="footer-col cats-col">
                <h4 class="footer-heading"><?php esc_html_e('Categories', 'election-awareness'); ?></h4>
                <?php
                if (has_nav_menu('footer_categories')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_categories',
                        'container' => false,
                        'menu_class' => 'footer-links',
                        'depth' => 1,
                    ));
                } else {
                    echo '<ul class="footer-links"><li><a href="#">' . esc_html__('Please assign a menu', 'election-awareness') . '</a></li></ul>';
                }
                ?>
            </div>

            <!-- Column 4: Newsletter -->
            <div class="footer-col newsletter-col">
                <h4 class="footer-heading">
                    <?php echo esc_html(get_theme_mod('footer_newsletter_heading', 'Newsletter')); ?></h4>
                <p><?php echo nl2br(esc_html(get_theme_mod('footer_newsletter_text', 'Get the latest updates directly in your inbox.'))); ?>
                </p>
                <form class="newsletter-form">
                    <input type="email" placeholder="<?php esc_attr_e('Your email address', 'election-awareness'); ?>"
                        required />
                    <button type="submit" class="btn-icon" aria-label="Join">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                <?php
                $copyright_default = '&copy; ' . date('Y') . ' Election Awareness. All rights reserved.';
                $copyright_text = get_theme_mod('footer_copyright_text', $copyright_default);
                echo wp_kses_post(str_replace('%year%', date('Y'), $copyright_text));
                ?>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<div id="mobile-menu-overlay"></div>
<div id="search-overlay-root"></div>

<?php wp_footer(); ?>

</body>

</html>