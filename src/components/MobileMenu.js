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
                                    {social === 'Facebook' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>}
                                    {social === 'Instagram' && <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>}
                                    {social === 'LinkedIn' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22.23 0H1.77C.8 0 0 .77 0 1.72v20.56C0 23.23.8 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.72V1.72C24 .77 23.2 0 22.23 0zM7.12 20.45H3.56V9h3.56v11.45zM5.34 7.58c-1.14 0-2.06-.93-2.06-2.06 0-1.14.92-2.06 2.06-2.06 1.14 0 2.06.92 2.06 2.06 0 1.13-.92 2.06-2.06 2.06zM20.45 20.45h-3.56v-5.6c0-1.34-.03-3.06-1.87-3.06-1.87 0-2.15 1.46-2.15 2.96v5.7h-3.56V9h3.42v1.56h.05c.48-.9 1.63-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29z"/></svg>}
                                    {social === 'X' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>}
                                    {social === 'YouTube' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" /></svg>}
                                    {social === 'TikTok' && <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.17-2.86-.6-4.12-1.31a11.31 11.31 0 0 1-1.87-1.35v7.45c.03 1.83-.5 3.61-1.48 5.1a9.42 9.42 0 0 1-4.01 3.73c-1.74.83-3.69 1.11-5.61.8-1.92-.31-3.74-1.28-5.07-2.73-1.34-1.44-2.13-3.32-2.22-5.28-.09-1.96.46-3.92 1.58-5.5a9.38 9.38 0 0 1 4.18-3.41c1.51-.55 3.12-.66 4.67-.34v4.13c-1.12-.35-2.35-.29-3.42.17a5.35 5.35(2.6 2.45 5.3 5.3 0 0 0-.25 4.31c.36 1 .98 1.89 1.8 2.53.82.63 1.83.98 2.86 1 1.03.01 2.06-.27 2.94-.82.88-.55 1.57-1.35 1.98-2.3.41-.95.53-2.01.35-3.03V0h.01Z"/></svg>}
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
