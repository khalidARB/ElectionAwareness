<?php
// Global CTA Section (exclude on Contact page to avoid redundancy)
if (!is_page('contact') && !is_page_template('template-contact.php')) {
    get_template_part('template-parts/cta-section');
    get_template_part('template-parts/newsletter-section');
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

            <!-- Column 4: Social Media -->
            <div class="footer-col social-col">
                <h4 class="footer-heading"><?php esc_html_e('Follow Us', 'election-awareness'); ?></h4>
                <p><?php esc_html_e('Connect with us on social media for the latest updates.', 'election-awareness'); ?></p>
                <div class="footer-social-icons">
                    <?php
                    $social_links_raw = get_option('election_theme_social_links', '[]');
                    $social_links = json_decode($social_links_raw, true);

                    if (!empty($social_links) && is_array($social_links)) {
                        foreach ($social_links as $link) {
                            $platform = isset($link['platform']) ? $link['platform'] : '';
                            $url = isset($link['url']) ? $link['url'] : '#';
                            $aria_label = ucfirst($platform);

                            if (empty($url))
                                continue;

                            echo '<a href="' . esc_url($url) . '" aria-label="' . esc_attr($aria_label) . '" target="_blank" rel="noopener noreferrer">';

                            switch ($platform) {
                                case 'facebook':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>';
                                    break;
                                case 'instagram':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>';
                                    break;
                                case 'twitter':
                                case 'x':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>';
                                    break;
                                case 'linkedin':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>';
                                    break;
                                case 'youtube':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>';
                                    break;
                                case 'tiktok':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>';
                                    break;
                                default:
                                    break;
                            }

                            echo '</a>';
                        }
                    } else {
                        // Fallback
                        ?>
                        <a href="#" aria-label="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                        <a href="#" aria-label="Instagram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                        <a href="#" aria-label="X"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg></a>
                        <?php
                    }
                    ?>
                </div>
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

<!-- Auth Modal -->
<div id="auth-modal" class="auth-modal-overlay">
    <div class="auth-modal-container">
        <button class="auth-modal-close" aria-label="Close modal">&times;</button>
        <div class="auth-modal-header">
            <h2 class="auth-modal-title">Sign In</h2>
            <div class="auth-tabs">
                <button class="auth-tab active" data-target="login">Sign In</button>
                <button class="auth-tab" data-target="register">Sign Up</button>
            </div>
        </div>
        <div class="auth-modal-body">
            <!-- Login Form -->
            <form id="auth-login-form" class="auth-form active">
                <div class="auth-form-group">
                    <label for="login-user">Email or Phone Number</label>
                    <input type="text" id="login-user" name="login_user" placeholder="e.g. 01712345678 or email" required>
                </div>
                <div class="auth-form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="login_password" required>
                </div>
                <div class="auth-form-messages" id="login-messages"></div>
                <button type="submit" class="btn btn-primary auth-submit">Sign In</button>
                <?php wp_nonce_field('ajax-login-nonce', 'security-login'); ?>
            </form>

            <!-- Registration Form -->
            <form id="auth-register-form" class="auth-form">
                <div class="auth-form-group">
                    <label for="reg-name">Full Name *</label>
                    <input type="text" id="reg-name" name="reg_name" required>
                </div>
                <div class="auth-form-group">
                    <label for="reg-phone">Phone Number *</label>
                    <input type="tel" id="reg-phone" name="reg_phone" placeholder="e.g. 01712345678" required>
                </div>
                <div class="auth-form-group">
                    <label for="reg-email">Email (Optional)</label>
                    <input type="email" id="reg-email" name="reg_email">
                </div>
                <div class="auth-form-group">
                    <label for="reg-password">Password *</label>
                    <input type="password" id="reg-password" name="reg_password" required>
                </div>
                <div class="auth-form-messages" id="register-messages"></div>
                <button type="submit" class="btn btn-primary auth-submit">Sign Up</button>
                <?php wp_nonce_field('ajax-register-nonce', 'security-register'); ?>
            </form>
        </div>
    </div>
</div>

<?php wp_footer(); ?>

</body>

</html>