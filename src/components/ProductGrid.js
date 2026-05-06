import React, { useState, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const SORT_OPTIONS = [
    { key: 'latest', label: 'Latest' },
    { key: 'price-low', label: 'Price: Low → High' },
    { key: 'price-high', label: 'Price: High → Low' },
];

const sortProducts = (products, sortKey) => {
    const sorted = [...products];
    switch (sortKey) {
        case 'price-low':
            return sorted.sort((a, b) => a.price - b.price);
        case 'price-high':
            return sorted.sort((a, b) => b.price - a.price);
        case 'latest':
        default:
            return sorted.sort((a, b) => new Date(b.date) - new Date(a.date));
    }
};

const ProductCard = ({ product, index }) => {
    const [buyState, setBuyState] = useState('idle'); // idle | adding | added

    const handleBuyClick = useCallback((e) => {
        e.preventDefault();
        if (buyState !== 'idle') return;

        setBuyState('adding');

        setTimeout(() => {
            setBuyState('added');

            setTimeout(() => {
                if (product.buyUrl && product.buyUrl !== '#') {
                    window.open(product.buyUrl, '_blank', 'noopener,noreferrer');
                }
                setBuyState('idle');
            }, 1200);
        }, 1500);
    }, [buyState, product.buyUrl]);

    const getBtnText = () => {
        switch (buyState) {
            case 'adding':
                return (
                    <>
                        <span className="product-buy-spinner"></span>
                        Adding...
                    </>
                );
            case 'added':
                return 'Added ✓';
            default:
                return 'Buy Now';
        }
    };

    return (
        <motion.article
            className="product-card"
            initial={{ opacity: 0, y: 40 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: '-50px' }}
            transition={{ duration: 0.5, delay: (index % 4) * 0.1 }}
        >
            <div className="product-card-image">
                {product.image ? (
                    <img
                        src={product.image}
                        alt={product.title}
                        loading="lazy"
                    />
                ) : (
                    <div className="product-card-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="M21 15l-5-5L5 21" />
                        </svg>
                    </div>
                )}
            </div>

            <div className="product-card-content">
                <h3 className="product-card-title" title={product.title}>
                    {product.title}
                </h3>
                <p className="product-card-desc">
                    {product.shortDesc}
                </p>

                <div className="product-card-footer">
                    <span className="product-card-price">
                        ${product.price.toFixed(2)}
                    </span>
                    <button
                        className={`product-buy-btn ${buyState}`}
                        onClick={handleBuyClick}
                        disabled={buyState !== 'idle'}
                    >
                        {getBtnText()}
                    </button>
                </div>
            </div>
        </motion.article>
    );
};

const ProductGrid = ({ initialProducts = [] }) => {
    const [activeSort, setActiveSort] = useState('latest');

    const sorted = sortProducts(initialProducts, activeSort);

    if (initialProducts.length === 0) {
        return (
            <div className="products-empty">
                <p>No products available yet. Check back soon!</p>
            </div>
        );
    }

    return (
        <div className="product-grid-wrapper">
            {/* Sort Bar */}
            <div className="products-sort-bar">
                <span className="sort-label">Sort by:</span>
                <div className="sort-options">
                    {SORT_OPTIONS.map((opt) => (
                        <button
                            key={opt.key}
                            className={`sort-btn ${activeSort === opt.key ? 'active' : ''}`}
                            onClick={() => setActiveSort(opt.key)}
                        >
                            {opt.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Product Grid */}
            <div className="products-grid">
                <AnimatePresence mode="popLayout">
                    {sorted.map((product, index) => (
                        <ProductCard
                            key={product.id}
                            product={product}
                            index={index}
                        />
                    ))}
                </AnimatePresence>
            </div>
        </div>
    );
};

export default ProductGrid;
