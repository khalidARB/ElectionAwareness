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
                                case 'x':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>';
                                    break;
                                case 'linkedin':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" /><rect x="2" y="9" width="4" height="12" /><circle cx="4" cy="4" r="2" /></svg>';
                                    break;
                                case 'youtube':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>';
                                    break;
                                case 'tiktok':
                                    echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.17-2.86-.6-4.12-1.31a11.31 11.31 0 0 1-1.87-1.35v7.45c.03 1.83-.5 3.61-1.48 5.1a9.42 9.42 0 0 1-4.01 3.73c-1.74.83-3.69 1.11-5.61.8-1.92-.31-3.74-1.28-5.07-2.73-1.34-1.44-2.13-3.32-2.22-5.28-.09-1.96.46-3.92 1.58-5.5a9.38 9.38 0 0 1 4.18-3.41c1.51-.55 3.12-.66 4.67-.34v4.13c-1.12-.35-2.35-.29-3.42.17a5.35 5.35 0 0 0-2.6 2.45 5.3 5.3 0 0 0-.25 4.31c.36 1 .98 1.89 1.8 2.53.82.63 1.83.98 2.86 1 1.03.01 2.06-.27 2.94-.82.88-.55 1.57-1.35 1.98-2.3.41-.95.53-2.01.35-3.03V0h.01Z"/></svg>';
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
                        <a href="#" aria-label="X"><svg width="18" height="18" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z">
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