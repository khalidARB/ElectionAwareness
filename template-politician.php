<?php
/**
 * Template Name: Politician Profiles
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<main id="primary" class="site-main politician-directory-page">
    <header class="page-header container section-spacing-top" style="text-align: center;">
        <h1 class="page-title">
            <?php echo esc_html(get_option('election_theme_politicians_heading', 'Politician Profiles')); ?>
        </h1>
        <p class="page-subtitle">
            <?php echo esc_html(get_option('election_theme_politicians_subheading', 'Learn about representatives, their legislative agendas, and political party affiliations.')); ?>
        </p>
    </header>

    <!-- Filters Section -->
    <section class="directory-filters container">
        <div class="filter-controls-wrapper">
            <input type="text" id="politician-search" placeholder="Search by name, party, or constituency..." class="search-input">
            
            <select id="party-filter" class="filter-select">
                <option value="">All Parties</option>
                <?php
                // Fetch unique parties for filter
                global $wpdb;
                $table_meta = $wpdb->postmeta;
                $parties = $wpdb->get_col("SELECT DISTINCT meta_value FROM $table_meta WHERE meta_key = '_politician_party' AND meta_value != ''");
                foreach ($parties as $party) {
                    echo '<option value="' . esc_attr($party) . '">' . esc_html($party) . '</option>';
                }
                ?>
            </select>
        </div>
    </section>

    <!-- Grid Section -->
    <section class="politicians-grid-section container section-spacing-bottom">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'politician',
            'posts_per_page' => 12,
            'paged' => $paged,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        $politician_query = new WP_Query($args);

        if ($politician_query->have_posts()): ?>
            <div class="politicians-grid" id="politicians-list">
                <?php
                while ($politician_query->have_posts()):
                    $politician_query->the_post();
                    $title = get_post_meta(get_the_ID(), '_politician_title', true) ?: 'Representative';
                    $party = get_post_meta(get_the_ID(), '_politician_party', true) ?: 'Independent';
                    $constituency = get_post_meta(get_the_ID(), '_politician_constituency', true) ?: 'National Assembly';
                    $focus = get_post_meta(get_the_ID(), '_politician_focus', true) ?: 'General Reform';
                    ?>
                    <article <?php post_class('politician-card reveal-on-scroll'); ?> 
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
                ));
                ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <div class="no-politicians-found">
                <p>No politician profiles found. Create them in your WordPress admin dashboard.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<style>
/* Politician Directory Styles */
.politician-directory-page {
    background-color: var(--color-deep-void);
    color: var(--color-text-grey);
}

.directory-filters {
    margin-bottom: 40px;
}

.filter-controls-wrapper {
    display: flex;
    gap: 20px;
    background: rgba(22, 31, 46, 0.4);
    border: 1px solid var(--color-steel-blue);
    padding: 20px;
    border-radius: 12px;
}

@media (max-width: 768px) {
    .filter-controls-wrapper {
        flex-direction: column;
    }
}

.search-input {
    flex: 1;
    padding: 12px 20px;
    background-color: var(--color-steel-blue);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--color-text-white);
    font-size: 15px;
    outline: none;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    border-color: var(--color-electric-yellow);
}

.filter-select {
    width: 220px;
    padding: 12px 20px;
    background-color: var(--color-steel-blue);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--color-text-white);
    font-size: 15px;
    outline: none;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    border-color: var(--color-electric-yellow);
}

.politicians-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

@media (max-width: 992px) {
    .politicians-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .politicians-grid {
        grid-template-columns: 1fr;
    }
}

.politician-card {
    background: rgba(22, 31, 46, 0.4);
    border: 1px solid var(--color-steel-blue);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.politician-card:hover {
    transform: translateY(-5px);
    border-color: var(--color-electric-yellow);
    box-shadow: 0 10px 30px rgba(250, 204, 21, 0.1);
}

.card-image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 1.1;
    overflow: hidden;
}

.card-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.politician-card:hover .card-photo {
    transform: scale(1.05);
}

.card-party-badge {
    position: absolute;
    bottom: 15px;
    left: 15px;
    background-color: var(--color-electric-yellow);
    color: black;
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-details {
    padding: 25px;
}

.card-label {
    font-size: 12px;
    text-transform: uppercase;
    color: var(--color-electric-yellow);
    font-weight: 700;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 8px;
}

.card-name {
    font-size: 1.4rem;
    font-family: var(--font-heading);
    color: var(--color-text-white);
    margin: 0 0 12px 0;
}

.card-meta-row {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 13px;
    color: var(--color-text-muted);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-focus {
    font-size: 14px;
    line-height: 1.5;
    margin: 0 0 20px 0;
}

.view-profile-btn {
    display: block;
    text-align: center;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.directory-pagination {
    margin-top: 50px;
    display: flex;
    justify-content: center;
    gap: 8px;
}

.directory-pagination .page-numbers {
    padding: 8px 16px;
    border: 1px solid var(--color-steel-blue);
    border-radius: 6px;
    color: var(--color-text-grey);
    text-decoration: none;
    transition: all 0.3s ease;
}

.directory-pagination .page-numbers.current,
.directory-pagination .page-numbers:hover {
    background-color: var(--color-electric-yellow);
    border-color: var(--color-electric-yellow);
    color: black;
}

.no-politicians-found {
    text-align: center;
    padding: 50px;
    background: rgba(22, 31, 46, 0.2);
    border: 1px solid var(--color-steel-blue);
    border-radius: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('politician-search');
    const partyFilter = document.getElementById('party-filter');
    const cards = document.querySelectorAll('.politician-card');

    if (searchInput && partyFilter) {
        function filterPoliticians() {
            const query = searchInput.value.toLowerCase();
            const partyVal = partyFilter.value.toLowerCase();

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const party = card.getAttribute('data-party');
                const constituency = card.getAttribute('data-constituency');

                const matchesSearch = name.includes(query) || constituency.includes(query) || party.includes(query);
                const matchesParty = partyVal === '' || party === partyVal;

                if (matchesSearch && matchesParty) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterPoliticians);
        partyFilter.addEventListener('change', filterPoliticians);
    }
});
</script>

<?php get_footer(); ?>
