<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<main id="primary" class="site-main container section-spacing-top" style="text-align: center; padding: 100px 20px;">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title" style="font-size: 4rem; color: var(--color-electric-yellow);">404</h1>
            <h2 class="page-subtitle">Page Not Found</h2>
        </header>

        <div class="page-content" style="margin-top: 20px;">
            <p>
                <?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'election-awareness'); ?>
            </p>
            <?php get_search_form(); ?>

            <div style="margin-top: 40px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Return Home</a>
            </div>

            <!-- DEBUG INFO -->
            <div
                style="margin: 40px auto; padding: 20px; background: rgba(255,0,0,0.1); border: 1px solid red; font-family: monospace; text-align: left; max-width: 600px;">
                <strong>Debug Info:</strong><br>
                <strong>Request:</strong> <?php echo esc_html($_SERVER['REQUEST_URI']); ?><br>
                <strong>Post Type Archive Link:</strong>
                <?php echo esc_html(get_post_type_archive_link('party')); ?><br>
                <strong>Rewrite Rules Valid:</strong> <?php echo (get_option('rewrite_rules')) ? 'Yes' : 'No'; ?>
            </div>
            <!-- END DEBUG -->
        </div>
    </section>
</main>

<?php
get_footer();
