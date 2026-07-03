<?php
/**
 * Template Name: Political Parties Page
 */

get_header(); ?>

<main id="primary" class="site-main">
    <header class="page-header container section-spacing-top" style="text-align: center;">
        <h1 class="page-title">
            <?php echo esc_html(get_option('election_theme_parties_heading', 'All Political Parties')); ?>
        </h1>
        <p class="page-subtitle">
            <?php echo esc_html(get_option('election_theme_parties_subheading', 'A comprehensive directory of active political groups and their manifestos.')); ?>
        </p>
    </header>

    <!-- Search Section -->
    <?php
    $search_query = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';
    ?>
    <section class="directory-filters container">
        <div class="filter-controls-wrapper">
            <input type="text" id="party-search" placeholder="Search by party name, leader, or manifesto..." class="search-input" value="<?php echo esc_attr($search_query); ?>">
        </div>
    </section>

    <section class="party-archive-section container section-spacing-bottom" id="party-list-container">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
        echo election_render_parties_list_html($search_query, $paged);
        ?>
    </section>
</main>

<style>
/* Political Parties Directory Search Styles */
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

.no-parties-found {
    text-align: center;
    padding: 50px;
    background: rgba(22, 31, 46, 0.2);
    border: 1px solid var(--color-steel-blue);
    border-radius: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('party-search');
    const listContainer = document.getElementById('party-list-container');
    let debounceTimer;

    function fetchFilteredParties(page = 1) {
        const searchQuery = searchInput ? searchInput.value : '';

        listContainer.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('action', 'filter_parties');
        formData.append('search_query', searchQuery);
        formData.append('paged', page);

        const ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data.html) {
                listContainer.innerHTML = res.data.html;
                
                // Trigger reveal-on-scroll animation for dynamically loaded items
                listContainer.querySelectorAll('.reveal-on-scroll').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('in-view');
                    }, index * 50); // Staggered fade-in
                });
                
                const url = new URL(window.location.href);
                if (searchQuery) url.searchParams.set('search_query', searchQuery);
                else url.searchParams.delete('search_query');
                
                if (page > 1) url.searchParams.set('paged', page);
                else url.searchParams.delete('paged');
                
                window.history.pushState({}, '', url.toString());
            }
            listContainer.style.opacity = '1';
        })
        .catch(err => {
            console.error('Filtering failed:', err);
            listContainer.style.opacity = '1';
        });
    }

    if (searchInput && listContainer) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchFilteredParties(1);
            }, 300);
        });

        listContainer.addEventListener('click', function(e) {
            // Handle pagination clicks within the container
            const pageLink = e.target.closest('.blog-pagination a') || e.target.closest('.page-numbers');
            if (pageLink && !pageLink.classList.contains('current') && !pageLink.classList.contains('active')) {
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
                
                fetchFilteredParties(pageNum);
                listContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
});
</script>

<?php get_footer(); ?>