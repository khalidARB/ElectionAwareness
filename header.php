<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site">

        <header id="masthead" class="site-header">
            <div class="container header-container">
                <!-- Logo -->
                <div class="site-branding">
                    <?php
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $retina_logo = get_theme_mod('retina_logo');
                    $mobile_logo = get_theme_mod('mobile_logo');

                    if ($custom_logo_id) {
                        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                        echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home">';
                        
                        if ($mobile_logo) {
                            echo '<picture>';
                            echo '<source media="(max-width: 768px)" srcset="' . esc_url($mobile_logo) . '">';
                            if ($retina_logo) {
                                echo '<img src="' . esc_url($logo[0]) . '" srcset="' . esc_url($logo[0]) . ' 1x, ' . esc_url($retina_logo) . ' 2x" class="custom-logo" alt="' . esc_attr(get_bloginfo('name')) . '">';
                            } else {
                                echo '<img src="' . esc_url($logo[0]) . '" class="custom-logo" alt="' . esc_attr(get_bloginfo('name')) . '">';
                            }
                            echo '</picture>';
                        } else {
                            if ($retina_logo) {
                                echo '<img src="' . esc_url($logo[0]) . '" srcset="' . esc_url($logo[0]) . ' 1x, ' . esc_url($retina_logo) . ' 2x" class="custom-logo" alt="' . esc_attr(get_bloginfo('name')) . '">';
                            } else {
                                the_custom_logo();
                            }
                        }
                        
                        echo '</a>';
                    } else {
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="logo-link">
                            <span class="logo-text-white">Election</span><span class="logo-text-yellow">Awareness</span>
                        </a>
                        <?php
                    }
                    ?>
                </div>

                <!-- Social Icons -->
                <div class="header-social-icons">
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
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>';
                                    break;
                                case 'instagram':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>';
                                    break;
                                case 'twitter':
                                case 'x':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>';
                                    break;
                                case 'linkedin':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>';
                                    break;
                                case 'youtube':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>';
                                    break;
                                case 'tiktok':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>';
                                    break;
                                default:
                                    // Fallback icon if needed
                                    break;
                            }

                            echo '</a>';
                        }
                    } else {
                        // Fallback if no links are set
                        ?>
                        <a href="#" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                        <a href="#" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                        <a href="#" aria-label="X"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg></a>
                        <?php
                    }
                    ?>
                </div>

                <!-- Navigation -->
                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                            'fallback_cb' => false, // Fallback to manual links below if no menu set
                        )
                    );
                    ?>
                    <!-- Manual Fallback for Demo -->
                    <?php if (!has_nav_menu('primary')): ?>
                        <ul id="primary-menu" class="menu">
                            <li class="menu-item"><a href="/" class="magnetic-link">Home</a></li>
                            <li class="menu-item"><a href="/blogs" class="magnetic-link">Blogs</a></li>
                            <li class="menu-item"><a href="/parties" class="magnetic-link">Political Parties</a></li>
                            <li class="menu-item"><a href="/about" class="magnetic-link">About</a></li>
                            <li class="menu-item"><a href="/contact" class="magnetic-link">Contact</a></li>
                        </ul>
                    <?php endif; ?>
                </nav>

                <!-- Actions -->
                <div class="header-actions">
                    <button class="search-toggle" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <div class="account-dropdown-wrapper">
                        <?php if (is_user_logged_in()) : ?>
                            <style>
                                .perfect-round-avatar-link {
                                    display: block;
                                    width: 32px !important;
                                    height: 32px !important;
                                    min-width: 32px !important;
                                    min-height: 32px !important;
                                    max-width: 32px !important;
                                    max-height: 32px !important;
                                    border: 2px solid var(--color-electric-yellow);
                                    border-radius: 50% !important;
                                    overflow: hidden !important;
                                    padding: 0 !important;
                                    margin: 0 !important;
                                    box-sizing: border-box !important;
                                    flex-shrink: 0;
                                }
                                .perfect-round-avatar-link img {
                                    width: 100% !important;
                                    height: 100% !important;
                                    object-fit: cover !important;
                                    border-radius: 50% !important;
                                    display: block !important;
                                    margin: 0 !important;
                                    padding: 0 !important;
                                }
                            </style>
                            <!-- Logged In: Only Profile Picture (Rounded) -->
                            <a href="<?php echo esc_url(home_url('/my-account/')); ?>" class="perfect-round-avatar-link" aria-label="Account">
                                <?php 
                                    $current_user = wp_get_current_user();
                                    // Fetch double size for retina displays, but display at 44x44
                                    echo get_avatar($current_user->ID, 88, '', 'Profile Picture'); 
                                ?>
                            </a>
                        <?php else : ?>
                            <!-- Logged Out: Only Icon -->
                            <a href="#" class="account-icon-trigger" aria-label="Account" data-auth-action="login">
                                <svg class="account-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                    <button class="mobile-menu-toggle" aria-label="Menu">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </button>
                </div>
            </div>
        </header>