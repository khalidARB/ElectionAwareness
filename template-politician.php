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
    <?php
    $search_query = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';
    $party_filter = isset($_GET['party_filter']) ? sanitize_text_field($_GET['party_filter']) : '';
    ?>
    <section class="directory-filters container">
        <div class="filter-controls-wrapper">
            <input type="text" id="politician-search" placeholder="Search by name, party, or constituency..." class="search-input" value="<?php echo esc_attr($search_query); ?>">
            <div class="custom-dropdown" id="party-filter-dropdown">
                <?php
                $selected_party_label = 'All Parties';
                if (!empty($party_filter)) {
                    $party_post = get_post($party_filter);
                    if ($party_post && $party_post->post_type === 'party') {
                        $selected_party_label = $party_post->post_title;
                    }
                }
                ?>
                <div class="dropdown-trigger">
                    <span class="selected-label"><?php echo esc_html($selected_party_label); ?></span>
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-item<?php echo empty($party_filter) ? ' active' : ''; ?>" data-value="">All Parties</div>
                    <?php
                    // Fetch parties from the CPT
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
                            $is_active = ($party_filter == $party_id);
                            echo '<div class="dropdown-item' . ($is_active ? ' active' : '') . '" data-value="' . esc_attr($party_id) . '">' . esc_html($party_title) . '</div>';
                        }
                        wp_reset_postdata();
                    }
                    ?>
                </div>
                <input type="hidden" id="party-filter" value="<?php echo esc_attr($party_filter); ?>">
            </div>
        </div>
    </section>

    <!-- Grid Section -->
    <section class="politicians-grid-section container section-spacing-bottom" id="politician-grid-container">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
        echo election_render_politician_grid_html($search_query, $party_filter, $paged);
        ?>
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
    .custom-dropdown {
        width: 100% !important;
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

.custom-dropdown {
    position: relative;
    width: auto;
    min-width: 220px;
    max-width: 100%;
    user-select: none;
}

.dropdown-trigger {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background-color: var(--color-steel-blue);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--color-text-white);
    font-size: 15px;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.custom-dropdown.open .dropdown-trigger,
.dropdown-trigger:hover {
    border-color: var(--color-electric-yellow);
}

.dropdown-arrow {
    margin-left: 15px;
    transition: transform 0.3s ease;
}

.custom-dropdown.open .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    width: 100%;
    background-color: #0b111c;
    border: 1px solid var(--color-steel-blue);
    border-radius: 8px;
    overflow-y: auto;
    max-height: 250px;
    z-index: 100;
    display: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
}

.custom-dropdown.open .dropdown-menu {
    display: block;
}

.dropdown-item {
    padding: 12px 20px;
    color: var(--color-text-white);
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
}

.dropdown-item:hover {
    background-color: var(--color-electric-yellow);
    color: black !important;
}

.dropdown-item.active {
    background-color: rgba(250, 204, 21, 0.2);
    color: var(--color-electric-yellow);
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
    const gridContainer = document.getElementById('politician-grid-container');
    let debounceTimer;

    function fetchFilteredPoliticians(page = 1) {
        const searchQuery = searchInput ? searchInput.value : '';
        const partyVal = partyFilter ? partyFilter.value : '';

        gridContainer.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('action', 'filter_politicians');
        formData.append('search_query', searchQuery);
        formData.append('party_filter', partyVal);
        formData.append('paged', page);

        const ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data.html) {
                gridContainer.innerHTML = res.data.html;
                
                const url = new URL(window.location.href);
                if (searchQuery) url.searchParams.set('search_query', searchQuery);
                else url.searchParams.delete('search_query');
                
                if (partyVal) url.searchParams.set('party_filter', partyVal);
                else url.searchParams.delete('party_filter');
                
                if (page > 1) url.searchParams.set('paged', page);
                else url.searchParams.delete('paged');
                
                window.history.pushState({}, '', url.toString());
            }
            gridContainer.style.opacity = '1';
        })
        .catch(err => {
            console.error('Filtering failed:', err);
            gridContainer.style.opacity = '1';
        });
    }

    if (searchInput && partyFilter && gridContainer) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchFilteredPoliticians(1);
            }, 300);
        });

        partyFilter.addEventListener('change', function() {
            fetchFilteredPoliticians(1);
        });

        gridContainer.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.page-numbers');
            if (pageLink && !pageLink.classList.contains('current')) {
                e.preventDefault();
                
                let pageNum = 1;
                const href = pageLink.getAttribute('href');
                if (href) {
                    const urlParams = new URLSearchParams(href.split('?')[1]);
                    if (urlParams.has('paged')) {
                        pageNum = parseInt(urlParams.get('paged'));
                    } else {
                        const match = href.match(/\/page\/(\d+)/);
                        if (match) {
                            pageNum = parseInt(match[1]);
                        }
                    }
                }
                
                fetchFilteredPoliticians(pageNum);
                gridContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Custom Dropdown JS Action bindings
        const customDropdown = document.getElementById('party-filter-dropdown');
        if (customDropdown) {
            const trigger = customDropdown.querySelector('.dropdown-trigger');
            const hiddenInput = document.getElementById('party-filter');
            const selectedLabel = customDropdown.querySelector('.selected-label');

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                customDropdown.classList.toggle('open');
            });

            customDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.getAttribute('data-value');
                    const label = this.textContent;

                    customDropdown.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    selectedLabel.textContent = label;
                    hiddenInput.value = val;
                    customDropdown.classList.remove('open');

                    // Trigger the change event manually
                    hiddenInput.dispatchEvent(new Event('change'));
                });
            });

            document.addEventListener('click', function() {
                customDropdown.classList.remove('open');
            });
        }
    }
});
</script>

<?php get_footer(); ?>
