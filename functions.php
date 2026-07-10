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
 * SEO: Add Open Graph and Twitter Card Meta Tags
 */
function election_add_social_meta_tags() {
    // Default values
    $site_name   = get_bloginfo('name');
    $og_type     = 'website';
    $og_title    = get_bloginfo('name');
    $og_desc     = get_bloginfo('description');
    $og_url      = home_url('/');
    $og_image    = '';
    $og_image_alt = get_bloginfo('name');

    // Handle Custom Logo as fallback image
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo_data) {
            $og_image = $logo_data[0];
            $alt_text = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);
            if ($alt_text) $og_image_alt = $alt_text;
        }
    }

    if (is_single() || is_page()) {
        global $post;
        $og_type  = 'article';
        $og_title = get_the_title();
        $og_url   = get_permalink();
        
        // Description from excerpt or content
        if (has_excerpt($post->ID)) {
            $og_desc = wp_strip_all_tags(get_the_excerpt());
        } else {
            $og_desc = wp_trim_words(wp_strip_all_tags($post->post_content), 30);
        }

        // Featured Image
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            $thumbnail_data = wp_get_attachment_image_src($thumbnail_id, 'full');
            if ($thumbnail_data) {
                $og_image = $thumbnail_data[0];
                $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                if ($alt_text) {
                    $og_image_alt = $alt_text;
                } else {
                    $og_image_alt = $og_title;
                }
            }
        }
    }
    
    // Fallback if no description
    if (empty($og_desc)) {
        $og_desc = get_bloginfo('description');
    }

    // Output tags
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>">
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og_desc); ?>">
    <meta property="og:url" content="<?php echo esc_url($og_url); ?>">
    <?php if ($og_image): ?>
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:secure_url" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo esc_attr($og_image_alt); ?>">
    <?php
        // Facebook recommends specifying image type for faster rendering
        $image_type = wp_check_filetype($og_image);
        if ($image_type['type']) {
            echo '<meta property="og:image:type" content="' . esc_attr($image_type['type']) . '">' . "\n";
        }
    ?>
    <?php endif; ?>

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($og_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($og_desc); ?>">
    <?php if ($og_image): ?>
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <meta name="twitter:image:alt" content="<?php echo esc_attr($og_image_alt); ?>">
    <?php endif; ?>
    <?php
}
add_action('wp_head', 'election_add_social_meta_tags', 5);

/**
 * SEO: Explicitly allow Facebook crawler in robots.txt to fix 403 block
 */
function election_allow_facebook_crawler($output, $public) {
    $output .= "\nUser-agent: facebookexternalhit\nAllow: /\n";
    return $output;
}
add_filter('robots_txt', 'election_allow_facebook_crawler', 10, 2);

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
    wp_enqueue_style('election-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;700&family=Public+Sans:wght@400;600&family=Space+Grotesk:wght@500;700&family=Hind+Siliguri:wght@400;500;600;700&display=swap', array(), null);

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

    // Enqueue Auth Modal Script
    wp_enqueue_script('election-auth-modal', get_template_directory_uri() . '/assets/js/auth-modal.js', array(), '1.0', true);
    wp_localize_script('election-auth-modal', 'electionAuth', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
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
    add_theme_support('post-thumbnails', array('post', 'page', 'party', 'product', 'politician'));
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

    // Register Feed Post CPT
    register_post_type('feed_post', array(
        'labels' => array(
            'name' => __('Feed Posts', 'election-awareness'),
            'singular_name' => __('Feed Post', 'election-awareness'),
            'add_new' => __('Add New Feed Post', 'election-awareness'),
            'add_new_item' => __('Add New Feed Post', 'election-awareness'),
            'edit_item' => __('Edit Feed Post', 'election-awareness'),
            'all_items' => __('All Feed Posts', 'election-awareness'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'author'),
        'menu_icon' => 'dashicons-format-status',
        'capabilities' => array(
            'create_posts' => 'manage_options',
            'edit_posts' => 'manage_options',
            'edit_others_posts' => 'manage_options',
            'publish_posts' => 'manage_options',
            'read_private_posts' => 'manage_options',
        ),
    ));

    // Register Politician Profile CPT
    register_post_type('politician', array(
        'labels' => array(
            'name' => __('Politicians', 'election-awareness'),
            'singular_name' => __('Politician', 'election-awareness'),
            'add_new' => __('Add New Politician', 'election-awareness'),
            'add_new_item' => __('Add New Politician', 'election-awareness'),
            'edit_item' => __('Edit Politician', 'election-awareness'),
            'all_items' => __('All Politicians', 'election-awareness'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => 'politicians',
        'rewrite' => array('slug' => 'politicians'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-businessperson',
    ));

    // Flush rewrite rules to ensure new CPT slugs work immediately
}
add_action('init', 'election_awareness_cpt_init');

/**
 * Flush rewrite rules on theme activation
 */
function election_theme_activation() {
    election_awareness_cpt_init();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'election_theme_activation');

/**
 * Add Security Headers
 */
function election_add_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'election_add_security_headers');

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
 * Politician Meta Boxes
 */
function politician_add_meta_boxes()
{
    add_meta_box('politician_details', 'Politician Details', 'politician_details_callback', 'politician', 'normal', 'high');
}
add_action('add_meta_boxes', 'politician_add_meta_boxes');

function politician_details_callback($post)
{
    $title = get_post_meta($post->ID, '_politician_title', true);
    $selected_party = get_post_meta($post->ID, '_politician_party', true);
    $constituency = get_post_meta($post->ID, '_politician_constituency', true);
    $focus = get_post_meta($post->ID, '_politician_focus', true);

    wp_nonce_field('politician_details_nonce', 'politician_details_nonce');
    ?>
    <p>
        <label>Representative Title (e.g. MP, Representative):</label><br>
        <input type="text" name="politician_title" value="<?php echo esc_attr($title); ?>" style="width:100%;">
    </p>
    <p>
        <label>Political Party:</label><br>
        <select name="politician_party" style="width:100%;">
            <option value="">Select Party</option>
            <option value="Independent" <?php selected($selected_party, 'Independent'); ?>>Independent</option>
            <?php
            $parties_query = new WP_Query(array(
                'post_type' => 'party',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ));
            if ($parties_query->have_posts()) {
                while ($parties_query->have_posts()) {
                    $parties_query->the_post();
                    $party_id = get_the_ID();
                    $party_title = get_the_title();
                    $is_selected = ($selected_party == $party_id || $selected_party == $party_title);
                    echo '<option value="' . esc_attr($party_id) . '" ' . selected($is_selected, true, false) . '>' . esc_html($party_title) . '</option>';
                }
                wp_reset_postdata();
            }
            ?>
        </select>
    </p>
    <p>
        <label>Constituency:</label><br>
        <input type="text" name="politician_constituency" value="<?php echo esc_attr($constituency); ?>" style="width:100%;">
    </p>
    <p>
        <label>Focus Area:</label><br>
        <input type="text" name="politician_focus" value="<?php echo esc_attr($focus); ?>" style="width:100%;">
    </p>
    <?php
}

function politician_save_meta($post_id)
{
    if (!isset($_POST['politician_details_nonce']) || !wp_verify_nonce($_POST['politician_details_nonce'], 'politician_details_nonce'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;
    if (get_post_type($post_id) !== 'politician')
        return;

    if (isset($_POST['politician_title']))
        update_post_meta($post_id, '_politician_title', sanitize_text_field($_POST['politician_title']));
    if (isset($_POST['politician_party']))
        update_post_meta($post_id, '_politician_party', sanitize_text_field($_POST['politician_party']));
    if (isset($_POST['politician_constituency']))
        update_post_meta($post_id, '_politician_constituency', sanitize_text_field($_POST['politician_constituency']));
    if (isset($_POST['politician_focus']))
        update_post_meta($post_id, '_politician_focus', sanitize_text_field($_POST['politician_focus']));
}
add_action('save_post', 'politician_save_meta');


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
    $sku = get_post_meta($post->ID, '_product_sku', true);
    $availability = get_post_meta($post->ID, '_product_availability', true) ?: 'instock';
    $short_desc = get_post_meta($post->ID, '_product_short_desc', true);
    $contact_phone = get_post_meta($post->ID, '_product_contact_phone', true);

    wp_nonce_field('product_details_nonce', 'product_details_nonce');
    ?>
    <p>
        <label>Price (৳):</label><br>
        <input type="number" step="1" min="0" name="product_price" value="<?php echo esc_attr($price); ?>" style="width:100%;" placeholder="e.g. 1500">
    </p>
    <p>
        <label>SKU:</label><br>
        <input type="text" name="product_sku" value="<?php echo esc_attr($sku); ?>" style="width:100%;" placeholder="e.g. TSHIRT-001">
    </p>
    <p>
        <label>Availability:</label><br>
        <select name="product_availability" style="width:100%;">
            <option value="instock" <?php selected($availability, 'instock'); ?>>In Stock</option>
            <option value="outofstock" <?php selected($availability, 'outofstock'); ?>>Out of Stock</option>
            <option value="preorder" <?php selected($availability, 'preorder'); ?>>Pre-order</option>
        </select>
    </p>
    <p>
        <label>Short Description (shown on card, max 2 lines):</label><br>
        <textarea name="product_short_desc" style="width:100%;" rows="2"><?php echo esc_textarea($short_desc); ?></textarea>
    </p>
    <p>
        <label>WhatsApp Number (include country code):</label><br>
        <input type="text" name="product_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" style="width:100%;" placeholder="88017...">
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
    if (isset($_POST['product_sku']))
        update_post_meta($post_id, '_product_sku', sanitize_text_field($_POST['product_sku']));
    if (isset($_POST['product_availability']))
        update_post_meta($post_id, '_product_availability', sanitize_text_field($_POST['product_availability']));
    if (isset($_POST['product_short_desc']))
        update_post_meta($post_id, '_product_short_desc', sanitize_textarea_field($_POST['product_short_desc']));
    if (isset($_POST['product_contact_phone']))
        update_post_meta($post_id, '_product_contact_phone', sanitize_text_field($_POST['product_contact_phone']));
}
add_action('save_post', 'product_save_meta');

/**
 * Feed Post Meta Boxes
 */
function feed_post_add_meta_boxes()
{
    add_meta_box('feed_post_details', 'Feed Post Details', 'feed_post_details_callback', 'feed_post', 'normal', 'high');
}
add_action('add_meta_boxes', 'feed_post_add_meta_boxes');

function feed_post_enqueue_admin_scripts($hook) {
    global $typenow;
    if ($typenow == 'feed_post' && in_array($hook, array('post.php', 'post-new.php'))) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'feed_post_enqueue_admin_scripts');

function feed_post_details_callback($post)
{
    $type = get_post_meta($post->ID, '_feed_type', true) ?: 'text';
    $media_ids = get_post_meta($post->ID, '_feed_media_ids', true) ?: '';

    wp_nonce_field('feed_post_details_nonce', 'feed_post_details_nonce');
    ?>
    <p>
        <label>Post Type:</label><br>
        <select name="feed_type" style="width:100%;">
            <option value="text" <?php selected($type, 'text'); ?>>Text Only</option>
            <option value="image" <?php selected($type, 'image'); ?>>Image(s)</option>
            <option value="video" <?php selected($type, 'video'); ?>>Video(s)</option>
            <option value="gallery" <?php selected($type, 'gallery'); ?>>Mixed (Images & Videos)</option>
        </select>
    </p>

    <div style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 15px;">
        <label><strong>Attached Media (Upload or Select multiple images/videos):</strong></label>
        <div id="feed_media_preview" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; margin-bottom:10px;">
            <?php
            if (!empty($media_ids)) {
                $ids = explode(',', $media_ids);
                foreach ($ids as $att_id) {
                    if (wp_attachment_is_image($att_id)) {
                        echo '<div style="position:relative;" data-id="'.esc_attr($att_id).'"><img src="'.wp_get_attachment_thumb_url($att_id).'" style="width:100px; height:100px; object-fit:cover;"></div>';
                    } else {
                        echo '<div style="position:relative; width:100px; height:100px; background:#ddd; display:flex; align-items:center; justify-content:center; text-align:center;" data-id="'.esc_attr($att_id).'">Video<br>('.esc_html($att_id).')</div>';
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" name="feed_media_ids" id="feed_media_ids" value="<?php echo esc_attr($media_ids); ?>">
        <button type="button" class="button" id="feed_media_upload_btn">Select Images & Videos</button>
        <button type="button" class="button" id="feed_media_clear_btn" style="color:red;">Clear Media</button>
    </div>

    <script>
    jQuery(document).ready(function($){
        var mediaUploader;
        $('#feed_media_upload_btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Select Images or Videos',
                button: { text: 'Select Media' },
                multiple: true
            });
            mediaUploader.on('select', function() {
                var selection = mediaUploader.state().get('selection');
                var ids = [];
                $('#feed_media_preview').empty();
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    if (attachment.type === 'image') {
                        var thumbUrl = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
                        $('#feed_media_preview').append('<div style="position:relative;" data-id="'+attachment.id+'"><img src="'+thumbUrl+'" style="width:100px; height:100px; object-fit:cover;"></div>');
                    } else if (attachment.type === 'video') {
                        $('#feed_media_preview').append('<div style="position:relative; width:100px; height:100px; background:#ddd; display:flex; align-items:center; justify-content:center; text-align:center;" data-id="'+attachment.id+'">Video<br>('+attachment.id+')</div>');
                    }
                });
                $('#feed_media_ids').val(ids.join(','));
            });
            mediaUploader.open();
        });
        $('#feed_media_clear_btn').click(function(e){
            e.preventDefault();
            $('#feed_media_preview').empty();
            $('#feed_media_ids').val('');
        });
    });
    </script>
    <?php
}

function feed_post_save_meta($post_id)
{
    if (!isset($_POST['feed_post_details_nonce']) || !wp_verify_nonce($_POST['feed_post_details_nonce'], 'feed_post_details_nonce'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;
    if (get_post_type($post_id) !== 'feed_post')
        return;

    if (isset($_POST['feed_type']))
        update_post_meta($post_id, '_feed_type', sanitize_text_field($_POST['feed_type']));
    if (isset($_POST['feed_media_ids']))
        update_post_meta($post_id, '_feed_media_ids', sanitize_text_field($_POST['feed_media_ids']));
}
add_action('save_post', 'feed_post_save_meta');


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

    // Mobile Logo
    $wp_customize->add_setting('mobile_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mobile_logo', array(
        'label' => __('Mobile Logo (Optional)', 'election-awareness'),
        'description' => __('Alternative logo for mobile devices.', 'election-awareness'),
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
                overflow-x: clip !important;
                width: 100% !important;
                position: relative !important;
            }

            .site-main {
                overflow-x: clip !important;
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
    } elseif (is_singular('politician')) {
        echo '<a href="' . esc_url(get_post_type_archive_link('politician')) . '">Politician Profiles</a>';
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
        'sanitize_callback' => 'election_sanitize_json_settings',
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

    // Politicians Page Settings
    register_setting('election_theme_options_group', 'election_theme_politicians_heading', array(
        'type' => 'string',
        'default' => 'Politician Profiles',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_politicians_subheading', array(
        'type' => 'string',
        'default' => 'Learn about representatives, their legislative agendas, and political party affiliations.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_politicians_count', array(
        'type' => 'integer',
        'default' => 12,
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

    register_setting('election_theme_options_group', 'election_theme_about_sections', array(
        'type' => 'string',
        'default' => '[]',
        'show_in_rest' => true,
        'sanitize_callback' => 'election_sanitize_json_settings',
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

    register_setting('election_theme_options_group', 'election_theme_products_empty_text', array(
        'type' => 'string',
        'default' => 'No products available yet. Check back soon!',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_products_cta_text', array(
        'type' => 'string',
        'default' => 'Call to Buy',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_products_global_phone', array(
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // Newsletter Section Settings
    register_setting('election_theme_options_group', 'election_theme_newsletter_heading', array(
        'type' => 'string',
        'default' => 'Join our Newsletter',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_newsletter_subheading', array(
        'type' => 'string',
        'default' => 'Get the latest election updates and deep dives directly in your inbox.',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_newsletter_btn_text', array(
        'type' => 'string',
        'default' => 'Subscribe Now',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_newsletter_placeholder', array(
        'type' => 'string',
        'default' => 'Your email address',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_expo_access_token', array(
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    register_setting('election_theme_options_group', 'election_theme_enable_auto_push', array(
        'type' => 'boolean',
        'default' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'wp_validate_boolean',
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
    // Social Media Icons
    $wp_customize->add_setting('social_icon_facebook', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('social_icon_facebook', array('label' => 'Facebook URL', 'section' => 'election_footer_settings', 'type' => 'url'));

    $wp_customize->add_setting('social_icon_instagram', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('social_icon_instagram', array('label' => 'Instagram URL', 'section' => 'election_footer_settings', 'type' => 'url'));

    $wp_customize->add_setting('social_icon_x', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('social_icon_x', array('label' => 'X (Twitter) URL', 'section' => 'election_footer_settings', 'type' => 'url'));
}
add_action('customize_register', 'election_awareness_customize_footer');

/**
 * --- Newsletter Subscriber System ---
 */

// 1. Create Database Table
function election_create_newsletter_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'election_subscribers';
    $charset_collate = $wpdb->get_charset_collate();

    // Standard SQL for maximum compatibility
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        created_at datetime NOT NULL,
        status varchar(20) DEFAULT 'active' NOT NULL,
        PRIMARY KEY  (id),
        KEY email (email)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'election_create_newsletter_table');
add_action('admin_init', 'election_create_newsletter_table'); // Ensure it exists for admin

// 2. Register REST API Endpoint for Subscription
add_action('rest_api_init', function () {
    register_rest_route('election-awareness/v1', '/subscribe', array(
        'methods' => 'POST',
        'callback' => 'election_newsletter_subscribe_handler',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('election-awareness/v1', '/subscribers', array(
        'methods' => 'GET',
        'callback' => 'election_get_subscribers_handler',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));

    register_rest_route('election-awareness/v1', '/subscribers/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'election_delete_subscriber_handler',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));
});

function election_newsletter_subscribe_handler($request) {
    global $wpdb;
    $params = $request->get_json_params();
    $email = isset($params['email']) ? sanitize_email($params['email']) : '';

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Please provide a valid email address.', array('status' => 400));
    }

    $table_name = $wpdb->prefix . 'election_subscribers';
    
    // Double check table existence
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        election_create_newsletter_table();
    }

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE email = %s", $email));

    if ($exists) {
        return array('success' => true, 'message' => 'You are already subscribed!');
    }

    // Explicitly insert created_at to avoid issues with MySQL versions/modes
    $inserted = $wpdb->insert($table_name, array(
        'email' => $email,
        'created_at' => current_time('mysql'),
        'status' => 'active'
    ));

    if ($inserted) {
        return array('success' => true, 'message' => 'Successfully subscribed!');
    }

    return new WP_Error('db_error', 'Could not save subscription to database. Error: ' . $wpdb->last_error, array('status' => 500));
}

function election_get_subscribers_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'election_subscribers';
    
    // Check if table exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return array(); // Table doesn't exist yet, return empty list
    }

    $results = $wpdb->get_results("SELECT id, email, created_at FROM $table_name ORDER BY created_at DESC");
    return $results ? $results : array();
}

function election_delete_subscriber_handler($request) {
    global $wpdb;
    $id = $request->get_param('id');
    $table_name = $wpdb->prefix . 'election_subscribers';
    
    // Check if table exists first to be safe
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        return new WP_Error('table_missing', 'Subscriber table does not exist.', array('status' => 500));
    }

    $deleted = $wpdb->delete($table_name, array('id' => $id), array('%d'));
    
    if ($deleted === false) {
        return new WP_Error('delete_error', 'Database error: ' . $wpdb->last_error, array('status' => 500));
    }

    if ($deleted === 0) {
        // Check if it exists at all
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE id = %d", $id));
        if (!$exists) {
            return new WP_Error('not_found', 'Subscriber not found. ID: ' . $id, array('status' => 404));
        }
        // If it exists but 0 rows were deleted, it's strange, but we'll report success if it's already gone or something? 
        // No, let's just say it failed to delete.
        return new WP_Error('delete_failed', 'Failed to delete subscriber. It might have been deleted already.', array('status' => 500));
    }
    
    return array('success' => true, 'message' => 'Subscriber deleted successfully.');
}

// 3. Send Email on New Post/Product
function election_notify_subscribers_on_publish($new_status, $old_status, $post) {
    if ($new_status !== 'publish' || $old_status === 'publish') {
        return;
    }

    if (!in_array($post->post_type, array('post', 'product'))) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'election_subscribers';
    $subscribers = $wpdb->get_col("SELECT email FROM $table_name WHERE status = 'active'");

    if (empty($subscribers)) {
        return;
    }

    $subject = '[' . get_bloginfo('name') . '] New Content Published: ' . $post->post_title;
    $content_type = ($post->post_type === 'product') ? 'New Product' : 'New Blog Post';
    
    $message = "Hello,\n\nWe just published a $content_type: " . $post->post_title . "\n\n";
    $message .= "You can view it here: " . get_permalink($post->ID) . "\n\n";
    $message .= "Thank you for staying informed!\n\n--\n" . get_bloginfo('name');

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    // Send emails in batches if needed, but for small lists direct sending is fine
    foreach ($subscribers as $email) {
        wp_mail($email, $subject, $message, $headers);
    }
}
add_action('transition_post_status', 'election_notify_subscribers_on_publish', 10, 3);
add_action('init', 'election_register_settings');

/**
 * --- SEO & Social Sharing Enhancement ---
 */

/**
 * Add Open Graph Tags for Social Sharing
 */
function election_add_social_sharing_meta() {
    if (!is_singular()) return;

    global $post;
    $img_url = '';
    
    if (has_post_thumbnail($post->ID)) {
        $img_url = get_the_post_thumbnail_url($post->ID, 'full');
    } else {
        // Fallback to logo
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        if ($logo) $img_url = $logo[0];
    }

    $title = get_the_title();
    $excerpt = wp_strip_all_tags(get_the_excerpt());
    if (empty($excerpt)) {
        $excerpt = wp_trim_words($post->post_content, 30);
    }
    $permalink = get_permalink();
    $site_name = get_bloginfo('name');

    // Open Graph
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />' . "\n";
    echo '<meta property="og:type" content="article" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($excerpt) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($permalink) . '" />' . "\n";
    if ($img_url) {
        echo '<meta property="og:image" content="' . esc_url($img_url) . '" />' . "\n";
        echo '<meta property="og:image:secure_url" content="' . esc_url($img_url) . '" />' . "\n";
    }

    // Twitter
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($excerpt) . '" />' . "\n";
    if ($img_url) {
        echo '<meta name="twitter:image" content="' . esc_url($img_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'election_add_social_sharing_meta', 5);

/**
 * Sanitize Bangla Slugs
 * This helps prevent URL issues with Bangla characters on some server environments.
 */
function election_sanitize_bangla_slugs($slug, $post_ID, $post_status, $post_type) {
    if (in_array($post_type, array('post', 'page', 'product'))) {
        // If the slug is entirely non-latin, we might want to keep it or handle it.
        // Modern WP usually handles it, but if it fails, we could potentially 
        // transliterate it. For now, we ensure the slug is properly encoded.
        return $slug;
    }
    return $slug;
}
// add_filter('wp_unique_post_slug', 'election_sanitize_bangla_slugs', 10, 4);

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

function election_register_api_routes() {
    register_rest_route('election/v1', '/products', array(
        'methods'  => 'GET',
        'callback' => 'election_get_products_rest_api',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('election/v1', '/signup', array(
        'methods'  => 'POST',
        'callback' => 'election_register_user_rest_api',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('election/v1', '/update-profile', array(
        'methods'  => 'POST',
        'callback' => 'election_update_profile_rest_api',
        'permission_callback' => function () {
            return is_user_logged_in(); // Requires JWT token or logged in session
        },
    ));

    register_rest_route('election/v1', '/register-push-token', array(
        'methods'  => 'POST',
        'callback' => 'election_register_push_token_rest_api',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('election/v1', '/push-tokens', array(
        'methods'  => 'GET',
        'callback' => 'election_get_push_tokens_rest_api',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));

    register_rest_route('election/v1', '/push-tokens/(?P<id>\d+)', array(
        'methods'  => 'DELETE',
        'callback' => 'election_delete_push_token_rest_api',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));

    register_rest_route('election/v1', '/send-manual-push', array(
        'methods'  => 'POST',
        'callback' => 'election_send_manual_push_rest_api',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        },
    ));
}
add_action('rest_api_init', 'election_register_api_routes');

function election_register_user_rest_api($request) {
    $params = $request->get_json_params();
    $phone = sanitize_text_field($params['phone']);
    $email = sanitize_email($params['email']);
    $password = $params['password'];
    $name = sanitize_text_field($params['name']);

    if (empty($name) || empty($phone) || empty($password)) {
        return new WP_Error('missing_fields', 'Name, phone number, and password are required.', array('status' => 400));
    }

    if (!preg_match('/^(?:\+88|88)?(01[3-9]\d{8})$/', $phone)) {
        return new WP_Error('invalid_phone', 'Please enter a valid Bangladesh phone number.', array('status' => 400));
    }

    if (username_exists($phone)) {
        return new WP_Error('user_exists', 'Phone number is already registered.', array('status' => 400));
    }
    
    if (!empty($email) && email_exists($email)) {
        return new WP_Error('email_exists', 'Email address is already in use.', array('status' => 400));
    }
    
    if (empty($email)) {
        $email = $phone . '@dummy.com';
    }

    $base_username = sanitize_title($name);
    if (empty($base_username)) {
        $base_username = 'user';
    }
    $username = $base_username;
    $suffix = 1;
    while (username_exists($username)) {
        $username = $base_username . $suffix;
        $suffix++;
    }

    $userdata = array(
        'user_login'   => $username,
        'user_pass'    => $password,
        'user_email'   => $email,
        'display_name' => $name,
        'first_name'   => $name,
        'role'         => 'subscriber'
    );
    
    $user_id = wp_insert_user($userdata);

    if (is_wp_error($user_id)) {
        return new WP_Error('registration_failed', $user_id->get_error_message(), array('status' => 500));
    }

    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'user_phone', $phone);
    return rest_ensure_response(array(
        'success' => true,
        'message' => 'User registered successfully!',
        'user_id' => $user_id
    ));
}

function election_update_profile_rest_api($request) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('not_logged_in', 'You must be logged in to update your profile.', array('status' => 401));
    }

    $current_user = get_userdata($user_id);
    $params = $request->get_params(); // handles multipart and json
    
    $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';
    $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : '';
    $email = isset($params['email']) ? sanitize_email($params['email']) : '';
    $password = isset($params['password']) ? $params['password'] : '';

    $userdata = array('ID' => $user_id);

    if (!empty($name)) {
        $userdata['display_name'] = $name;
        $userdata['first_name'] = $name;
    }

    if (!empty($phone)) {
        if (!preg_match('/^(?:\+88|88)?(01[3-9]\d{8})$/', $phone)) {
            return new WP_Error('invalid_phone', 'Please enter a valid Bangladesh phone number.', array('status' => 400));
        }
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, 'user_phone', $phone);
    }

    if (!empty($email) && $email !== $current_user->user_email) {
        if (!email_exists($email)) {
            $userdata['user_email'] = $email;
        } else {
            return new WP_Error('email_exists', 'Email address is already in use.', array('status' => 400));
        }
    } else if (empty($email) && !empty($phone)) {
        if (strpos($current_user->user_email, '@dummy.com') === false) {
            $userdata['user_email'] = $phone . '@dummy.com';
        }
    }

    if (!empty($password)) {
        $userdata['user_pass'] = $password;
    }

    $files = $request->get_file_params();
    $avatar_url = get_user_meta($user_id, 'custom_avatar_url', true);
    
    if (!empty($files['avatar']) && $files['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_type = wp_check_filetype($files['avatar']['name']);
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
        if (!in_array($file_type['type'], $allowed_types)) {
            return new WP_Error('invalid_file_type', 'Only JPEG, PNG, GIF, and WEBP images are allowed.', array('status' => 400));
        }
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload_overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($files['avatar'], $upload_overrides);
        
        if (isset($uploaded_file['file'])) {
            $avatar_url = $uploaded_file['url'];
            update_user_meta($user_id, 'custom_avatar_url', $avatar_url);
        } else {
            return new WP_Error('upload_error', $uploaded_file['error'], array('status' => 500));
        }
    }

    $update_id = wp_update_user($userdata);

    if (is_wp_error($update_id)) {
        return new WP_Error('update_failed', $update_id->get_error_message(), array('status' => 500));
    }

    $updated_user = get_userdata($user_id);

    return rest_ensure_response(array(
        'success' => true,
        'message' => 'Profile updated successfully!',
        'user' => array(
            'id' => $user_id,
            'name' => $updated_user->display_name,
            'email' => strpos($updated_user->user_email, '@dummy.com') === false ? $updated_user->user_email : '',
            'phone' => get_user_meta($user_id, 'billing_phone', true),
            'avatar_url' => $avatar_url
        )
    ));
}

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
 * Sanitize JSON settings while allowing HTML in specific fields
 */
function election_sanitize_json_settings($input) {
    $data = json_decode($input, true);
    if (!is_array($data)) {
        return '[]';
    }

    $sanitized_data = array_map(function($item) {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                if ($key === 'content' || $key === 'title' || $key === 'label') {
                    $item[$key] = wp_kses_post($value);
                } else {
                    $item[$key] = sanitize_text_field($value);
                }
            }
        }
        return $item;
    }, $data);

    return json_encode($sanitized_data);
}

/**
 * GitHub Theme Updater
 */
require_once get_template_directory() . '/inc/updater.php';

/**
 * --- Custom Push Notification System ---
 */

/**
 * REST API Callback to Register Push Tokens (Supports Expo & Native Device Tokens)
 */
function election_register_push_token_rest_api($request) {
    global $wpdb;
    $params = $request->get_json_params();
    
    $expo_token = isset($params['expo_token']) ? sanitize_text_field($params['expo_token']) : '';
    $native_token = isset($params['native_token']) ? sanitize_text_field($params['native_token']) : '';
    $platform = isset($params['platform']) ? sanitize_text_field($params['platform']) : '';
    $device_name = isset($params['device_name']) ? sanitize_text_field($params['device_name']) : '';
    $action = isset($params['action']) ? sanitize_text_field($params['action']) : 'register';

    if (empty($expo_token) && empty($native_token)) {
        return new WP_Error('missing_token', 'Expo token or native token is required.', array('status' => 400));
    }

    $table_name = $wpdb->prefix . 'election_push_tokens';

    if ($action === 'unregister') {
        if (!empty($native_token)) {
            $wpdb->delete($table_name, array('native_token' => $native_token), array('%s'));
        } elseif (!empty($expo_token)) {
            $wpdb->delete($table_name, array('expo_token' => $expo_token), array('%s'));
        }
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Token unregistered successfully.'
        ));
    }

    $user_id = get_current_user_id() ?: null;

    // 1. Create table if not exists dynamically
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) DEFAULT NULL,
        expo_token varchar(255) DEFAULT NULL,
        native_token varchar(255) DEFAULT NULL,
        platform varchar(50) DEFAULT NULL,
        device_name varchar(100) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // 2. Check if this token is already registered
    $existing = null;
    if (!empty($native_token)) {
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE native_token = %s", $native_token));
    } elseif (!empty($expo_token)) {
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE expo_token = %s", $expo_token));
    }

    if ($existing) {
        $wpdb->update(
            $table_name,
            array(
                'user_id' => $user_id,
                'expo_token' => $expo_token,
                'native_token' => $native_token,
                'platform' => $platform,
                'device_name' => $device_name
            ),
            array('id' => $existing->id)
        );
        $message = 'Token updated successfully.';
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'expo_token' => $expo_token,
                'native_token' => $native_token,
                'platform' => $platform,
                'device_name' => $device_name
            )
        );
        $message = 'Token registered successfully.';
    }

    return rest_ensure_response(array(
        'success' => true,
        'message' => $message
    ));
}

/**
 * Hook to send automatic push notifications to all users when a new post is published
 */
function election_send_push_notification_on_publish($new_status, $old_status, $post) {
    if ($new_status !== 'publish' || $old_status === 'publish') {
        return; 
    }

    if ($post->post_type !== 'post') {
        return; 
    }

    // Check if auto push is enabled
    $auto_push = get_option('election_theme_enable_auto_push', true);
    if (!$auto_push) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'election_push_tokens';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
        return;
    }

    $tokens = $wpdb->get_col("SELECT expo_token FROM $table_name WHERE expo_token != ''");

    if (empty($tokens)) {
        return;
    }

    $chunks = array_chunk($tokens, 100);

    foreach ($chunks as $chunk) {
        $messages = array();
        foreach ($chunk as $token) {
            $messages[] = array(
                'to' => $token,
                'title' => '📢 New Post: ' . $post->post_title,
                'body' => wp_strip_all_tags($post->post_excerpt ? $post->post_excerpt : wp_trim_words($post->post_content, 20)),
                'sound' => 'default',
                'data' => array(
                    'postId' => $post->ID,
                    'postTitle' => $post->post_title
                )
            );
        }

        $headers = array('Content-Type' => 'application/json');
        $access_token = get_option('election_theme_expo_access_token');
        if (!empty($access_token)) {
            $headers['Authorization'] = 'Bearer ' . sanitize_text_field($access_token);
        }

        wp_remote_post('https://exp.host/--/api/v2/push/send', array(
            'headers' => $headers,
            'body'    => json_encode($messages),
            'timeout' => 15,
        ));
    }
}
add_action('transition_post_status', 'election_send_push_notification_on_publish', 10, 3);

/**
 * REST API Callback to Retrieve Push Tokens
 */
function election_get_push_tokens_rest_api($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'election_push_tokens';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
        return array();
    }

    $results = $wpdb->get_results("SELECT t.*, u.user_email, u.display_name FROM $table_name t LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID ORDER BY t.created_at DESC", ARRAY_A);
    
    return rest_ensure_response($results ? $results : array());
}

/**
 * REST API Callback to Delete Push Token
 */
function election_delete_push_token_rest_api($request) {
    global $wpdb;
    $id = $request->get_param('id');
    $table_name = $wpdb->prefix . 'election_push_tokens';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
        return new WP_Error('table_missing', 'Push tokens table does not exist.', array('status' => 500));
    }

    $deleted = $wpdb->delete($table_name, array('id' => $id), array('%d'));

    if ($deleted === false) {
        return new WP_Error('delete_error', 'Database error: ' . $wpdb->last_error, array('status' => 500));
    }

    return rest_ensure_response(array('success' => true, 'message' => 'Token deleted successfully.'));
}

/**
 * REST API Callback to Send Manual Push Notification
 */
function election_send_manual_push_rest_api($request) {
    global $wpdb;
    $params = $request->get_json_params();
    $title = isset($params['title']) ? sanitize_text_field($params['title']) : '';
    $body = isset($params['body']) ? sanitize_text_field($params['body']) : '';

    if (empty($title) || empty($body)) {
        return new WP_Error('missing_fields', 'Title and body are required.', array('status' => 400));
    }

    $table_name = $wpdb->prefix . 'election_push_tokens';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
        return new WP_Error('table_missing', 'Push tokens table does not exist.', array('status' => 500));
    }

    $tokens = $wpdb->get_col("SELECT expo_token FROM $table_name WHERE expo_token != ''");

    if (empty($tokens)) {
        return array('success' => true, 'sent_count' => 0, 'message' => 'No registered devices found.');
    }

    $chunks = array_chunk($tokens, 100);
    $sent_count = 0;
    $errors = array();

    foreach ($chunks as $chunk) {
        $messages = array();
        foreach ($chunk as $token) {
            $messages[] = array(
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            );
        }

        $headers = array('Content-Type' => 'application/json');
        $access_token = get_option('election_theme_expo_access_token');
        if (!empty($access_token)) {
            $headers['Authorization'] = 'Bearer ' . sanitize_text_field($access_token);
        }

        $response = wp_remote_post('https://exp.host/--/api/v2/push/send', array(
            'headers' => $headers,
            'body'    => json_encode($messages),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            $errors[] = $response->get_error_message();
        } else {
            $sent_count += count($chunk);
        }
    }

    return rest_ensure_response(array(
        'success' => empty($errors),
        'sent_count' => $sent_count,
        'errors' => $errors,
        'message' => empty($errors) ? sprintf('Push notifications sent successfully to %d devices.', $sent_count) : 'Some notifications failed to send.'
    ));
}

/**
 * Auth Modal AJAX Handlers
 */
function election_custom_login() {
    check_ajax_referer('ajax-login-nonce', 'security-login');
    
    $user_login = sanitize_text_field($_POST['login_user']);
    $password = $_POST['login_password'];
    
    if (empty($user_login) || empty($password)) {
        wp_send_json_error(array('message' => 'Both fields are required.'));
    }

    $creds = array(
        'user_login'    => $user_login,
        'user_password' => $password,
        'remember'      => true
    );
    
    $user = wp_signon($creds, false);
    
    if (is_wp_error($user)) {
        wp_send_json_error(array('message' => 'Invalid credentials.'));
    }
    
    wp_send_json_success(array('message' => 'Login successful.'));
}
add_action('wp_ajax_nopriv_custom_login', 'election_custom_login');
add_action('wp_ajax_custom_login', 'election_custom_login');

function election_custom_register() {
    check_ajax_referer('ajax-register-nonce', 'security-register');
    
    $name = sanitize_text_field($_POST['reg_name']);
    $phone = sanitize_text_field($_POST['reg_phone']);
    $email = sanitize_email($_POST['reg_email']);
    $password = $_POST['reg_password'];
    
    if (empty($name) || empty($phone) || empty($password)) {
        wp_send_json_error(array('message' => 'Name, Phone, and Password are required.'));
    }
    
    if (!preg_match('/^(?:\+88|88)?(01[3-9]\d{8})$/', $phone)) {
        wp_send_json_error(array('message' => 'Please enter a valid Bangladesh phone number.'));
    }
    
    if (username_exists($phone)) {
        wp_send_json_error(array('message' => 'Phone number is already registered.'));
    }
    
    if (!empty($email) && email_exists($email)) {
        wp_send_json_error(array('message' => 'Email address is already in use.'));
    }
    
    if (empty($email)) {
        $email = $phone . '@dummy.com';
    }
    
    $base_username = sanitize_title($name);
    if (empty($base_username)) {
        $base_username = 'user';
    }
    $username = $base_username;
    $suffix = 1;
    while (username_exists($username)) {
        $username = $base_username . $suffix;
        $suffix++;
    }
    
    $userdata = array(
        'user_login' => $username,
        'user_pass'  => $password,
        'user_email' => $email,
        'display_name' => $name,
        'first_name' => $name,
        'role'       => 'subscriber'
    );
    
    $user_id = wp_insert_user($userdata);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => $user_id->get_error_message()));
    }
    
    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'user_phone', $phone);
    
    // Auto-login after registration
    $creds = array(
        'user_login'    => $phone,
        'user_password' => $password,
        'remember'      => true
    );
    wp_signon($creds, false);
    
    wp_send_json_success(array('message' => 'Registration successful.'));
}
add_action('wp_ajax_nopriv_custom_register', 'election_custom_register');
add_action('wp_ajax_custom_register', 'election_custom_register');

/**
 * Restrict Dashboard Access for Subscribers
 */
function election_restrict_admin_access() {
    if (is_admin() && !defined('DOING_AJAX') && current_user_can('subscriber')) {
        wp_redirect(home_url('/my-account/'));
        exit;
    }
}
add_action('admin_init', 'election_restrict_admin_access');

/**
 * User Data Center Admin Menu
 */
function election_app_users_data_center_menu() {
    add_menu_page(
        'App Users Data Center', // Page title
        'Data Center',           // Menu title
        'manage_options',        // Capability
        'app-users-data-center', // Menu slug
        'election_app_users_data_center_page', // Callback function
        'dashicons-groups',      // Icon
        30                       // Position
    );
}
add_action('admin_menu', 'election_app_users_data_center_menu');

function election_app_users_data_center_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle Delete Action
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['user_id'])) {
        $delete_id = intval($_GET['user_id']);
        if (wp_delete_user($delete_id)) {
            echo '<div class="notice notice-success is-dismissible"><p>User deleted successfully.</p></div>';
        }
    }

    $args = array(
        'role'    => 'subscriber',
        'orderby' => 'registered',
        'order'   => 'DESC',
    );
    $user_query = new WP_User_Query($args);
    $users = $user_query->get_results();

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">App Users Data Center</h1>
        <p>Manage users who have registered through the app or website front-end.</p>
        <hr class="wp-header-end">
        
        <style>
            .data-center-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.04); border: 1px solid #ccd0d4; }
            .data-center-table th, .data-center-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0f0f1; }
            .data-center-table th { background: #f6f7f7; font-weight: 600; color: #2c3338; border-bottom: 2px solid #ccd0d4; }
            .data-center-table tr:hover { background-color: #f6f7f7; }
            .action-links a { color: #d63638; text-decoration: none; }
            .action-links a:hover { text-decoration: underline; color: #b32d2e; }
            .phone-badge { background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 12px; font-size: 13px; font-weight: 600; display: inline-block; }
            .date-muted { color: #646970; font-size: 13px; }
        </style>

        <table class="data-center-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Avatar</th>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) : 
                        $phone = get_user_meta($user->ID, 'billing_phone', true);
                        if (empty($phone)) $phone = get_user_meta($user->ID, 'user_phone', true);
                        if (empty($phone)) $phone = $user->user_login; // Fallback if phone is username
                        $email = strpos($user->user_email, '@dummy.com') !== false ? '<em style="color:#8c8f94;">Not Provided</em>' : esc_html($user->user_email);
                        $avatar_url = get_user_meta($user->ID, 'custom_avatar_url', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($user->ID); ?></td>
                            <td>
                                <?php if ($avatar_url): ?>
                                    <img src="<?php echo esc_url($avatar_url); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" alt="Avatar">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #666;">
                                        <span class="dashicons dashicons-admin-users"></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo esc_html($user->display_name); ?></strong></td>
                            <td><span class="phone-badge"><?php echo esc_html($phone ?: 'N/A'); ?></span></td>
                            <td><?php echo $email; ?></td>
                            <td class="date-muted"><?php echo esc_html(date('M j, Y, g:i a', strtotime($user->user_registered))); ?></td>
                            <td class="action-links">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=app-users-data-center&action=delete&user_id=' . $user->ID)); ?>" onclick="return confirm('Are you sure you want to permanently delete this user?');">Delete User</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #646970;">No app users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Get politician party name (resolving Party CPT relationship or text value fallback)
 */
function election_get_politician_party_name($politician_id)
{
    $party_val = get_post_meta($politician_id, '_politician_party', true);
    if (empty($party_val)) {
        return 'Independent';
    }
    if (is_numeric($party_val)) {
        $party_post = get_post($party_val);
        if ($party_post && $party_post->post_type === 'party') {
            return $party_post->post_title;
        }
    }
    return $party_val;
}

/**
 * Search politician post IDs by name, constituency, or party name/CPT
 */
function election_search_politicians_ids($search_term)
{
    global $wpdb;
    $search_term = trim($search_term);
    if (empty($search_term)) {
        return array();
    }
    
    $like = '%' . $wpdb->esc_like($search_term) . '%';
    
    // Query 1: Politician posts with matching titles
    $post_ids_by_title = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'politician' AND post_status = 'publish' AND post_title LIKE %s",
        $like
    ));
    
    // Query 2: Politician posts with matching constituency or party name stored in meta
    $post_ids_by_meta = $wpdb->get_col($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE (meta_key = '_politician_constituency' AND meta_value LIKE %s) OR (meta_key = '_politician_party' AND meta_value LIKE %s)",
        $like, $like
    ));

    // Query 3: Match via Party CPT title
    $party_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'party' AND post_status = 'publish' AND post_title LIKE %s",
        $like
    ));
    
    $post_ids_by_party_id = array();
    if (!empty($party_ids)) {
        $placeholders = implode(',', array_fill(0, count($party_ids), '%d'));
        $post_ids_by_party_id = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_politician_party' AND meta_value IN ($placeholders)",
            ...$party_ids
        ));
    }
    
    $merged_ids = array_unique(array_merge($post_ids_by_title, $post_ids_by_meta, $post_ids_by_party_id));
    return !empty($merged_ids) ? $merged_ids : array(0);
}

/**
 * Render politician profiles grid HTML (reusable for Ajax & initial load)
 */
function election_render_politician_grid_html($search_query = '', $party_filter = '', $paged = 1)
{
    $args = array(
        'post_type' => 'politician',
        'posts_per_page' => intval(get_option('election_theme_politicians_count', 12)),
        'paged' => $paged,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    );

    if (!empty($search_query)) {
        $args['post__in'] = election_search_politicians_ids($search_query);
    }

    if (!empty($party_filter)) {
        $args['meta_query'] = array(
            array(
                'key' => '_politician_party',
                'value' => $party_filter,
                'compare' => '='
            )
        );
    }

    $politician_query = new WP_Query($args);

    ob_start();
    if ($politician_query->have_posts()): ?>
        <div class="politicians-grid" id="politicians-list">
            <?php
            while ($politician_query->have_posts()):
                $politician_query->the_post();
                $title = get_post_meta(get_the_ID(), '_politician_title', true) ?: 'Representative';
                $party = election_get_politician_party_name(get_the_ID());
                $constituency = get_post_meta(get_the_ID(), '_politician_constituency', true) ?: 'National Assembly';
                $focus = get_post_meta(get_the_ID(), '_politician_focus', true) ?: 'General Reform';
                ?>
                <article <?php post_class('politician-card'); ?> 
                         data-name="<?php echo esc_attr(strtolower(get_the_title())); ?>"
                         data-party="<?php echo esc_attr(strtolower($party)); ?>"
                         data-constituency="<?php echo esc_attr(strtolower($constituency)); ?>">
                    
                    <div class="card-image-wrapper">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium_large', array('class' => 'card-photo')); ?>
                        <?php else: ?>
                            <img src="https://i.pravatar.cc/350?u=<?php the_ID(); ?>" alt="Politician Photo" class="card-photo">
                        <?php endif; ?>
                        
                        <span class="card-party-badge"><?php echo esc_html($party); ?></span>
                    </div>

                    <div class="card-details">
                        <span class="card-label"><?php echo esc_html($title); ?></span>
                        <h3 class="card-name"><?php the_title(); ?></h3>
                        
                        <div class="card-meta-row">
                            <span class="meta-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <?php echo esc_html($constituency); ?>
                            </span>
                        </div>

                        <p class="card-focus">
                            <strong>Focus:</strong> <?php echo esc_html($focus); ?>
                        </p>

                        <a href="<?php the_permalink(); ?>" class="view-profile-btn button-yellow">
                            View Profile
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="directory-pagination">
            <?php
            echo paginate_links(array(
                'total' => $politician_query->max_num_pages,
                'current' => $paged,
                'prev_text' => '&laquo; Prev',
                'next_text' => 'Next &raquo;',
                'format' => '?paged=%#%',
            ));
            ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else: ?>
        <div class="no-politicians-found">
            <p>No politician profiles found matching the criteria.</p>
        </div>
    <?php endif;

    return ob_get_clean();
}

/**
 * Handle Ajax filtering of politicians
 */
function election_ajax_filter_politicians()
{
    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    $party_filter = isset($_POST['party_filter']) ? sanitize_text_field($_POST['party_filter']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $html = election_render_politician_grid_html($search_query, $party_filter, $paged);
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_filter_politicians', 'election_ajax_filter_politicians');
add_action('wp_ajax_nopriv_filter_politicians', 'election_ajax_filter_politicians');

/**
 * Search political party post IDs by title, content, or leader
 */
function election_search_parties_ids($search_term)
{
    global $wpdb;
    $search_term = trim($search_term);
    if (empty($search_term)) {
        return array();
    }
    
    $like = '%' . $wpdb->esc_like($search_term) . '%';
    
    // Query 1: Party posts with matching titles or content
    $post_ids_by_title_content = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'party' AND post_status = 'publish' AND (post_title LIKE %s OR post_content LIKE %s)",
        $like, $like
    ));
    
    // Query 2: Party posts with matching leader meta
    $post_ids_by_meta = $wpdb->get_col($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_party_leader' AND meta_value LIKE %s",
        $like
    ));
    
    $merged_ids = array_unique(array_merge($post_ids_by_title_content, $post_ids_by_meta));
    return !empty($merged_ids) ? $merged_ids : array(0);
}

/**
 * Render political parties list HTML (reusable for Ajax & initial load)
 */
function election_render_parties_list_html($search_query = '', $paged = 1)
{
    $posts_per_page = get_option('election_theme_parties_count', 10);
    $args = array(
        'post_type' => 'party',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    );

    if (!empty($search_query)) {
        $args['post__in'] = election_search_parties_ids($search_query);
    }

    $party_query = new WP_Query($args);

    ob_start();
    if ($party_query->have_posts()): ?>
        <div class="party-list">
            <?php
            while ($party_query->have_posts()):
                $party_query->the_post();
                $leader = get_post_meta(get_the_ID(), '_party_leader', true);
                $year = get_post_meta(get_the_ID(), '_party_year', true);
                $seats = get_post_meta(get_the_ID(), '_party_seats', true);
                $popularity = get_post_meta(get_the_ID(), '_party_popularity', true);

                $seats_val = floatval($seats);
                $popularity_val = floatval($popularity);
                $seats_percent = ($seats_val > 0) ? ($seats_val / 500) * 100 : 0;
                ?>
                <article <?php post_class('party-card reveal-on-scroll'); ?> id="party-<?php the_ID(); ?>">
                    <div class="party-card-main">
                        <!-- Left: Logo -->
                        <div class="party-logo">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else: ?>
                                <div class="party-logo-placeholder"></div>
                            <?php endif; ?>
                        </div>

                        <!-- Middle: Info -->
                        <div class="party-info">
                            <h2 class="party-name">
                                <?php the_title(); ?>
                            </h2>
                            <div class="party-meta">
                                <span class="meta-item"><strong>Leader:</strong>
                                    <?php echo esc_html($leader); ?>
                                </span>
                                <span class="meta-item"><strong>Founded:</strong>
                                    <?php echo esc_html($year); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Right: Stats -->
                        <div class="party-stats">
                            <div class="stat-group">
                                <label>Poll Popularity</label>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: <?php echo esc_attr($popularity_val); ?>%;">
                                    </div>
                                    <span class="stat-value">
                                        <?php echo esc_html($popularity_val); ?>%
                                    </span>
                                </div>
                            </div>
                            <div class="stat-group">
                                <label>Current Seats</label>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: <?php echo esc_attr($seats_percent); ?>%;">
                                    </div>
                                    <span class="stat-value">
                                        <?php echo esc_html($seats); ?> / 500
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Expand Trigger -->
                        <button class="party-expand-btn" aria-expanded="false"
                            aria-controls="party-content-<?php the_ID(); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                    </div>

                    <!-- Accordion Content -->
                    <div class="party-card-expanded" id="party-content-<?php the_ID(); ?>" hidden>
                        <div class="expanded-inner">
                            <div class="manifesto-section">
                                <div class="manifesto-header">
                                    <h3>Manifesto Summary</h3>
                                    <a href="<?php the_permalink(); ?>" class="btn-party-view">View Full Profile</a>
                                </div>
                                <div class="manifesto-text">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="blog-pagination">
            <?php
            echo paginate_links(array(
                'total' => $party_query->max_num_pages,
                'current' => $paged,
                'prev_text' => '&larr; Previous',
                'next_text' => 'Next &rarr;',
                'mid_size' => 2,
                'type' => 'list'
            ));
            ?>
        </div>
        <?php wp_reset_postdata(); ?>

    <?php else: ?>
        <p class="no-parties-found">No political parties found matching the criteria.</p>
    <?php endif;

    return ob_get_clean();
}

/**
 * Handle Ajax filtering of political parties
 */
function election_ajax_filter_parties()
{
    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $html = election_render_parties_list_html($search_query, $paged);
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_filter_parties', 'election_ajax_filter_parties');
add_action('wp_ajax_nopriv_filter_parties', 'election_ajax_filter_parties');

/**
 * Expose feed_post custom meta in REST API
 */
add_action('rest_api_init', 'election_expose_feed_meta_rest_api');
function election_expose_feed_meta_rest_api() {
    register_rest_field('feed_post', 'feed_type', array(
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_feed_type', true) ?: 'text';
        },
        'schema' => null,
    ));

    register_rest_field('feed_post', 'feed_media', array(
        'get_callback' => function($post) {
            $media_ids = get_post_meta($post['id'], '_feed_media_ids', true);
            $media_urls = array();
            if (!empty($media_ids)) {
                $ids = explode(',', $media_ids);
                foreach ($ids as $id) {
                    $url = wp_get_attachment_url($id);
                    if ($url) {
                        $type = wp_attachment_is_image($id) ? 'image' : 'video';
                        $media_urls[] = array(
                            'id' => $id,
                            'url' => $url,
                            'type' => $type
                        );
                    }
                }
            }
            return $media_urls;
        },
        'schema' => null,
    ));
}
