import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const privacySections = [
    {
        id: "who-we-are",
        title: "1. Who We Are",
        content: <p>Our website address is: <span className="highlight">https://electionawareness.com</span>. We are a platform dedicated to political awareness and news analysis.</p>
    },
    {
        id: "comments-media",
        title: "2. Comments & Media",
        content: (
            <div className="policy-group">
                <p>When visitors leave comments on the site, we collect the data shown in the comments form, and also the visitor’s IP address and browser user agent string to help spam detection.</p>
                <ul className="details-list">
                    <li><span className="highlight">Gravatar:</span> An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it.</li>
                    <li><span className="highlight">Media Uploads:</span> If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors can download and extract any location data from images on the website.</li>
                </ul>
            </div>
        )
    },
    {
        id: "cookies",
        title: "3. Cookies (The \"Digital Ballot\")",
        content: <p>If you leave a comment on our site you may opt-in to saving your name, email address and website in <span className="highlight">Cookies</span>. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p>
    },
    {
        id: "embedded",
        title: "4. Embedded Content from Other Websites",
        content: <p>Articles on this site may include embedded content (e.g. videos from YouTube, Twitter/X feeds, charts). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website. These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content.</p>
    },
    {
        id: "analytics-newsletter",
        title: "5. Analytics & Newsletter",
        content: (
            <div className="policy-group">
                <p>We use generic analytics tools to understand how our audience engages with our election coverage. This data is anonymized.</p>
                <ul className="details-list">
                    <li><span className="highlight">Newsletter:</span> If you subscribe to our 'Election Awareness' newsletter, your email address is stored securely and used only for sending you updates. We do not sell your email to political parties or third-party advertisers.</li>
                </ul>
            </div>
        )
    },
    {
        id: "data-sharing",
        title: "6. Who We Share Your Data With",
        content: <p>If you request a password reset, your IP address will be included in the reset email. Otherwise, we do not share your <span className="highlight">Personal Data</span> with any third parties.</p>
    },
    {
        id: "data-retention",
        title: "7. How Long We Retain Your Data",
        content: <p>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p>
    },
    {
        id: "your-rights",
        title: "8. Your Rights Over Your Data",
        content: <p>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>
    },
    {
        id: "contact-info",
        title: "9. Contact Information",
        content: <p>For privacy-specific concerns, please contact our Data Protection Officer at: <span className="highlight">seo.electionawareness@gmail.com</span></p>
    }
];

const AccordionItem = ({ title, content, isOpen, onToggle }) => {
    return (
        <div className={`privacy-accordion-item ${isOpen ? 'is-open' : ''}`}>
            <button
                className="privacy-accordion-header"
                onClick={onToggle}
                aria-expanded={isOpen}
            >
                <span className="accordion-title">{title}</span>
                <span className={`accordion-icon ${isOpen ? 'rotate' : ''}`}>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M6 9l6 6 6-6" />
                    </svg>
                </span>
            </button>
            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: "auto", opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3, ease: "easeInOut" }}
                        className="privacy-accordion-content"
                    >
                        <div className="content-inner">
                            {content}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
};

const PrivacyPolicy = ({ initialTitle, initialSubtitle, initialSections, initialUpdatedDate }) => {
    const [openId, setOpenId] = useState(null);

    const toggleAccordion = (id) => {
        setOpenId(openId === id ? null : id);
    };

    // Default Sections Fallback
    const defaultSections = [
        {
            id: "who-we-are",
            title: "1. Who We Are",
            content: "Our website address is: https://electionawareness.com. We are a platform dedicated to political awareness and news analysis."
        },
        {
            id: "comments-media",
            title: "2. Comments & Media",
            content: "When visitors leave comments on the site, we collect the data shown in the comments form, and also the visitor’s IP address and browser user agent string to help spam detection. An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service."
        },
        {
            id: "cookies",
            title: "3. Cookies (The \"Digital Ballot\")",
            content: "If you leave a comment on our site you may opt-in to saving your name, email address and website in Cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment."
        },
        {
            id: "embedded",
            title: "4. Embedded Content from Other Websites",
            content: "Articles on this site may include embedded content (e.g. videos from YouTube, Twitter/X feeds). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website."
        },
        {
            id: "analytics-newsletter",
            title: "5. Analytics & Newsletter",
            content: "We use generic analytics tools to understand audience engagement. If you subscribe to our newsletter, your email address is stored securely and used only for updates."
        },
        {
            id: "data-sharing",
            title: "6. Who We Share Your Data With",
            content: "If you request a password reset, your IP address will be included in the reset email. Otherwise, we do not share your Personal Data with any third parties."
        },
        {
            id: "data-retention",
            title: "7. How Long We Retain Your Data",
            content: "If you leave a comment, the comment and its metadata are retained indefinitely for automatic follow-up approval."
        },
        {
            id: "your-rights",
            title: "8. Your Rights Over Your Data",
            content: "You can request an exported file of the personal data we hold about you, or request that we erase any personal data we hold about you."
        },
        {
            id: "contact-info",
            title: "9. Contact Information",
            content: "For privacy-specific concerns, please contact our Data Protection Officer at: seo.electionawareness@gmail.com"
        }
    ];

    // Dynamic resolution
    const displayTitle = initialTitle || 'Privacy Policy';
    const displaySubtitle = initialSubtitle || 'We value your privacy as much as your vote.';
    const sections = (initialSections && initialSections.length > 0) ? initialSections : defaultSections;
    const effectiveDate = initialUpdatedDate || new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    return (
        <div className="privacy-page-wrapper">
            <header className="privacy-header">
                <div className="privacy-icon-container">
                    <svg className="shield-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <h1 className="privacy-title">{displayTitle}</h1>
                <p className="privacy-subtitle">{displaySubtitle}</p>
                <div className="privacy-badge">
                    Effective Date: {effectiveDate}
                </div>
            </header>

            <div className="privacy-container">
                <div className="privacy-card">
                    {sections.map((section) => (
                        <AccordionItem
                            key={section.id}
                            title={section.title}
                            content={typeof section.content === 'string' ? <div dangerouslySetInnerHTML={{ __html: section.content }} /> : section.content}
                            isOpen={openId === section.id}
                            onToggle={() => toggleAccordion(section.id)}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
};

export default PrivacyPolicy;
