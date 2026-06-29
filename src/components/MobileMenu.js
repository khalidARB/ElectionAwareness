import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { createRoot } from 'react-dom/client';

const menuVariants = {
    closed: {
        x: "100%",
        transition: {
            duration: 0.4,
            ease: [0.32, 0.72, 0, 1],
            when: "afterChildren"
        }
    },
    open: {
        x: "0%",
        transition: {
            duration: 0.6,
            ease: [0.32, 0.72, 0, 1],
            when: "beforeChildren",
            staggerChildren: 0.08
        }
    }
};

const itemVariants = {
    closed: { opacity: 0, x: 20 },
    open: { opacity: 1, x: 0 }
};

const MobileMenu = () => {
    const [isOpen, setIsOpen] = useState(false);

    useEffect(() => {
        const toggleBtn = document.querySelector('.mobile-menu-toggle');
        const handleToggle = () => {
            setIsOpen(prev => !prev);
            if (toggleBtn) toggleBtn.classList.toggle('is-active');
        };

        if (toggleBtn) {
            toggleBtn.addEventListener('click', handleToggle);
        }

        return () => {
            if (toggleBtn) toggleBtn.removeEventListener('click', handleToggle);
        };
    }, []);

    // Handle Body Scroll Locking
    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }

        return () => {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        };
    }, [isOpen]);

    const handleLinkClick = () => {
        setIsOpen(false);
        const toggleBtn = document.querySelector('.mobile-menu-toggle');
        if (toggleBtn) toggleBtn.classList.remove('is-active');
    };

    return (
        <AnimatePresence>
            {isOpen && (
                <>
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={handleLinkClick}
                        style={{
                            position: 'fixed',
                            inset: 0,
                            backgroundColor: 'rgba(0, 0, 0, 0.6)',
                            backdropFilter: 'blur(4px)',
                            zIndex: 998
                        }}
                    />

                    {/* Menu Content */}
                    <motion.div
                        className="mobile-menu-offcanvas"
                        initial="closed"
                        animate="open"
                        exit="closed"
                        variants={menuVariants}
                        style={{
                            position: 'fixed',
                            top: 0,
                            right: 0,
                            width: 'min(400px, 85vw)',
                            height: '100vh',
                            backgroundColor: '#0A1019',
                            zIndex: 999,
                            display: 'flex',
                            flexDirection: 'column',
                            justifyContent: 'center', // Center content vertically
                            boxShadow: '-10px 0 30px rgba(0,0,0,0.5)',
                            borderLeft: '1px solid rgba(255,255,255,0.05)',
                            overflowY: 'auto',
                            WebkitOverflowScrolling: 'touch'
                        }}
                    >
                        <div style={{
                            padding: '0 40px',
                            marginBottom: '30px',
                            display: 'flex',
                            gap: '20px'
                        }}>
                            {['Facebook', 'Instagram', 'LinkedIn', 'X', 'YouTube', 'TikTok'].map((social) => (
                                <a
                                    key={social}
                                    href="#"
                                    aria-label={social}
                                    style={{ color: '#E2E8F0', display: 'flex', transition: 'color 0.3s' }}
                                    onMouseEnter={(e) => e.currentTarget.style.color = '#FACC15'}
                                    onMouseLeave={(e) => e.currentTarget.style.color = '#E2E8F0'}
                                >
                                    {social === 'Facebook' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>}
                                    {social === 'Instagram' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>}
                                    {social === 'LinkedIn' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>}
                                    {social === 'X' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>}
                                    {social === 'YouTube' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>}
                                    {social === 'TikTok' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>}
                                </a>
                            ))}
                        </div>

                        {/* Search Section */}
                        <div style={{
                            padding: '0 40px',
                            marginBottom: '40px'
                        }}>
                            <button
                                className="search-toggle"
                                onClick={handleLinkClick}
                                style={{
                                    background: 'rgba(255, 255, 255, 0.05)',
                                    border: '1px solid rgba(255, 255, 255, 0.1)',
                                    borderRadius: '12px',
                                    padding: '14px 20px',
                                    color: '#FFFFFF',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '12px',
                                    cursor: 'pointer',
                                    width: '100%',
                                    fontFamily: '"Space Grotesk", sans-serif',
                                    fontWeight: '600',
                                    fontSize: '15px'
                                }}
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                Search Stories...
                            </button>
                        </div>

                        {/* Navigation Links */}
                        <motion.ul style={{
                            listStyle: 'none',
                            padding: '0 40px',
                            margin: 0,
                            display: 'flex',
                            flexDirection: 'column',
                            gap: '10px'
                        }}>
                            {(window.electionAppData?.menuItems || []).map((item, index) => (
                                <motion.li key={index} variants={itemVariants}>
                                    <a
                                        href={item.url}
                                        onClick={handleLinkClick}
                                        style={{
                                            color: '#FFFFFF',
                                            fontFamily: '"Space Grotesk", sans-serif',
                                            fontSize: '24px',
                                            fontWeight: '700',
                                            textDecoration: 'none',
                                            display: 'block',
                                            padding: '10px 0',
                                            transition: 'transform 0.2s ease, color 0.2s ease'
                                        }}
                                        onMouseEnter={(e) => {
                                            e.currentTarget.style.color = '#FACC15';
                                            e.currentTarget.style.transform = 'translateX(5px)';
                                        }}
                                        onMouseLeave={(e) => {
                                            e.currentTarget.style.color = '#FFFFFF';
                                            e.currentTarget.style.transform = 'translateX(0)';
                                        }}
                                    >
                                        {item.title}
                                    </a>
                                </motion.li>
                            ))}
                        </motion.ul>
                    </motion.div>
                </>
            )}
        </AnimatePresence>
    );
};

export default MobileMenu;
