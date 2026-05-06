<?php
/**
 * Template Name: Products Page
 */

get_header();

$heading = get_option('election_theme_products_heading', 'Awareness Gear');
$subtitle = get_option('election_theme_products_subtitle', 'Support the movement with our official merchandise and premium reports.');

// Query all products
$products_query = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => 50,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
));

$products_data = array();
if ($products_query->have_posts()) {
    while ($products_query->have_posts()) {
        $products_query->the_post();
        $products_data[] = array(
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
    wp_reset_postdata();
}
?>

<main id="primary" class="products-page">

    <!-- Products Page Header -->
    <section class="products-header container section-spacing-top">
        <div class="centered-content">
            <h1 class="page-title"><?php echo esc_html($heading); ?></h1>
            <p class="section-desc"><?php echo esc_html($subtitle); ?></p>
        </div>
    </section>

    <!-- Product Grid (React) -->
    <section class="products-section container">
        <div id="product-grid-root" data-props='<?php echo esc_attr(wp_json_encode(array(
            'initialProducts' => $products_data,
        ))); ?>'></div>
    </section>

</main>

<?php get_footer(); ?>
