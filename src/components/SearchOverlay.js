import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const SearchOverlay = () => {
    const [isOpen, setIsOpen] = useState(false);
    const inputRef = useRef(null);

    useEffect(() => {
        const toggle = () => setIsOpen(prev => !prev);
        window.toggleSearchOverlay = toggle;

        const handleGlobalClick = (e) => {
            const btn = e.target.closest('.search-toggle');
            if (btn) {
                e.preventDefault();
                toggle();
            }
        };

        document.addEventListener('click', handleGlobalClick);
        return () => {
            document.removeEventListener('click', handleGlobalClick);
            delete window.toggleSearchOverlay;
        };
    }, []);

    useEffect(() => {
        if (isOpen && inputRef.current) {
            setTimeout(() => inputRef.current.focus(), 100);
        }

        // Body Scroll Locking
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

    const handleClose = () => setIsOpen(false);

    // Close on Escape key
    useEffect(() => {
        const handleEsc = (e) => {
            if (e.key === 'Escape') setIsOpen(false);
        };
        window.addEventListener('keydown', handleEsc);
        return () => window.removeEventListener('keydown', handleEsc);
    }, []);

    return (
        <AnimatePresence>
            {isOpen && (
                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    style={{
                        position: 'fixed',
                        inset: 0,
                        backgroundColor: 'rgba(8, 11, 16, 0.95)',
                        backdropFilter: 'blur(10px)',
                        zIndex: 9999,
                        overflowY: 'auto',
                        WebkitOverflowScrolling: 'touch'
                    }}
                >
                    <div style={{
                        minHeight: '100%',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        alignItems: 'center',
                        padding: '40px 20px',
                        position: 'relative'
                    }}>
                        <button
                            onClick={handleClose}
                            style={{
                                position: 'absolute',
                                top: '24px',
                                right: '24px',
                                background: 'none',
                                border: 'none',
                                color: '#FFFFFF',
                                fontSize: '32px',
                                cursor: 'pointer',
                                zIndex: 10
                            }}
                        >
                            ✕
                        </button>

                        <form
                            action={window.electionAppData?.homeUrl || "/"}
                            method="get"
                            style={{ width: '100%', maxWidth: '800px', textAlign: 'center' }}
                        >
                            <motion.input
                                ref={inputRef}
                                initial={{ y: 20, opacity: 0 }}
                                animate={{ y: 0, opacity: 1 }}
                                transition={{ delay: 0.1 }}
                                type="search"
                                name="s"
                                placeholder="Type to search..."
                                style={{
                                    width: '100%',
                                    background: 'transparent',
                                    border: 'none',
                                    borderBottom: '2px solid var(--color-electric-yellow)',
                                    color: '#FFFFFF',
                                    fontSize: 'clamp(24px, 5vw, 48px)',
                                    fontFamily: '"Space Grotesk", sans-serif',
                                    fontWeight: '700',
                                    padding: '16px 0',
                                    outline: 'none',
                                    textAlign: 'center'
                                }}
                            />
                            <input type="hidden" name="post_type" value="post" />
                            <motion.p
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                transition={{ delay: 0.2 }}
                                style={{
                                    color: '#94A3B8',
                                    marginTop: '16px',
                                    fontSize: '14px'
                                }}
                            >
                                Press Enter to search
                            </motion.p>

                            <motion.button
                                type="button"
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                transition={{ delay: 0.3 }}
                                onClick={handleClose}
                                style={{
                                    background: 'rgba(255, 255, 255, 0.05)',
                                    border: '1px solid rgba(255, 255, 255, 0.1)',
                                    borderRadius: '50px',
                                    padding: '10px 30px',
                                    color: '#FFFFFF',
                                    marginTop: '30px',
                                    cursor: 'pointer',
                                    fontFamily: '"Space Grotesk", sans-serif',
                                    fontWeight: '600',
                                    fontSize: '14px',
                                    transition: 'all 0.3s ease'
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                                    e.currentTarget.style.borderColor = 'var(--color-electric-yellow)';
                                    e.currentTarget.style.color = 'var(--color-electric-yellow)';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                                    e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                                    e.currentTarget.style.color = '#FFFFFF';
                                }}
                            >
                                Go Back
                            </motion.button>
                        </form>
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    );
};

export default SearchOverlay;
