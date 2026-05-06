<?php
/**
 * Election Awareness Theme functions and definitions
 * 
 * SECURITY: Standard hardening implemented.
 */

// Disable XML-RPC for security
add_filter('xmlrpc_enabled', '__return_false');

// Remove WP Version for security
remove_action('wp_head', 'wp_generator');

// Disable Pingbacks
add_filter('xmlrpc_methods', function ($methods) {
    unset($methods['pingback.ping']);
    return $methods;
});

/**
 * Add Meta Description
 */
function election_add_meta_description() {
    if ( is_front_page() || is_home() ) {
        echo '<meta name="description" content="Empower your vote with Election Awareness. Get modern, unbiased political analysis, breaking election news, and deep dives into party policies.">' . "\n";
    }
}
add_action( 'wp_head', 'election_add_meta_description' );

/**
 * SEO: Image Optimization - Prioritize WebP and SVG Support
 */

// 1. Enable SVG Upload Support
function election_enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'election_enable_svg_uploads');

// 2. Prioritize WebP for Generated Images
function election_prioritize_webp_format($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
}
add_filter('image_editor_output_format', 'election_prioritize_webp_format');

// 3. Set Default Image Quality (SEO Sweet Spot)
function election_set_image_quality($quality) {
    return 85;
}
add_filter('wp_editor_set_quality', 'election_set_image_quality');
add_filter('jpeg_quality', 'election_set_image_quality');
add_filter('webp_quality', 'election_set_image_quality');

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function election_awareness_scripts()
{
    // Enqueue Google Fonts
    wp_enqueue_style('election-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;700&family=Public+Sans:wght@400;600&family=Space+Grotesk:wght@500;700&display=swap', array(), null);

    // Enqueue Main Stylesheet (Cache busting with filemtime())
    wp_enqueue_style('election-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));


    // Enqueue React Build
    $asset_file_path = get_template_directory() . '/build/index.asset.php';

    if (file_exists($asset_file_path)) {

        $asset_file = include($asset_file_path);

        wp_enqueue_script(
            'election-app',
            get_template_directory_uri() . '/build/index.js',
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );

        // Localize Menu Data for Mobile Menu
        $menu_items = array();
        $locations = get_nav_menu_locations();
        if (isset($locations['primary'])) {
            $menu = wp_get_nav_menu_object($locations['primary']);
            $items = wp_get_nav_menu_items($menu->term_id);
            if ($items) {
                foreach ($items as $item) {
                    $menu_items[] = array(
                        'title' => $item->title,
                        'url' => $item->url,
                    );
                }
            }
        }

        // Fallback menu if none set
        if (empty($menu_items)) {
            $menu_items = array(
                array('title' => 'Home', 'url' => home_url('/')),
                array('title' => 'Blogs', 'url' => home_url('/blogs')),
                array('title' => 'Political Parties', 'url' => home_url('/political-parties')),
                array('title' => 'About', 'url' => home_url('/about')),
                array('title' => 'Contact', 'url' => home_url('/contact')),
            );
        }

        wp_localize_script('election-app', 'electionAppData', array(
            'menuItems' => $menu_items,
            'homeUrl' => home_url('/')
        ));
    }

    // Dynamic Theme Colors
    $deep_void = get_theme_mod('color_deep_void', '#080B10');
    $midnight_blue = get_theme_mod('color_midnight_blue', '#0A1019');
    $steel_blue = get_theme_mod('color_steel_blue', '#161F2E');
    $electric_yellow = get_theme_mod('color_electric_yellow', '#FFFF00');
    $text_white = get_theme_mod('color_text_white', '#FFFFFF');
    $text_grey = get_theme_mod('color_text_grey', '#E2E8F0');
    $text_muted = get_theme_mod('color_text_muted', '#94A3B8');

    $custom_css = "
        :root {
            --color-deep-void: {$deep_void} !important;
            --color-midnight-blue: {$midnight_blue} !important;
            --color-steel-blue: {$steel_blue} !important;
            --color-electric-yellow: {$electric_yellow} !important;
            --color-text-white: {$text_white} !important;
            --color-text-grey: {$text_grey} !important;
            --color-text-muted: {$text_muted} !important;
        }
    ";
    wp_add_inline_style('election-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'election_awareness_scripts');

/**
 * Performance: Preload LCP Images
 */
function election_preload_lcp_images()
{
    $lcp_image = '';

    if (is_front_page()) {
        $hero_query = new WP_Query(array(
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1,
        ));
        if ($hero_query->have_posts()) {
            $hero_query->the_post();
            $lcp_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            wp_reset_postdata();
        }
    } elseif (is_single()) {
        $lcp_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }

    if ($lcp_image) {
        echo '<link rel="preload" as="image" href="' . esc_url($lcp_image) . '" fetchpriority="high">';
    }

    // Preconnect to Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head', 'election_preload_lcp_images', 1);

function election_awareness_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails', array('post', 'page', 'party', 'product'));
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

    // Register Navigation Menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'election-awareness'),
        'footer_quick' => esc_html__('Footer Quick Links', 'election-awareness'),
        'footer_categories' => esc_html__('Footer Categories', 'election-awareness'),
    ));
}
add_action('after_setup_theme', 'election_awareness_setup');

/**
 * Register Custom Post Types on Init
 */
function election_awareness_cpt_init()
{
    // Register Political Party CPT
    register_post_type('party', array(
        'labels' => array(
            'name' => __('Political Parties', 'election-awareness'),
            'singular_name' => __('Political Party', 'election-awareness'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => 'political-parties',
        'rewrite' => array('slug' => 'political-parties'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-groups',
    ));

    // Register Contact Inquiries CPT
    register_post_type('inquiry', array(
        'labels' => array(
            'name' => __('Inquiries', 'election-awareness'),
            'singular_name' => __('Inquiry', 'election-awareness'),
            'menu_name' => __('Inquiries', 'election-awareness'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-email',
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // Users can't create from admin
        ),
        'map_meta_cap' => true,
    ));

    // Register Product CPT
    register_post_type('product', array(
        'labels' => array(
            'name' => __('Products', 'election-awareness'),
            'singular_name' => __('Product', 'election-awareness'),
            'add_new' => __('Add New Product', 'election-awareness'),
            'add_new_item' => __('Add New Product', 'election-awareness'),
            'edit_item' => __('Edit Product', 'election-awareness'),
            'all_items' => __('All Products', 'election-awareness'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => 'shop',
        'rewrite' => array('slug' => 'shop'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-cart',
    ));

    // Flush rewrite rules to ensure new CPT slugs work immediately
    // Ideally, this should be done only on theme activation, but for debugging we force it once.
    flush_rewrite_rules(true);
}
add_action('init', 'election_awareness_cpt_init');

/**
 * Force Archive Template for Party CPT
 */
function election_force_party_template($template)
{
    if (is_post_type_archive('party')) {
        $new_template = locate_template(array('archive-party.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'election_force_party_template');

// Party Meta Boxes
function party_add_meta_boxes()
{
    add_meta_box('party_details', 'Party Details', 'party_details_callback', 'party', 'normal', 'high');
}
add_action('add_meta_boxes', 'party_add_meta_boxes');

function party_details_callback($post)
{
    $leader = get_post_meta($post->ID, '_party_leader', true);
    $year = get_post_meta($post->ID, '_party_year', true);
    $seats = get_post_meta($post->ID, '_party_seats', true);
    $popularity = get_post_meta($post->ID, '_party_popularity', true);

    wp_nonce_field('party_details_nonce', 'party_details_nonce');
    ?>
    <p>
        <label>Leader Name:</label><br>
        <input type="text" name="party_leader" value="<?php echo esc_attr($leader); ?>" style="width:100%;">
    </p>
    <p>
        <label>Founding Year:</label><br>
        <input type="number" name="party_year" value="<?php echo esc_attr($year); ?>" style="width:100%;">
    </p>
    <p>
        <label>Current Seats (0-500):</label><br>
        <input type="number" name="party_seats" value="<?php echo esc_attr($seats); ?>" style="width:100%;">
    </p>
    <p>
        <label>Poll Popularity (0-100%):</label><br>
        <input type="number" name="party_popularity" value="<?php echo esc_attr($popularity); ?>" style="width:100%;">
    </p>
    <?php
}

function party_save_meta($post_id)
{
    if (!isset($_POST['party_details_nonce']) || !wp_verify_nonce($_POST['party_details_nonce'], 'party_details_nonce'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    if (isset($_POST['party_leader']))
        update_post_meta($post_id, '_party_leader', sanitize_text_field($_POST['party_leader']));
    if (isset($_POST['party_year']))
        update_post_meta($post_id, '_party_year', sanitize_text_field($_POST['party_year']));
    if (isset($_POST['party_seats']))
        update_post_meta($post_id, '_party_seats', sanitize_text_field($_POST['party_seats']));
    if (isset($_POST['party_popularity']))
        update_post_meta($post_id, '_party_popularity', sanitize_text_field($_POST['party_popularity']));
}
add_action('save_post', 'party_save_meta');

/**
 * Product Meta Boxes
 */
function product_add_meta_boxes()
{
    add_meta_box('product_details', 'Product Details', 'product_details_callback', 'product', 'normal', 'high');
}
add_action('add_meta_boxes', 'product_add_meta_boxes');

function product_details_callback($post)
{
    $price = get_post_meta($post->ID, '_product_price', true);
    $buy_url = get_post_meta($post->ID, '_product_buy_url', true);
    $short_desc = get_post_meta($post->ID, '_product_short_desc', true);

    wp_nonce_field('product_details_nonce', 'product_details_nonce');
    ?>
    <p>
        <label>Price ($):</label><br>
        <input type="number" step="0.01" min="0" name="product_price" value="<?php echo esc_attr($price); ?>" style="width:100%;">
    </p>
    <p>
        <label>Buy URL (external link):</label><br>
        <input type="url" name="product_buy_url" value="<?php echo esc_attr($buy_url); ?>" style="width:100%;" placeholder="https://">
    </p>
    <p>
        <label>Short Description (shown on card, max 2 lines):</label><br>
        <textarea name="product_short_desc" style="width:100%;" rows="2"><?php echo esc_textarea($short_desc); ?></textarea>
    </p>
    <?php
}

function product_save_meta($post_id)
{
    if (!isset($_POST['product_details_nonce']) || !wp_verify_nonce($_POST['product_details_nonce'], 'product_details_nonce'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;
    if (get_post_type($post_id) !== 'product')
        return;

    if (isset($_POST['product_price']))
        update_post_meta($post_id, '_product_price', sanitize_text_field($_POST['product_price']));
    if (isset($_POST['product_buy_url']))
        update_post_meta($post_id, '_product_buy_url', esc_url_raw($_POST['product_buy_url']));
    if (isset($_POST['product_short_desc']))
        update_post_meta($post_id, '_product_short_desc', sanitize_textarea_field($_POST['product_short_desc']));
}
add_action('save_post', 'product_save_meta');

/**
 * FAQ Meta Box for Posts
 */
function election_faq_add_meta_box()
{
    add_meta_box(
        'election_post_faq',
        'Frequently Asked Questions',
        'election_faq_meta_box_callback',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'election_faq_add_meta_box');

function election_faq_meta_box_callback($post)
{
    wp_nonce_field('election_faq_nonce', 'election_faq_nonce');
    $faqs = get_post_meta($post->ID, '_election_post_faqs', true);

    if (empty($faqs) || !is_array($faqs)) {
        $faqs = array(array('question' => '', 'answer' => ''));
    }
    ?>
    <div id="election-faq-wrapper">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="faq-item" style="margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">
                <p>
                    <label>Question:</label><br>
                    <input type="text" name="election_faqs[<?php echo $index; ?>][question]"
                        value="<?php echo esc_attr($faq['question']); ?>" style="width: 100%;">
                </p>
                <p>
                    <label>Answer:</label><br>
                    <textarea name="election_faqs[<?php echo $index; ?>][answer]" style="width: 100%;"
                        rows="3"><?php echo esc_textarea($faq['answer']); ?></textarea>
                </p>
                <button type="button" class="button remove-faq-row">Remove FAQ</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="add-faq-row">Add FAQ</button>

    <script>
        jQuery(document).ready(function ($) {
            $('#add-faq-row').on('click', function () {
                var count = $('.faq-item').length;
                var html = '<div class="faq-item" style="margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">' +
                    '<p><label>Question:</label><br><input type="text" name="election_faqs[' + count + '][question]" value="" style="width: 100%;"></p>' +
                    '<p><label>Answer:</label><br><textarea name="election_faqs[' + count + '][answer]" style="width: 100%;" rows="3"></textarea></p>' +
                    '<button type="button" class="button remove-faq-row">Remove FAQ</button></div>';
                $('#election-faq-wrapper').append(html);
            });

            $(document).on('click', '.remove-faq-row', function () {
                if ($('.faq-item').length > 1) { // Keep at least one row or handle empty logic
                    $(this).closest('.faq-item').remove();
                } else {
                    // Define behavior for last item removal if desired, currently clearing values
                    $(this).closest('.faq-item').find('input, textarea').val('');
                }
            });
        });
    </script>
    <?php
}

function election_faq_save_meta($post_id)
{
    if (!isset($_POST['election_faq_nonce']) || !wp_verify_nonce($_POST['election_faq_nonce'], 'election_faq_nonce'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    if (isset($_POST['election_faqs'])) {
        $clean_faqs = array();
        foreach ($_POST['election_faqs'] as $faq) {
            if (!empty($faq['question']) && !empty($faq['answer'])) {
                $clean_faqs[] = array(
                    'question' => sanitize_text_field($faq['question']),
                    'answer' => wp_kses_post($faq['answer'])
                );
            }
        }
        update_post_meta($post_id, '_election_post_faqs', $clean_faqs);
    } else {
        delete_post_meta($post_id, '_election_post_faqs');
    }
}
add_action('save_post', 'election_faq_save_meta');

// Customizer Settings
function election_awareness_customize_register($wp_customize)
{
    // Existing Election Settings
    $wp_customize->add_section('election_settings', array(
        'title' => __('Election Settings', 'election-awareness'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('election_date', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('election_date', array(
        'label' => __('Election Date (YYYY-MM-DD)', 'election-awareness'),
        'section' => 'election_settings',
        'type' => 'date',
    ));

    // Logo Width Settings (Site Identity)
    $wp_customize->add_setting('logo_width_desktop', array(
        'default' => '200',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('logo_width_desktop', array(
        'label' => __('Logo Width - Desktop (px)', 'election-awareness'),
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => array('min' => 50, 'max' => 500, 'step' => 1),
    ));

    $wp_customize->add_setting('logo_width_tablet', array(
        'default' => '150',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('logo_width_tablet', array(
        'label' => __('Logo Width - Tablet (px)', 'election-awareness'),
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => array('min' => 50, 'max' => 400, 'step' => 1),
    ));

    $wp_customize->add_setting('logo_width_mobile', array(
        'default' => '120',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('logo_width_mobile', array(
        'label' => __('Logo Width - Mobile (px)', 'election-awareness'),
        'section' => 'title_tagline',
        'type' => 'range',
        'input_attrs' => array('min' => 50, 'max' => 300, 'step' => 1),
    ));

    // Retina Logo
    $wp_customize->add_setting('retina_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'retina_logo', array(
        'label' => __('Retina Logo (@2x)', 'election-awareness'),
        'section' => 'title_tagline',
    )));

    // Theme Colors Section
    $wp_customize->add_section('theme_colors', array(
        'title' => __('Theme Colors', 'election-awareness'),
        'priority' => 31,
    ));

    $colors = array(
        'color_deep_void' => array(
            'label' => __('Main Background', 'election-awareness'),
            'default' => '#080B10',
        ),
        'color_midnight_blue' => array(
            'label' => __('Secondary Background', 'election-awareness'),
            'default' => '#0A1019',
        ),
        'color_steel_blue' => array(
            'label' => __('Tertiary Background', 'election-awareness'),
            'default' => '#161F2E',
        ),
        'color_electric_yellow' => array(
            'label' => __('Primary Accent', 'election-awareness'),
            'default' => '#FFFF00',
        ),
        'color_text_white' => array(
            'label' => __('Heading Text', 'election-awareness'),
            'default' => '#FFFFFF',
        ),
        'color_text_grey' => array(
            'label' => __('Body Text', 'election-awareness'),
            'default' => '#E2E8F0',
        ),
        'color_text_muted' => array(
            'label' => __('Body Text Muted', 'election-awareness'),
            'default' => '#94A3B8',
        ),
    );

    foreach ($colors as $id => $color) {
        $wp_customize->add_setting($id, array(
            'default' => $color['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, array(
            'label' => $color['label'],
            'section' => 'theme_colors',
        )));
    }
}
add_action('customize_register', 'election_awareness_customize_register');

/**
 * Output Dynamic Logo CSS
 */
function election_awareness_logo_css()
{
    $desktop = get_theme_mod('logo_width_desktop', '200');
    $tablet = get_theme_mod('logo_width_tablet', '150');
    $mobile = get_theme_mod('logo_width_mobile', '120');

    echo '<style type="text/css">';
    echo '.custom-logo-link img { width: ' . esc_attr($desktop) . 'px !important; height: auto !important; }';
    echo '@media (max-width: 992px) { .custom-logo-link img { width: ' . esc_attr($tablet) . 'px !important; } }';
    echo '@media (max-width: 768px) { .custom-logo-link img { width: ' . esc_attr($mobile) . 'px !important; } }';
    echo '</style>';
}
add_action('wp_head', 'election_awareness_logo_css');

/**
 * Force Single Post Responsive CSS (Bypass Cache)
 */
function election_force_responsive_css()
{
    if (is_single()) {
        ?>
        <style type="text/css">
            /* --- Force Single Post Responsiveness --- */

            /* Global Containment */
            html,
            body {
                overflow-x: hidden !important;
                width: 100% !important;
                position: relative !important;
            }

            .site-main {
                overflow-x: hidden !important;
            }

            /* Container sizing for all mobile/tablet */
            @media (max-width: 1024px) {
                .container {
                    width: 100% !important;
                    max-width: 100% !important;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                    box-sizing: border-box !important;
                }

                .post-layout {
                    display: block !important;
                    /* Stack columns */
                    margin-top: 0 !important;
                }

                .post-sidebar {
                    display: none !important;
                }

                .post-content-body {
                    width: 100% !important;
                    padding: 40px 0 !important;
                }
            }

            /* Post Hero */
            @media (max-width: 1024px) {
                .post-hero {
                    height: 60vh !important;
                    min-height: 400px !important;
                }

                .post-title {
                    font-size: 2.8rem !important;
                    line-height: 1.2 !important;
                }
            }

            @media (max-width: 550px) {
                .post-title {
                    font-size: 2rem !important;
                }

                .post-meta-bar {
                    font-size: 0.9rem !important;
                    gap: 10px !important;
                }
            }

            /* Author Box */
            @media (max-width: 1024px) {
                .author-box {
                    flex-direction: column !important;
                    padding: 40px 24px !important;
                    text-align: center !important;
                    gap: 30px !important;
                    margin: 40px 0 !important;
                    display: flex !important;
                    align-items: center !important;
                }

                .author-box-content {
                    width: 100% !important;
                }

                .author-box-avatar img {
                    width: 100px !important;
                    height: 100px !important;
                    margin: 0 auto !important;
                }
            }

            /* Read Next (Related Posts) */
            @media (max-width: 1024px) {
                .related-posts-grid {
                    display: grid !important;
                    grid-template-columns: repeat(2, 1fr) !important;
                    gap: 30px !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: visible !important;
                }

                .related-mini-card {
                    width: 100% !important;
                    padding: 0 !important;
                    scroll-snap-align: none !important;
                }
            }

            @media (max-width: 767px) {
                .related-posts-grid {
                    grid-template-columns: 1fr !important;
                    gap: 20px !important;
                }
            }

            /* Comments Area - Premium Redesign */
            @media (max-width: 1024px) {
                .comments-area-wrapper {
                    margin-top: 80px !important;
                    background: rgba(255, 255, 255, 0.02) !important;
                    padding: 60px 40px !important;
                    border-radius: 20px !important;
                    border: 1px solid rgba(255, 255, 255, 0.05) !important;
                }

                #reply-title {
                    font-size: 2.2rem !important;
                    background: linear-gradient(90deg, #fff, var(--color-text-grey)) !important;
                    -webkit-background-clip: text !important;
                    -webkit-text-fill-color: transparent !important;
                    margin-bottom: 40px !important;
                }

                .comment-form {
                    display: grid !important;
                    grid-template-columns: repeat(2, 1fr) !important;
                    /* 2 columns for better room */
                    gap: 20px !important;
                }

                .comment-form-author,
                .comment-form-email {
                    grid-column: span 1 !important;
                }

                .comment-form-url,
                .comment-form-comment,
                .form-submit,
                .comment-notes,
                .comment-form-cookies-consent {
                    grid-column: 1 / -1 !important;
                }

                .comment-form-cookies-consent {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: flex-start !important;
                    gap: 10px !important;
                    margin: 10px 0 !important;
                    /* Reduced from 15px */
                    justify-content: flex-start !important;
                    grid-column: 1 / -1 !important;
                    width: 100% !important;
                }

                .comment-form-cookies-consent input {
                    width: 18px !important;
                    height: 18px !important;
                    margin: 0 !important;
                    margin-top: 4px !important;
                    flex-shrink: 0 !important;
                    cursor: pointer !important;
                    accent-color: var(--color-electric-yellow) !important;
                }

                .comment-form-cookies-consent label {
                    display: inline !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    font-size: 14px !important;
                    color: rgba(255, 255, 255, 0.7) !important;
                    text-transform: none !important;
                    letter-spacing: normal !important;
                    cursor: pointer !important;
                    line-height: 1.4 !important;
                    text-align: left !important;
                }

                .comment-respond label {
                    color: rgba(255, 255, 255, 0.6) !important;
                    font-size: 13px !important;
                    letter-spacing: 1px !important;
                    text-transform: uppercase !important;
                    margin-bottom: 8px !important;
                    /* Reduced from 12px */
                }

                .comment-respond input,
                .comment-respond textarea {
                    background: rgba(255, 255, 255, 0.03) !important;
                    border: 1px solid rgba(255, 255, 255, 0.1) !important;
                    border-radius: 12px !important;
                    padding: 18px 20px !important;
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
                    font-size: 16px !important;
                }

                .comment-respond textarea {
                    height: 180px !important;
                    resize: vertical !important;
                }

                .comment-respond input:focus,
                .comment-respond textarea:focus {
                    background: rgba(255, 255, 255, 0.05) !important;
                    border-color: var(--color-electric-yellow) !important;
                    box-shadow: 0 0 20px rgba(255, 255, 0, 0.1) !important;
                    transform: translateY(-2px) !important;
                }

                .submit-comment-btn {
                    background: var(--color-electric-yellow) !important;
                    color: #000 !important;
                    font-size: 16px !important;
                    padding: 16px 40px !important;
                    border-radius: 50px !important;
                    width: auto !important;
                    float: right !important;
                    letter-spacing: 0.5px !important;
                    font-family: var(--font-heading) !important;
                    font-weight: 700 !important;
                    text-transform: uppercase !important;
                    transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1) !important;
                    cursor: pointer !important;
                    border: none !important;
                }

                .submit-comment-btn:hover {
                    background-color: #e6e600 !important;
                    box-shadow: 0 8px 25px rgba(255, 255, 0, 0.2) !important;
                    transform: translateY(-3px) !important;
                }
            }

            @media (max-width: 767px) {
                .comments-area-wrapper {
                    padding: 40px 20px !important;
                }

                .comment-form {
                    grid-template-columns: 1fr !important;
                }

                .comment-form-author,
                .comment-form-email {
                    grid-column: 1 / -1 !important;
                }

                .submit-comment-btn {
                    width: 100% !important;
                    float: none !important;
                }
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'election_force_responsive_css', 100);

/**
 * Check the "Save my name, email..." checkbox by default
 */
add_filter('comment_form_default_fields', function ($fields) {
    if (isset($fields['cookies'])) {
        $fields['cookies'] = str_replace('type="checkbox"', 'type="checkbox" checked="checked"', $fields['cookies']);
    }
    return $fields;
});

// Helper to get days left
function election_get_days_left()
{
    $election_date = get_theme_mod('election_date', '');
    if (empty($election_date))
        return 0;

    $now = current_time('timestamp');
    $then = strtotime($election_date);

    if ($then <= $now)
        return 0;

    $diff = $then - $now;
    return ceil($diff / (60 * 60 * 24));
}

/**
 * Calculate Estimated Reading Time
 */
function election_calculate_reading_time($post_id)
{
    $post = get_post($post_id);
    $content = strip_shortcodes($post->post_content);
    $content = strip_tags($content);
    $word_count = str_word_count($content);
    $reading_time = ceil($word_count / 200); // 200 words per minute

    return $reading_time;
}

/**
 * Custom Breadcrumbs with Yellow Slashes
 */
function election_the_breadcrumbs()
{
    echo '<nav class="breadcrumb">';
    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
    echo '<span class="sep">/</span>';

    if (is_singular('party')) {
        echo '<a href="' . esc_url(get_post_type_archive_link('party')) . '">Political Parties</a>';
        echo '<span class="sep">/</span>';
        the_title();
    } elseif (is_singular('post')) {
        echo '<a href="' . esc_url(get_post_type_archive_link('post')) . '">Blogs</a>';
        echo '<span class="sep">/</span>';
        the_category(' ');
        echo '<span class="sep">/</span>';
        the_title();
    } elseif (is_category()) {
        echo '<a href="' . esc_url(get_post_type_archive_link('post')) . '">Blogs</a>';
        echo '<span class="sep">/</span>';
        single_cat_title();
    } elseif (is_page()) {
        the_title();
    } else {
        // Fallback for archives etc
        echo '<a href="' . esc_url(get_post_type_archive_link('post')) . '">Blogs</a>';
    }

    echo '</nav>';
}

/**
 * Restrict search to blog posts only
 */
function election_search_filter($query)
{
    if ($query->is_search && !is_admin()) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts', 'election_search_filter');

// Force Contact Template for "Contact" page (regardless of slug)
function election_force_contact_template($template)
{
    if (is_page()) {
        $page = get_queried_object();
        if ($page->post_title === 'Contact' || $page->post_name === 'contact') {
            $new_template = locate_template(array('template-contact.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
    }

    // Force Terms Template for "Terms & Conditions" page (regardless of slug)
    if (is_page()) {
        $page = get_queried_object();
        if ($page->post_title === 'Terms & Conditions' || $page->post_name === 'terms-conditions') {
            $new_template = locate_template(array('template-terms.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
    }

    // Force Privacy Template for "Privacy Policy" page (regardless of slug)
    if (is_page()) {
        $page = get_queried_object();
        if ($page->post_title === 'Privacy Policy' || $page->post_name === 'privacy-policy') {
            $new_template = locate_template(array('template-privacy.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
    }

    // Force Products Template for "Products" page
    if (is_page()) {
        $page = get_queried_object();
        if ($page->post_title === 'Products' || $page->post_name === 'products') {
            $new_template = locate_template(array('template-products.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
    }

    return $template;
}
add_filter('template_include', 'election_force_contact_template');

/**
 * Theme Builder Dashboard (React Admin)
 */

// 1. Register Setting
function election_register_settings()
{
    register_setting('election_theme_options_group', 'election_theme_hero_count', array(
        'type' => 'integer',
        'default' => 3,
        'show_in_rest' => true, // Important for React to fetch/save
        'sanitize_callback' => 'absint',
    ));

    register_setting('election_theme_options_group', 'election_theme_ticker_count', array(
        'type' => 'integer',
        'default' => 5,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ));

    // Latest Stories Grid
    register_setting('election_theme_options_group', 'election_theme_grid_heading', array(
        'type' => 'string',
        'default' => 'Latest Stories',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_grid_cta_text', array(
        'type' => 'string',
        'default' => 'View All',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_grid_cta_url', array(
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
    ));

    register_setting('election_theme_options_group', 'election_theme_grid_count', array(
        'type' => 'integer',
        'default' => 5,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ));

    // CTA Section
    register_setting('election_theme_options_group', 'election_theme_cta_heading', array(
        'type' => 'string',
        'default' => 'Join the Awareness Movement',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_cta_subheading', array(
        'type' => 'string',
        'default' => 'Stay informed with the latest election updates and deep dives.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_cta_text', array(
        'type' => 'string',
        'default' => 'Get Involved',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_cta_url', array(
        'type' => 'string',
        'default' => '/contact',
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
    ));

    register_setting('election_theme_options_group', 'election_theme_social_links', array(
        'type' => 'string',
        'default' => '[]',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // All News (Blog) Page Settings
    register_setting('election_theme_options_group', 'election_theme_all_news_heading', array(
        'type' => 'string',
        'default' => 'Latest News',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_all_news_count', array(
        'type' => 'integer',
        'default' => 9,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ));

    register_setting('election_theme_options_group', 'election_theme_archive_posts_count', array(
        'type' => 'integer',
        'default' => 9,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ));

    // Political Parties Page Settings
    register_setting('election_theme_options_group', 'election_theme_parties_heading', array(
        'type' => 'string',
        'default' => 'All Political Parties',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_parties_subheading', array(
        'type' => 'string',
        'default' => 'A comprehensive directory of active political groups and their manifestos.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_parties_count', array(
        'type' => 'integer',
        'default' => 10,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ));

    // About Page Settings
    // Mission Section
    register_setting('election_theme_options_group', 'election_theme_about_mission_label', array(
        'type' => 'string',
        'default' => 'Purpose',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_about_mission_title', array(
        'type' => 'string',
        'default' => 'Our Mission',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_about_mission_content', array(
        'type' => 'string',
        'default' => 'At Election Awareness, we believe that informed voters are the bedrock of democracy. Our platform provides unbiased data, real-time analytics, and deep investigative journalism to ensure every citizen has the tools they need to make the right choice.',
        'show_in_rest' => true,
        'sanitize_callback' => 'wp_kses_post', // Allow HTML in content
    ));

    register_setting('election_theme_options_group', 'election_theme_about_mission_image', array(
        'type' => 'string',
        'default' => 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?auto=format&fit=crop&q=80&w=1200',
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
    ));

    // Vision Section
    register_setting('election_theme_options_group', 'election_theme_about_vision_label', array(
        'type' => 'string',
        'default' => 'Future',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_about_vision_title', array(
        'type' => 'string',
        'default' => 'Our Vision',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_about_vision_content', array(
        'type' => 'string',
        'default' => 'Our vision is a world where every election is conducted with absolute clarity and every vote is cast with confidence. We aspire to be the global standard for electoral intelligence, bridging the gap between complex data and public understanding.',
        'show_in_rest' => true,
        'sanitize_callback' => 'wp_kses_post',
    ));

    register_setting('election_theme_options_group', 'election_theme_about_vision_image', array(
        'type' => 'string',
        'default' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200',
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
    ));

    // About Page Header
    register_setting('election_theme_options_group', 'election_theme_about_header_title', array(
        'type' => 'string',
        'default' => 'We are the <span class="highlight">voice</span> of clear elections.',
        'show_in_rest' => true,
        'sanitize_callback' => 'wp_kses_post', // Allow HTML for span tags
    ));

    // Terms & Conditions Settings
    register_setting('election_theme_options_group', 'election_theme_terms_title', array(
        'type' => 'string',
        'default' => 'Terms of Service',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_terms_sections', array(
        'type' => 'string',
        'default' => '[]',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_terms_updated_date', array(
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Privacy Policy Settings
    register_setting('election_theme_options_group', 'election_theme_privacy_title', array(
        'type' => 'string',
        'default' => 'Privacy Policy',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_privacy_subtitle', array(
        'type' => 'string',
        'default' => 'We value your privacy as much as your vote.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_privacy_sections', array(
        'type' => 'string',
        'default' => '[]',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_privacy_updated_date', array(
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Products Page Settings
    register_setting('election_theme_options_group', 'election_theme_products_heading', array(
        'type' => 'string',
        'default' => 'Awareness Gear',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_products_subtitle', array(
        'type' => 'string',
        'default' => 'Support the movement with our official merchandise and premium reports.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));
}

function election_awareness_customize_footer($wp_customize)
{
    // Footer Section
    $wp_customize->add_section('election_footer_settings', array(
        'title' => __('Footer Settings', 'election-awareness'),
        'priority' => 120,
    ));

    // Mission Statement
    $wp_customize->add_setting('footer_mission_text', array(
        'default' => 'Empowering voters with clear, unbiased insights into the political landscape. Modernizing the way you consume news.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('footer_mission_text', array(
        'label' => __('Mission Statement', 'election-awareness'),
        'section' => 'election_footer_settings',
        'type' => 'textarea',
    ));

    // Newsletter Section
    $wp_customize->add_setting('footer_newsletter_heading', array(
        'default' => 'Newsletter',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('footer_newsletter_heading', array(
        'label' => __('Newsletter Heading', 'election-awareness'),
        'section' => 'election_footer_settings',
        'type' => 'text',
    ));

    $wp_customize->add_setting('footer_newsletter_text', array(
        'default' => 'Get the latest updates directly in your inbox.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('footer_newsletter_text', array(
        'label' => __('Newsletter Text', 'election-awareness'),
        'section' => 'election_footer_settings',
        'type' => 'textarea',
    ));

    // Copyright
    $wp_customize->add_setting('footer_copyright_text', array(
        'default' => '&copy; ' . date('Y') . ' Election Awareness. All rights reserved.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('footer_copyright_text', array(
        'label' => __('Copyright Text', 'election-awareness'),
        'section' => 'election_footer_settings',
        'type' => 'text',
        'description' => 'Use %year% to auto-insert the current year.',
    ));
}
add_action('customize_register', 'election_awareness_customize_footer');
add_action('init', 'election_register_settings');

/**
 * Handle All News and Archive Post Count via pre_get_posts
 */
function election_all_news_posts_filter($query)
{
    if (!is_admin() && $query->is_main_query()) {
        if (is_home()) {
            $count = get_option('election_theme_all_news_count', 9);
            $query->set('posts_per_page', $count);
        } elseif (is_post_type_archive('party')) {
            $count = get_option('election_theme_parties_count', 10);
            $query->set('posts_per_page', $count);
        } elseif (is_archive()) {
            $count = get_option('election_theme_archive_posts_count', 9);
            $query->set('posts_per_page', $count);
        }
    }
    return $query;
}
add_action('pre_get_posts', 'election_all_news_posts_filter');

// 2. Add Admin Menu Page
function election_add_admin_menu()
{
    add_menu_page(
        __('Theme Options', 'election-awareness'),
        __('Theme Builder', 'election-awareness'),
        'manage_options',
        'election-theme-builder',
        'election_render_admin_page',
        'dashicons-layout',
        60
    );
}
add_action('admin_menu', 'election_add_admin_menu');

// 3. Render Admin Page HTML
function election_render_admin_page()
{
    echo '<div id="election-theme-dashboard"></div>';
}

// 4. Enqueue Admin Scripts
function election_enqueue_admin_scripts($hook)
{
    if ('toplevel_page_election-theme-builder' !== $hook) {
        return;
    }

    // Enqueue WordPress Media Uploader
    wp_enqueue_media();

    $asset_file = include(get_template_directory() . '/build/admin.asset.php');

    wp_enqueue_script(
        'election-admin-app',
        get_template_directory_uri() . '/build/admin.js',
        $asset_file['dependencies'], // This should include wp-element, wp-components, wp-api-fetch
        $asset_file['version'],
        true
    );

    // Enqueue Admin Styles
    wp_enqueue_style(
        'election-admin-style',
        get_template_directory_uri() . '/build/admin.css',
        array('wp-components'),
        $asset_file['version']
    );

    // Enqueue Fonts
    wp_enqueue_style('election-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;700&family=Public+Sans:wght@400;600&family=Space+Grotesk:wght@500;700&display=swap', array(), null);
}
add_action('admin_enqueue_scripts', 'election_enqueue_admin_scripts');

/**
 * REST API: Products Endpoint
 */
function election_register_products_rest_route() {
    register_rest_route('election/v1', '/products', array(
        'methods'  => 'GET',
        'callback' => 'election_get_products_rest_api',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'election_register_products_rest_route');

function election_get_products_rest_api($request) {
    $products_query = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 100,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    $products = array();
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $products[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'link'      => get_permalink(),
                'image'     => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: '',
                'price'     => floatval(get_post_meta(get_the_ID(), '_product_price', true)),
                'buyUrl'    => get_post_meta(get_the_ID(), '_product_buy_url', true) ?: '#',
                'shortDesc' => get_post_meta(get_the_ID(), '_product_short_desc', true) ?: get_the_excerpt(),
                'date'      => get_the_date('Y-m-d H:i:s'),
            );
        }
    }
    wp_reset_postdata();

    return rest_ensure_response($products);
}

/**
 * GitHub Theme Updater
 */
require_once get_template_directory() . '/inc/updater.php';
