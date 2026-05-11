<?php
/**
 * The template for displaying single product pages
 */

get_header(); ?>

<main id="primary" class="site-main product-single-page">
    <div class="container">
        <?php while (have_posts()) : the_post(); 
            $product_id = get_the_ID();
            $price = get_post_meta($product_id, '_product_price', true);
            $contact_phone = get_post_meta($product_id, '_product_contact_phone', true);
            $global_phone = get_option('election_theme_products_global_phone');
            $display_phone = $contact_phone ?: $global_phone;
            $cta_text = get_option('election_theme_products_cta_text', 'Call to Buy');
            $short_desc = get_post_meta($product_id, '_product_short_desc', true);
            $featured_img = get_the_post_thumbnail_url($product_id, 'full');
            $categories = get_the_terms($product_id, 'product_cat');
        ?>

            <div class="product-details-container reveal-item">
                <!-- Breadcrumbs -->
                <nav class="product-breadcrumbs">
                    <a href="<?php echo home_url(); ?>">Home</a>
                    <span class="sep">/</span>
                    <a href="<?php echo get_post_type_archive_link('product'); ?>">Awareness Gear</a>
                    <span class="sep">/</span>
                    <span class="current"><?php the_title(); ?></span>
                </nav>

                <div class="product-main-layout">
                    <!-- Left: Product Image -->
                    <div class="product-image-section">
                        <div class="product-gallery-main">
                            <?php if ($featured_img) : ?>
                                <img src="<?php echo esc_url($featured_img); ?>" alt="<?php the_title_attribute(); ?>" class="main-img">
                            <?php else : ?>
                                <div class="product-img-placeholder">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <path d="M21 15l-5-5L5 21" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Product Info -->
                    <div class="product-info-section">
                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <span class="product-meta-cat"><?php echo esc_html($categories[0]->name); ?></span>
                        <?php endif; ?>
                        
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        
                        <div class="product-price-tag">
                            <span class="currency">৳</span>
                            <span class="amount"><?php echo number_format($price); ?></span>
                        </div>

                        <?php if ($short_desc) : ?>
                            <div class="product-short-description">
                                <p><?php echo nl2br(esc_html($short_desc)); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="product-description-excerpt">
                            <?php the_content(); ?>
                        </div>

                        <div class="product-actions">
                            <a href="<?php echo $display_phone ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $display_phone) : '#'; ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="whatsapp-buy-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                <?php echo esc_html($cta_text); ?> on WhatsApp
                            </a>
                        </div>

                        <div class="product-meta-extra">
                            <div class="meta-item">
                                <span class="label">SKU:</span>
                                <span class="value"><?php echo esc_html(get_post_meta($product_id, '_product_sku', true) ?: 'AG-' . str_pad($product_id, 4, '0', STR_PAD_LEFT)); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Availability:</span>
                                <?php 
                                $availability = get_post_meta($product_id, '_product_availability', true) ?: 'instock';
                                $avail_label = 'In Stock';
                                $avail_class = 'in-stock';
                                if ($availability === 'outofstock') {
                                    $avail_label = 'Out of Stock';
                                    $avail_class = 'out-of-stock';
                                } elseif ($availability === 'preorder') {
                                    $avail_label = 'Pre-order';
                                    $avail_class = 'pre-order';
                                }
                                ?>
                                <span class="value <?php echo $avail_class; ?>"><?php echo esc_html($avail_label); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Shipping:</span>
                                <span class="value">Ships within 24-48 hours</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <section class="related-products">
                    <div class="related-header">
                        <h2 class="related-title">You May Also Like</h2>
                        <div class="related-line"></div>
                    </div>

                    <?php
                    $related_query = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 4,
                        'post__not_in' => array($product_id),
                        'orderby' => 'rand'
                    ));

                    if ($related_query->have_posts()) : ?>
                        <div class="related-grid">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); 
                                $r_price = get_post_meta(get_the_ID(), '_product_price', true);
                            ?>
                                <a href="<?php the_permalink(); ?>" class="related-card">
                                    <div class="r-img">
                                        <?php if (has_post_thumbnail()) : the_post_thumbnail('medium'); endif; ?>
                                    </div>
                                    <div class="r-info">
                                        <h4><?php the_title(); ?></h4>
                                        <span class="r-price">৳<?php echo number_format($r_price); ?></span>
                                    </div>
                                </a>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
