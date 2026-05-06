import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import SkeletonPost from './SkeletonPost';

const PostFeed = ({ initialPosts, context = {}, type = 'grid', postsPerPage = 9 }) => {
    const [posts, setPosts] = useState(initialPosts || []);
    const [page, setPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [hasMore, setHasMore] = useState(true);
    const loaderRef = useRef(null);

    // Context can contain categoryId, tagId, or searchQuery
    const { categoryId, tagId, searchQuery } = context;

    const highlightText = (text, query) => {
        if (!query) return text;
        const parts = text.split(new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi'));
        return (
            <>
                {parts.map((part, i) =>
                    part.toLowerCase() === query.toLowerCase() ? (
                        <span key={i} className="highlight-term">{part}</span>
                    ) : (
                        part
                    )
                )}
            </>
        );
    };

    const fetchPosts = async () => {
        if (loading || !hasMore) return;

        setLoading(true);
        const nextPage = page + 1;

        try {
            let url = `/wp-json/wp/v2/posts?page=${nextPage}&per_page=${postsPerPage}&_embed`;

            if (categoryId) url += `&categories=${categoryId}`;
            if (tagId) url += `&tags=${tagId}`;
            if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

            const response = await fetch(url);

            if (!response.ok) {
                setHasMore(false);
                setLoading(false);
                return;
            }

            const newPostsRaw = await response.json();

            if (newPostsRaw.length === 0) {
                setHasMore(false);
            } else {
                // Transform WP JSON to our card format
                const transformedPosts = newPostsRaw.map(post => ({
                    id: post.id,
                    title: post.title.rendered,
                    link: post.link,
                    date: new Date(post.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }),
                    excerpt: post.excerpt.rendered.replace(/<[^>]+>/g, '').substring(0, 150) + '...',
                    image: post._embedded?.['wp:featuredmedia']?.[0]?.source_url || '',
                    category: post._embedded?.['wp:term']?.[0]?.[0]?.name || ''
                }));

                setPosts(prev => [...prev, ...transformedPosts]);
                setPage(nextPage);
            }
        } catch (error) {
            console.error('Error fetching posts:', error);
            setHasMore(false);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        const observer = new IntersectionObserver(
            entries => {
                if (entries[0].isIntersecting && !loading && hasMore) {
                    fetchPosts();
                }
            },
            { threshold: 0.1 }
        );

        if (loaderRef.current) {
            observer.observe(loaderRef.current);
        }

        return () => observer.disconnect();
    }, [loading, hasMore, page]);

    return (
        <div className={`post-feed-container ${type}-view`}>
            <div className={type === 'grid' ? 'standard-archive-grid' : 'search-results-list'}>
                <AnimatePresence mode="popLayout">
                    {posts.map((post, index) => {
                        const isFeatured = type === 'grid' && index === 0;

                        return (
                            <motion.article
                                key={post.id}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.5, delay: (index % 3) * 0.1 }}
                                className={isFeatured ? 'featured-archive-card' : (type === 'grid' ? 'blog-card' : 'search-result-card')}
                                style={isFeatured ? { gridColumn: '1 / -1' } : {}}
                            >
                                {isFeatured ? (
                                    <div className="featured-card-inner">
                                        <div className="featured-card-image">
                                            <a href={post.link}>
                                                {post.image ? <img src={post.image} alt={post.title} /> : <div className="placeholder-image"></div>}
                                            </a>
                                        </div>
                                        <div className="featured-card-content">
                                            <span className="post-cat-label">{post.category}</span>
                                            <h2 className="featured-card-title">
                                                <a href={post.link}>{searchQuery ? highlightText(post.title, searchQuery) : <span dangerouslySetInnerHTML={{ __html: post.title }} />}</a>
                                            </h2>
                                            <div className="featured-card-excerpt">
                                                {searchQuery ? highlightText(post.excerpt, searchQuery) : <div dangerouslySetInnerHTML={{ __html: post.excerpt }} />}
                                            </div>
                                            <a href={post.link} className="btn btn-outline btn-sm">Read Full Perspective</a>
                                        </div>
                                    </div>
                                ) : (
                                    type === 'grid' ? (
                                        <>
                                            <div className="blog-card-image">
                                                <a href={post.link}>
                                                    {post.image ? <img src={post.image} alt={post.title} /> : <div className="placeholder-image"></div>}
                                                </a>
                                            </div>
                                            <div className="blog-card-content">
                                                <h2 className="blog-card-title">
                                                    <a href={post.link}>{searchQuery ? highlightText(post.title, searchQuery) : <span dangerouslySetInnerHTML={{ __html: post.title }} />}</a>
                                                </h2>
                                                <div className="blog-card-excerpt">
                                                    {searchQuery ? highlightText(post.excerpt, searchQuery) : <div dangerouslySetInnerHTML={{ __html: post.excerpt }} />}
                                                </div>
                                                <a href={post.link} className="blog-read-more">Read More</a>
                                            </div>
                                        </>
                                    ) : (
                                        <>
                                            <div className="result-thumb">
                                                <a href={post.link}>
                                                    {post.image ? <img src={post.image} alt={post.title} /> : <div className="thumb-placeholder"></div>}
                                                </a>
                                            </div>
                                            <div className="result-content">
                                                <span className="result-date">{post.date}</span>
                                                <h2 className="result-title">
                                                    <a href={post.link}>{searchQuery ? highlightText(post.title, searchQuery) : <span dangerouslySetInnerHTML={{ __html: post.title }} />}</a>
                                                </h2>
                                                <div className="result-excerpt">
                                                    {searchQuery ? highlightText(post.excerpt, searchQuery) : <div dangerouslySetInnerHTML={{ __html: post.excerpt }} />}
                                                </div>
                                            </div>
                                        </>
                                    )
                                )}
                            </motion.article>
                        );
                    })}
                </AnimatePresence>
            </div>

            {/* Skeletons while loading */}
            {loading && (
                <div className={type === 'grid' ? 'standard-archive-grid' : 'search-results-list'} style={{ marginTop: '40px' }}>
                    {[1, 2, 3].map(i => (
                        <SkeletonPost key={`skel-${i}`} type={type} />
                    ))}
                </div>
            )}

            {/* Sentinel element for Intersection Observer */}
            <div ref={loaderRef} style={{ height: '40px' }}></div>

            {!hasMore && posts.length > 0 && (
                <div className="feed-end-notice" style={{ textAlign: 'center', padding: '60px 0', opacity: 0.5 }}>
                    <p>You've reached the end of the collection.</p>
                </div>
            )}
        </div>
    );
};

export default PostFeed;
