<?php
/**
 * Template Name: Home Page
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- 4. Posts Feed -->
    <section class="posts-feed container section-spacing">
        <?php
        $feed_query = new WP_Query(array(
            'post_type' => 'feed_post',
            'posts_per_page' => 10,
            'post_status' => 'publish'
        ));
        
        $real_posts = array();
        if ($feed_query->have_posts()) {
            while ($feed_query->have_posts()) {
                $feed_query->the_post();
                
                $type = get_post_meta(get_the_ID(), '_feed_type', true) ?: 'text';
                $media_ids_string = get_post_meta(get_the_ID(), '_feed_media_ids', true);
                
                $media_items = array();
                
                // Add uploaded media first
                if (!empty($media_ids_string)) {
                    $media_ids = explode(',', $media_ids_string);
                    foreach ($media_ids as $att_id) {
                        $att_url = wp_get_attachment_url($att_id);
                        $is_image = wp_attachment_is_image($att_id);
                        if ($att_url) {
                            $media_items[] = array(
                                'url' => $att_url,
                                'type' => $is_image ? 'image' : 'video'
                            );
                        }
                    }
                }
                
                // Fallback to featured image if available and no other media exists
                if (empty($media_items) && has_post_thumbnail()) {
                    $media_items[] = array(
                        'url' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                        'type' => 'image'
                    );
                }
                
                $author_id = get_post_field('post_author', get_the_ID());
                $author_name = get_the_author_meta('display_name', $author_id);
                $author_avatar = get_avatar_url($author_id);
                
                // fallback to random avatar if default mystery man
                if (strpos($author_avatar, 'mystery') !== false || empty($author_avatar)) {
                    $author_avatar = 'https://i.pravatar.cc/150?u=' . $author_id;
                }
                
                $real_posts[] = array(
                    'id' => get_the_ID(),
                    'type' => $type,
                    'author' => array(
                        'name' => $author_name,
                        'avatar' => $author_avatar
                    ),
                    'date' => human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago',
                    'caption' => get_the_content(),
                    'media_items' => $media_items
                );
            }
            wp_reset_postdata();
        }
        ?>

        <style>
            .posts-feed-container { max-width: 600px; margin: 0 auto; }
            .post-card { background-color: var(--color-midnight-blue, #0A1019); margin-top: 15px; padding-top: 15px; border: 1px solid var(--color-steel-blue, #161F2E); border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); transition: transform 0.3s ease, border-color 0.3s ease; }
            .post-card:hover { transform: translateY(-2px); border-color: rgba(255, 255, 0, 0.3); }
            .post-header { display: flex; align-items: center; padding: 0 16px 12px; }
            .post-avatar { width: 44px; height: 44px; border-radius: 50%; margin-right: 14px; border: 2px solid var(--color-steel-blue, #161F2E); }
            .post-author-info { display: flex; flex-direction: column; }
            .post-author-name { font-size: 16px; font-weight: bold; color: var(--color-text-white, #FFFFFF); font-family: var(--font-heading); }
            .post-date { font-size: 13px; color: var(--color-text-muted, #94A3B8); margin-top: 2px; }
            .post-caption { font-size: 15px; color: var(--color-text-grey, #E2E8F0); padding: 0 16px 16px; margin: 0; line-height: 1.6; }
            
            /* Facebook style gallery grid */
            .post-media-gallery { display: grid; gap: 2px; border-top: 1px solid var(--color-steel-blue); border-bottom: 1px solid var(--color-steel-blue); background: var(--color-steel-blue); }
            .post-media-gallery.gallery-count-1 { grid-template-columns: 1fr; }
            .post-media-gallery.gallery-count-2 { grid-template-columns: 1fr 1fr; }
            .post-media-gallery.gallery-count-3 { grid-template-columns: 1fr 1fr; }
            .post-media-gallery.gallery-count-3 .media-item-wrapper:nth-child(1) { grid-column: 1 / 3; height: 350px; }
            .post-media-gallery.gallery-count-3 .media-item-wrapper:nth-child(n+2) { height: 200px; }
            .post-media-gallery.gallery-count-more { grid-template-columns: 1fr 1fr; grid-template-rows: 200px 200px; }
            
            .media-item-wrapper { position: relative; width: 100%; height: 100%; overflow: hidden; background: #000; }
            .gallery-count-1 .media-item-wrapper { max-height: 500px; display: flex; align-items: center; justify-content: center; }
            .gallery-count-2 .media-item-wrapper { height: 300px; }
            .gallery-count-more .media-item-wrapper { height: 100%; }
            
            .media-item-wrapper img, .media-item-wrapper video { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; }
            .gallery-count-1 .media-item-wrapper img, .gallery-count-1 .media-item-wrapper video { object-fit: contain; max-height: 500px; pointer-events: none; }
            
            .more-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; z-index: 10; pointer-events: none; }
            
            /* Lightbox Styles */
            .feed-lightbox { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 99999; display: none; flex-direction: column; justify-content: center; align-items: center; }
            .feed-lightbox.active { display: flex; }
            .lightbox-content { width: 90%; height: 85%; display: flex; justify-content: center; align-items: center; position: relative; }
            .lightbox-content img, .lightbox-content video { max-width: 100%; max-height: 100%; object-fit: contain; box-shadow: 0 0 30px rgba(0,0,0,0.5); }
            .lightbox-close { position: absolute; top: 20px; right: 30px; background: none; border: none; color: #fff; font-size: 40px; cursor: pointer; z-index: 100000; transition: color 0.3s; }
            .lightbox-close:hover { color: var(--color-electric-yellow, #FFFF00); }
            .lightbox-prev, .lightbox-next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: none; color: #fff; font-size: 30px; width: 60px; height: 60px; cursor: pointer; border-radius: 50%; z-index: 100000; transition: background 0.3s, color 0.3s; display: flex; align-items: center; justify-content: center; }
            .lightbox-prev:hover, .lightbox-next:hover { background: rgba(255,255,255,0.3); color: var(--color-electric-yellow, #FFFF00); }
            .lightbox-prev { left: 20px; }
            .lightbox-next { right: 20px; }

            .post-footer { display: flex; padding: 14px 16px; background-color: rgba(22, 31, 46, 0.3); }
            .share-button { background: none; border: none; display: flex; align-items: center; cursor: pointer; font-size: 14px; color: var(--color-text-muted, #94A3B8); font-weight: 500; padding: 8px 16px; border-radius: 50px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-family: var(--font-body); gap: 8px; }
            .share-button:hover { background-color: rgba(255, 255, 0, 0.1); color: var(--color-electric-yellow, #FFFF00); transform: translateY(-2px); }
            .share-svg { transition: transform 0.3s ease; }
            .share-button:hover .share-svg { transform: rotate(-5deg) scale(1.1); }
        </style>

        <div class="section-header-wrapper">
            <h2 class="section-heading">Posts Feed</h2>
        </div>
        <div class="posts-feed-container">
            <?php if (!empty($real_posts)): ?>
                <?php foreach ($real_posts as $item): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <img src="<?php echo esc_url($item['author']['avatar']); ?>" alt="Avatar" class="post-avatar">
                            <div class="post-author-info">
                                <span class="post-author-name"><?php echo esc_html($item['author']['name']); ?></span>
                                <span class="post-date"><?php echo esc_html($item['date']); ?></span>
                            </div>
                        </div>
                        
                        <?php if ($item['caption']): ?>
                            <div class="post-caption"><?php echo wp_kses_post(wpautop($item['caption'])); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['media_items'])): ?>
                            <?php 
                            $media_count = count($item['media_items']);
                            $gallery_class = 'gallery-count-' . ($media_count >= 4 ? 'more' : $media_count);
                            ?>
                            <div class="post-media-gallery <?php echo $gallery_class; ?>" data-media="<?php echo esc_attr(json_encode($item['media_items'])); ?>">
                                <?php 
                                $display_items = array_slice($item['media_items'], 0, 4);
                                foreach ($display_items as $index => $media): 
                                ?>
                                    <div class="media-item-wrapper">
                                        <?php if ($media['type'] === 'image'): ?>
                                            <img src="<?php echo esc_url($media['url']); ?>" alt="Post Image" loading="lazy">
                                        <?php elseif ($media['type'] === 'video'): ?>
                                            <video src="<?php echo esc_url($media['url']); ?>" controls preload="metadata"></video>
                                        <?php endif; ?>
                                        
                                        <?php if ($index === 3 && $media_count > 4): ?>
                                            <div class="more-overlay">+<?php echo ($media_count - 4); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-footer">
                            <button class="share-button">
                                <svg class="share-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                Share
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--color-text-muted);">No feed posts available yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="feed-lightbox" class="feed-lightbox">
        <button class="lightbox-close" aria-label="Close">&times;</button>
        <button class="lightbox-prev" aria-label="Previous">&#10094;</button>
        <div class="lightbox-content"></div>
        <button class="lightbox-next" aria-label="Next">&#10095;</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = document.getElementById('feed-lightbox');
        const lightboxContent = lightbox.querySelector('.lightbox-content');
        const closeBtn = lightbox.querySelector('.lightbox-close');
        const prevBtn = lightbox.querySelector('.lightbox-prev');
        const nextBtn = lightbox.querySelector('.lightbox-next');
        
        let currentMedia = [];
        let currentIndex = 0;

        function renderLightbox() {
            lightboxContent.innerHTML = '';
            if (!currentMedia || currentMedia.length === 0) return;
            const item = currentMedia[currentIndex];
            
            if (item.type === 'image') {
                const img = document.createElement('img');
                img.src = item.url;
                lightboxContent.appendChild(img);
            } else if (item.type === 'video') {
                const video = document.createElement('video');
                video.src = item.url;
                video.controls = true;
                video.autoplay = true;
                lightboxContent.appendChild(video);
            }
            
            prevBtn.style.display = currentMedia.length > 1 ? 'flex' : 'none';
            nextBtn.style.display = currentMedia.length > 1 ? 'flex' : 'none';
        }

        function openLightbox(mediaItems, startIndex) {
            currentMedia = mediaItems;
            currentIndex = startIndex;
            renderLightbox();
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            lightboxContent.innerHTML = ''; // stops video
            document.body.style.overflow = '';
        }

        closeBtn.addEventListener('click', closeLightbox);
        
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : currentMedia.length - 1;
            renderLightbox();
        });

        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex = (currentIndex < currentMedia.length - 1) ? currentIndex + 1 : 0;
            renderLightbox();
        });

        // Close on background click
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target === lightboxContent) {
                closeLightbox();
            }
        });

        // Attach click events
        document.querySelectorAll('.post-media-gallery').forEach(gallery => {
            const mediaData = gallery.getAttribute('data-media');
            if (!mediaData) return;
            
            try {
                const mediaItems = JSON.parse(mediaData);
                const wrappers = gallery.querySelectorAll('.media-item-wrapper');
                
                wrappers.forEach((wrapper, index) => {
                    wrapper.style.cursor = 'pointer';
                    wrapper.addEventListener('click', (e) => {
                        e.preventDefault();
                        openLightbox(mediaItems, index);
                    });
                });
            } catch (err) {
                console.error("Error parsing media items", err);
            }
        });
    });
    </script>

</main>

<?php
get_footer();

