<?php
/**
 * Template Name: Privacy Policy
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        $privacy_title = get_option('election_theme_privacy_title', 'Privacy Policy');
        $privacy_subtitle = get_option('election_theme_privacy_subtitle', 'We value your privacy as much as your vote.');
        $privacy_sections = get_option('election_theme_privacy_sections', '[]');
        $privacy_updated = get_option('election_theme_privacy_updated_date', '');

        $props = array(
            'initialTitle' => $privacy_title,
            'initialSubtitle' => $privacy_subtitle,
            'initialSections' => json_decode($privacy_sections, true),
            'initialUpdatedDate' => $privacy_updated
        );
        ?>
        <div id="privacy-policy-root" data-props='<?php echo esc_attr(json_encode($props)); ?>'>
            <!-- React will mount here -->
        </div>
    </main>
</div>

<?php
get_footer();
