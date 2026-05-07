import React from 'react';
import { createRoot } from 'react-dom/client';
import { motion } from 'framer-motion';
import MobileMenu from './components/MobileMenu';

import SearchOverlay from './components/SearchOverlay';
import PostFeed from './components/PostFeed';
import TermsAndConditions from './components/TermsAndConditions';
import PrivacyPolicy from './components/PrivacyPolicy';
import ProductGrid from './components/ProductGrid';
import { initMagneticLinks } from './utils/magnetic';

import { initScrollReveal } from './utils/animations';

const App = () => {
    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="react-content"
            style={{ marginTop: '20px' }}
        >
            <div className="card">
                <h2>Interactive React Component</h2>
                <p>This component is powered by React and Framer Motion.</p>
                <div style={{ marginTop: '20px' }}>
                    <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        className="btn"
                    >
                        Click Me
                    </motion.button>
                </div>
            </div>
        </motion.div>
    );
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mount Main App (Demo)
    const rootElement = document.getElementById('election-app-root');
    if (rootElement) {
        const root = createRoot(rootElement);
        root.render(<App />);
    }

    // 2. Mount Mobile Menu Overlay
    let mobileMenuRoot = document.getElementById('mobile-menu-overlay');
    if (!mobileMenuRoot) {
        mobileMenuRoot = document.createElement('div');
        mobileMenuRoot.id = 'mobile-menu-overlay';
        document.body.appendChild(mobileMenuRoot);
    }

    if (mobileMenuRoot) {
        const root = createRoot(mobileMenuRoot);
        root.render(<MobileMenu />);
    }


    // 3. Mount Search Overlay
    const searchOverlayRoot = document.getElementById('search-overlay-root');
    if (searchOverlayRoot) {
        const root = createRoot(searchOverlayRoot);
        root.render(<SearchOverlay />);
    }

    // 3b. Mount Post Feed (Archives/Search)
    const postFeedRoot = document.getElementById('post-feed-root');
    if (postFeedRoot) {
        const props = JSON.parse(postFeedRoot.getAttribute('data-props') || '{}');
        const root = createRoot(postFeedRoot);
        root.render(<PostFeed {...props} />);
    }

    // 3c. Mount Terms & Conditions
    const termsRoot = document.getElementById('terms-conditions-root');
    if (termsRoot) {
        const root = createRoot(termsRoot);
        const props = termsRoot.dataset.props ? JSON.parse(termsRoot.dataset.props) : {};
        root.render(<TermsAndConditions {...props} />);
    }

    // 3d. Mount Privacy Policy
    const privacyRoot = document.getElementById('privacy-policy-root');
    if (privacyRoot) {
        const root = createRoot(privacyRoot);
        const props = privacyRoot.dataset.props ? JSON.parse(privacyRoot.dataset.props) : {};
        root.render(<PrivacyPolicy {...props} />);
    }

    // 3e. Mount Product Grid
    const productGridRoot = document.getElementById('product-grid-root');
    if (productGridRoot) {
        const props = JSON.parse(productGridRoot.getAttribute('data-props') || '{}');
        const root = createRoot(productGridRoot);
        root.render(<ProductGrid {...props} />);
    }

    // 4. Init Magnetic Links
    initMagneticLinks();
    initScrollReveal();

    // 4b. Active Focus Logic (Search Page)
    const massiveSearchInput = document.querySelector('.search-field-massive');
    if (massiveSearchInput) {
        massiveSearchInput.addEventListener('focus', () => {
            document.body.classList.add('searching-focus');
        });
        massiveSearchInput.addEventListener('blur', () => {
            document.body.classList.remove('searching-focus');
        });
    }

    // 5. Hero Slider Logic (Infinite/Circular)
    const slider = document.querySelector('.hero-slider');
    const prevBtn = document.querySelector('.hero-prev');
    const nextBtn = document.querySelector('.hero-next');

    if (slider && prevBtn && nextBtn) {
        const scrollAmount = () => window.innerWidth; // Dynamic width

        const goNext = () => {
            const maxScroll = slider.scrollWidth - slider.clientWidth;
            if (slider.scrollLeft >= maxScroll - 10) {
                slider.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
            }
        };

        const goPrev = () => {
            if (slider.scrollLeft <= 10) {
                slider.scrollTo({ left: slider.scrollWidth, behavior: 'smooth' });
            } else {
                slider.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
            }
        };

        // Auto Scroll (5 seconds)
        let autoScrollTimer = setInterval(goNext, 5000);

        const resetTimer = () => {
            clearInterval(autoScrollTimer);
            autoScrollTimer = setInterval(goNext, 5000);
        };

        nextBtn.addEventListener('click', () => {
            goNext();
            resetTimer();
        });

        prevBtn.addEventListener('click', () => {
            goPrev();
            resetTimer();
        });
    }

    // 6. Party Card Accordion
    document.addEventListener('click', (e) => {
        const expandBtn = e.target.closest('.party-expand-btn');
        if (expandBtn) {
            const card = expandBtn.closest('.party-card');
            const content = card.querySelector('.party-card-expanded');
            const isExpanded = card.classList.toggle('is-expanded');

            expandBtn.setAttribute('aria-expanded', isExpanded);
            content.hidden = !isExpanded;
        }
    });

    // 7. Stats Counter Animation
    const stats = document.querySelectorAll('.stat-number');
    if (stats.length > 0) {
        const observerOptions = {
            threshold: 0.5
        };

        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'));
                    animateCount(el, target);
                    observer.unobserve(el);
                }
            });
        }, observerOptions);

        stats.forEach(stat => counterObserver.observe(stat));
    }

    function animateCount(el, target) {
        let start = 0;
        const duration = 2000; // 2 seconds
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function: easeOutExpo
            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

            const current = Math.floor(ease * target);
            el.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                el.textContent = target.toLocaleString();
            }
        }

        requestAnimationFrame(update);
    }

    // 8. Reading Progress Bar
    const progressBar = document.getElementById('reading-progress-bar');
    if (progressBar) {
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + "%";
        });
    }

    // 9. Blog Filters Scroll Logic
    const filtersWrapper = document.querySelector('.blog-filters-wrapper');
    const filtersLeft = document.getElementById('blog-filters-left');
    const filtersRight = document.getElementById('blog-filters-right');

    if (filtersWrapper && filtersLeft && filtersRight) {
        const scrollAmount = 300;

        filtersLeft.addEventListener('click', () => {
            filtersWrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        filtersRight.addEventListener('click', () => {
            filtersWrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        const updateArrowVisibility = () => {
            const { scrollLeft, scrollWidth, clientWidth } = filtersWrapper;

            // Show/hide left arrow
            if (scrollLeft <= 5) {
                filtersLeft.classList.add('hidden');
            } else {
                filtersLeft.classList.remove('hidden');
            }

            // Show/hide right arrow
            if (scrollLeft + clientWidth >= scrollWidth - 5) {
                filtersRight.classList.add('hidden');
            } else {
                filtersRight.classList.remove('hidden');
            }
        };

        filtersWrapper.addEventListener('scroll', updateArrowVisibility);
        window.addEventListener('resize', updateArrowVisibility);

        // Initial check after a small delay to ensure rendering is complete
        setTimeout(updateArrowVisibility, 100);
    }
});
