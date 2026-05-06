import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

const TermsAndConditions = ({ initialTitle, initialSections, initialUpdatedDate }) => {
    const [activeId, setActiveId] = useState('acceptance');

    // Default sections if none are provided via props
    const defaultSections = [
        {
            id: 'acceptance',
            title: '1. Acceptance of Terms',
            content: "By accessing 'Election Awareness', you agree to be bound by these Terms of Service. If you disagree with any part of these terms, you may not access our services. We provide political analysis 'as-is' for educational and awareness purposes."
        },
        {
            id: 'modifications',
            title: '2. Modifications to Service',
            content: "We reserve the right to modify or discontinue any part of our website or service without notice. We may also revise these terms at any time. By continuing to use the site, you agree to the latest version of these Terms & Conditions."
        },
        {
            id: 'intellectual-property',
            title: '3. Intellectual Property',
            content: "All content, including text, custom graphics, logo designs, and deep-dive analysis, is the property of Election Awareness unless otherwise stated. Redistribution or commercial use without express written permission is strictly prohibited."
        },
        {
            id: 'user-conduct',
            title: '4. User Conduct & Comments',
            content: "Voters and participants are encouraged to engage. However, we maintain a zero-tolerance policy for hate speech, misinformation, or harassment. We reserve the right to remove any content or user that violates our community standards."
        },
        {
            id: 'disclaimer',
            title: '5. Accuracy of Data',
            content: "While we strive for absolute accuracy in our election coverage, political data is fluid. 'Election Awareness' is not responsible for any decisions made based on the information provided. Always verify with official electoral commissions."
        },
        {
            id: 'governing-law',
            title: '6. Governing Law',
            content: "These terms are governed by the laws of the jurisdiction in which 'Election Awareness' operates. Any disputes shall be resolved in the local courts of that jurisdiction."
        }
    ];

    // Use props if available, otherwise fallback to defaults
    const displayTitle = initialTitle || 'Terms & Conditions';
    const sections = (initialSections && initialSections.length > 0) ? initialSections : defaultSections;
    const lastUpdated = initialUpdatedDate || new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    useEffect(() => {
        const handleScroll = () => {
            const scrollPosition = window.scrollY + 150;

            for (const section of sections) {
                const element = document.getElementById(section.id);
                if (element) {
                    const { offsetTop, offsetHeight } = element;
                    if (scrollPosition >= offsetTop && scrollPosition < offsetTop + offsetHeight) {
                        setActiveId(section.id);
                        break;
                    }
                }
            }
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, [sections]);

    const scrollToSection = (id) => {
        const element = document.getElementById(id);
        if (element) {
            const offset = 100;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
    };

    return (
        <div className="terms-page-wrapper">
            <header className="terms-hero">
                <div className="container">
                    <motion.div
                        initial={{ opacity: 0, y: 30 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="hero-content"
                    >
                        <span className="hero-label">Legal Framework</span>
                        <h1>{displayTitle}</h1>
                        <p className="hero-subtitle">Ensuring transparency and trust in our digital democracy.</p>
                        <p className="hero-updated">Last Updated: {lastUpdated}</p>
                    </motion.div>
                </div>
            </header>

            <div className="terms-body container">
                <div className="terms-grid">
                    <aside className="terms-sidebar">
                        <nav className="terms-nav">
                            <div className="nav-label">Table of Contents</div>
                            <ul>
                                {sections.map((section) => (
                                    <li key={section.id}>
                                        <button
                                            onClick={() => scrollToSection(section.id)}
                                            className={activeId === section.id ? 'active' : ''}
                                        >
                                            <span className="dot"></span>
                                            {section.title}
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </aside>

                    <main className="terms-content">
                        {sections.map((section, index) => (
                            <section
                                key={section.id}
                                id={section.id}
                                className="terms-section"
                            >
                                <motion.div
                                    initial={{ opacity: 0, x: 20 }}
                                    whileInView={{ opacity: 1, x: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ delay: index * 0.1 }}
                                >
                                    <h2>{section.title}</h2>
                                    <div className="section-text">
                                        <div dangerouslySetInnerHTML={{ __html: section.content }} />
                                    </div>

                                    {section.constraint && (
                                        <div className="terms-constraint">
                                            <p><em>Constraint: {section.constraint}</em></p>
                                        </div>
                                    )}

                                    {section.list && (
                                        <ul className="terms-list">
                                            {section.list.map((item, i) => (
                                                <li key={i}>{item}</li>
                                            ))}
                                        </ul>
                                    )}
                                    <div className="section-divider"></div>
                                </motion.div>
                            </section>
                        ))}
                    </main>
                </div>
            </div>
        </div>
    );
};

export default TermsAndConditions;
