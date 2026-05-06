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
                        {/* Social Icons Section */}
                        <div style={{
                            padding: '0 40px',
                            marginBottom: '30px',
                            display: 'flex',
                            gap: '20px'
                        }}>
                            {['Facebook', 'Instagram', 'Twitter', 'YouTube'].map((social) => (
                                <a
                                    key={social}
                                    href="#"
                                    aria-label={social}
                                    style={{ color: '#E2E8F0', display: 'flex', transition: 'color 0.3s' }}
                                    onMouseEnter={(e) => e.currentTarget.style.color = '#FACC15'}
                                    onMouseLeave={(e) => e.currentTarget.style.color = '#E2E8F0'}
                                >
                                    {social === 'Facebook' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>}
                                    {social === 'Instagram' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>}
                                    {social === 'Twitter' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>}
                                    {social === 'YouTube' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" /></svg>}
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
