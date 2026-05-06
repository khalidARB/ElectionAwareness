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
                    if (has_custom_logo()) {
                        $retina_logo = get_theme_mod('retina_logo');
                        if ($retina_logo) {
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home">';
                            echo '<img src="' . esc_url($logo[0]) . '" srcset="' . esc_url($logo[0]) . ' 1x, ' . esc_url($retina_logo) . ' 2x" class="custom-logo" alt="' . get_bloginfo('name') . '">';
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
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>';
                                    break;
                                case 'instagram':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>';
                                    break;
                                case 'twitter':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>';
                                    break;
                                case 'youtube':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>';
                                    break;
                                case 'linkedin':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z" /><circle cx="4" cy="4" r="2" /></svg>';
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
                        <a href="#" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg></a>
                        <a href="#" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg></a>
                        <a href="#" aria-label="Twitter"><svg width="18" height="18" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                </path>
                            </svg></a>
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
                    <button class="mobile-menu-toggle" aria-label="Menu">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </button>
                </div>
            </div>
        </header>