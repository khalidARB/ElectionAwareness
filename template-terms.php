<?php
/**
 * Template Name: Terms & Conditions
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        $terms_title = get_option('election_theme_terms_title', 'Terms of Service');
        $terms_sections = get_option('election_theme_terms_sections', '[]');
        $terms_updated = get_option('election_theme_terms_updated_date', '');
        $props = array(
            'initialTitle' => $terms_title,
            'initialSections' => json_decode($terms_sections, true),
            'initialUpdatedDate' => $terms_updated
        );
        ?>
        <div id="terms-conditions-root" data-props='<?php echo esc_attr(json_encode($props)); ?>'>
            <!-- React will mount here -->
        </div>
    </main>
</div>

<?php
get_footer();
