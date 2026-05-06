import { useState, useEffect } from '@wordpress/element';
import ImageUpload from './ImageUpload';
import CustomRangeSlider from './CustomRangeSlider';
import { Button, Spinner, Notice, PanelRow, TextControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';



const AdminDashboard = () => {
    const [heroCount, setHeroCount] = useState(3);
    const [tickerCount, setTickerCount] = useState(5);

    // Grid Settings State
    const [gridHeading, setGridHeading] = useState('Latest Stories');
    const [gridCtaText, setGridCtaText] = useState('View All');
    const [gridCtaUrl, setGridCtaUrl] = useState('');
    const [gridCount, setGridCount] = useState(5);

    // CTA Section State
    const [ctaHeading, setCtaHeading] = useState('Join the Awareness Movement');
    const [ctaSubheading, setCtaSubheading] = useState('Stay informed with the latest election updates and deep dives.');
    const [ctaText, setCtaText] = useState('Get Involved');
    const [ctaUrl, setCtaUrl] = useState('/contact');

    // Social Links State
    const [socialLinks, setSocialLinks] = useState([]);

    // All News Settings State
    const [allNewsHeading, setAllNewsHeading] = useState('Latest News');
    const [allNewsCount, setAllNewsCount] = useState(9);
    const [archivePostsCount, setArchivePostsCount] = useState(9);

    // Political Parties Settings State
    const [partiesHeading, setPartiesHeading] = useState('All Political Parties');
    const [partiesSubheading, setPartiesSubheading] = useState('A comprehensive directory of active political groups and their manifestos.');
    const [partiesCount, setPartiesCount] = useState(10);

    // About Page Settings State
    const [headerTitle, setHeaderTitle] = useState('We are the <span class="highlight">voice</span> of clear elections.');
    const [missionLabel, setMissionLabel] = useState('Purpose');
    const [missionTitle, setMissionTitle] = useState('Our Mission');
    const [missionContent, setMissionContent] = useState('At Election Awareness, we believe that informed voters are the bedrock of democracy. Our platform provides unbiased data, real-time analytics, and deep investigative journalism to ensure every citizen has the tools they need to make the right choice.');
    const [missionImage, setMissionImage] = useState('https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?auto=format&fit=crop&q=80&w=1200');

    const [visionLabel, setVisionLabel] = useState('Future');
    const [visionTitle, setVisionTitle] = useState('Our Vision');
    const [visionContent, setVisionContent] = useState('Our vision is a world where every election is conducted with absolute clarity and every vote is cast with confidence. We aspire to be the global standard for electoral intelligence, bridging the gap between complex data and public understanding.');
    const [visionImage, setVisionImage] = useState('https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200');

    // Terms & Conditions Settings State
    const [termsTitle, setTermsTitle] = useState('Terms of Service');
    const [termsSections, setTermsSections] = useState([]);
    const [termsUpdatedDate, setTermsUpdatedDate] = useState('');

    // Privacy Policy Settings State
    const [privacyTitle, setPrivacyTitle] = useState('Privacy Policy');
    const [privacySubtitle, setPrivacySubtitle] = useState('We value your privacy as much as your vote.');
    const [privacySections, setPrivacySections] = useState([]);
    const [privacyUpdatedDate, setPrivacyUpdatedDate] = useState('');

    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [notice, setNotice] = useState(null);
    const [lastSaved, setLastSaved] = useState(null);

    // Tab Navigation State
    const [activeTab, setActiveTab] = useState('home');



    useEffect(() => {
        // Fetch existing setting
        apiFetch({ path: '/wp/v2/settings' })
            .then((settings) => {
                if (settings.election_theme_hero_count) {
                    setHeroCount(parseInt(settings.election_theme_hero_count, 10));
                }
                if (settings.election_theme_ticker_count) {
                    setTickerCount(parseInt(settings.election_theme_ticker_count, 10));
                }

                // Grid Settings Fetch
                if (settings.election_theme_grid_heading) setGridHeading(settings.election_theme_grid_heading);
                if (settings.election_theme_grid_cta_text) setGridCtaText(settings.election_theme_grid_cta_text);
                if (settings.election_theme_grid_cta_url) setGridCtaUrl(settings.election_theme_grid_cta_url);
                if (settings.election_theme_grid_count) setGridCount(parseInt(settings.election_theme_grid_count, 10));

                // CTA Settings Fetch
                if (settings.election_theme_cta_heading) setCtaHeading(settings.election_theme_cta_heading);
                if (settings.election_theme_cta_subheading) setCtaSubheading(settings.election_theme_cta_subheading);
                if (settings.election_theme_cta_text) setCtaText(settings.election_theme_cta_text);
                if (settings.election_theme_cta_url) setCtaUrl(settings.election_theme_cta_url);

                // Social Links Fetch
                if (settings.election_theme_social_links) {
                    try {
                        setSocialLinks(JSON.parse(settings.election_theme_social_links));
                    } catch (e) {
                        setSocialLinks([]);
                    }
                }

                // All News Settings Fetch
                if (settings.election_theme_all_news_heading) setAllNewsHeading(settings.election_theme_all_news_heading);
                if (settings.election_theme_all_news_count) setAllNewsCount(parseInt(settings.election_theme_all_news_count, 10));
                if (settings.election_theme_archive_posts_count) setArchivePostsCount(parseInt(settings.election_theme_archive_posts_count, 10));

                // Political Parties Settings Fetch
                if (settings.election_theme_parties_heading) setPartiesHeading(settings.election_theme_parties_heading);
                if (settings.election_theme_parties_subheading) setPartiesSubheading(settings.election_theme_parties_subheading);
                if (settings.election_theme_parties_count) setPartiesCount(parseInt(settings.election_theme_parties_count, 10));

                // About Page Settings Fetch
                if (settings.election_theme_about_header_title) setHeaderTitle(settings.election_theme_about_header_title);
                if (settings.election_theme_about_mission_label) setMissionLabel(settings.election_theme_about_mission_label);
                if (settings.election_theme_about_mission_title) setMissionTitle(settings.election_theme_about_mission_title);
                if (settings.election_theme_about_mission_content) setMissionContent(settings.election_theme_about_mission_content);
                if (settings.election_theme_about_mission_image) setMissionImage(settings.election_theme_about_mission_image);
                if (settings.election_theme_about_vision_label) setVisionLabel(settings.election_theme_about_vision_label);
                if (settings.election_theme_about_vision_title) setVisionTitle(settings.election_theme_about_vision_title);
                if (settings.election_theme_about_vision_content) setVisionContent(settings.election_theme_about_vision_content);
                if (settings.election_theme_about_vision_image) setVisionImage(settings.election_theme_about_vision_image);

                // Terms & Conditions Fetch
                if (settings.election_theme_terms_title) setTermsTitle(settings.election_theme_terms_title);
                if (settings.election_theme_terms_updated_date) setTermsUpdatedDate(settings.election_theme_terms_updated_date);
                if (settings.election_theme_terms_sections) {
                    try {
                        setTermsSections(JSON.parse(settings.election_theme_terms_sections));
                    } catch (e) {
                        setTermsSections([]);
                    }
                }

                // Privacy Policy Fetch
                if (settings.election_theme_privacy_title) setPrivacyTitle(settings.election_theme_privacy_title);
                if (settings.election_theme_privacy_subtitle) setPrivacySubtitle(settings.election_theme_privacy_subtitle);
                if (settings.election_theme_privacy_updated_date) setPrivacyUpdatedDate(settings.election_theme_privacy_updated_date);
                if (settings.election_theme_privacy_sections) {
                    try {
                        setPrivacySections(JSON.parse(settings.election_theme_privacy_sections));
                    } catch (e) {
                        setPrivacySections([]);
                    }
                }

                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching settings:', error);
                setNotice({ status: 'error', message: 'Failed to load settings.' });
                setIsLoading(false);
            });
    }, []);

    const handleSave = () => {
        setIsSaving(true);
        setNotice(null);

        apiFetch({
            path: '/wp/v2/settings',
            method: 'POST',
            data: {
                election_theme_hero_count: heroCount,
                election_theme_ticker_count: tickerCount,
                election_theme_grid_heading: gridHeading,
                election_theme_grid_cta_text: gridCtaText,
                election_theme_grid_cta_url: gridCtaUrl,
                election_theme_grid_count: gridCount,
                election_theme_cta_heading: ctaHeading,
                election_theme_cta_subheading: ctaSubheading,
                election_theme_cta_text: ctaText,
                election_theme_cta_url: ctaUrl,
                election_theme_social_links: JSON.stringify(socialLinks),
                election_theme_all_news_heading: allNewsHeading,
                election_theme_all_news_count: allNewsCount,
                election_theme_archive_posts_count: archivePostsCount,
                election_theme_parties_heading: partiesHeading,
                election_theme_parties_subheading: partiesSubheading,
                election_theme_parties_count: partiesCount,
                election_theme_about_header_title: headerTitle,
                election_theme_about_mission_label: missionLabel,
                election_theme_about_mission_title: missionTitle,
                election_theme_about_mission_content: missionContent,
                election_theme_about_mission_image: missionImage,
                election_theme_about_vision_label: visionLabel,
                election_theme_about_vision_title: visionTitle,
                election_theme_about_vision_content: visionContent,
                election_theme_about_vision_image: visionImage,
                election_theme_terms_title: termsTitle,
                election_theme_terms_sections: JSON.stringify(termsSections),
                election_theme_terms_updated_date: termsUpdatedDate,
                election_theme_privacy_title: privacyTitle,
                election_theme_privacy_subtitle: privacySubtitle,
                election_theme_privacy_sections: JSON.stringify(privacySections),
                election_theme_privacy_updated_date: privacyUpdatedDate
            },
        })
            .then(() => {
                setNotice({ status: 'success', message: 'Settings saved successfully!' });
                setIsSaving(false);
                setLastSaved(new Date().toLocaleTimeString());
            })
            .catch((error) => {
                console.error('Error saving settings:', error);
                setNotice({ status: 'error', message: 'Failed to save settings.' });
                setIsSaving(false);
            });
    };

    const addSocialLink = () => {
        setSocialLinks([...socialLinks, { platform: 'facebook', url: '' }]);
    };

    const updateSocialLink = (index, key, value) => {
        const newLinks = [...socialLinks];
        newLinks[index][key] = value;
        setSocialLinks(newLinks);
    };

    const removeSocialLink = (index) => {
        const newLinks = socialLinks.filter((_, i) => i !== index);
        setSocialLinks(newLinks);
    };

    const moveSocialLink = (index, direction) => {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= socialLinks.length) return;
        const newLinks = [...socialLinks];
        [newLinks[index], newLinks[newIndex]] = [newLinks[newIndex], newLinks[index]];
        setSocialLinks(newLinks);
    };

    const addTermsSection = () => {
        setTermsSections([...termsSections, { id: `section-${Date.now()}`, title: '', content: '' }]);
    };

    const updateTermsSection = (index, key, value) => {
        const newSections = [...termsSections];
        newSections[index][key] = value;
        setTermsSections(newSections);
    };

    const removeTermsSection = (index) => {
        const newSections = termsSections.filter((_, i) => i !== index);
        setTermsSections(newSections);
    };

    const moveTermsSection = (index, direction) => {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= termsSections.length) return;
        const newSections = [...termsSections];
        [newSections[index], newSections[newIndex]] = [newSections[newIndex], newSections[index]];
        setTermsSections(newSections);
    };

    const addPrivacySection = () => {
        setPrivacySections([...privacySections, { id: `privacy-${Date.now()}`, title: '', content: '' }]);
    };

    const updatePrivacySection = (index, key, value) => {
        const newSections = [...privacySections];
        newSections[index][key] = value;
        setPrivacySections(newSections);
    };

    const removePrivacySection = (index) => {
        const newSections = privacySections.filter((_, i) => i !== index);
        setPrivacySections(newSections);
    };

    const movePrivacySection = (index, direction) => {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= privacySections.length) return;
        const newSections = [...privacySections];
        [newSections[index], newSections[newIndex]] = [newSections[newIndex], newSections[index]];
        setPrivacySections(newSections);
    };

    if (isLoading) {
        return <div style={{ display: 'flex', justifyContent: 'center', padding: '50px' }}><Spinner /></div>;
    }

    const Sidebar = () => {
        const menuItems = [
            { id: 'home', label: 'Home Page', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9 22 9 12 15 12 15 22" /></svg> },
            { id: 'news', label: 'News & Archives', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2" /><path d="M18 14h-8" /><path d="M15 18h-5" /><path d="M10 6h8v4h-8z" /></svg> },
            { id: 'parties', label: 'Parties Directory', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg> },
            { id: 'about', label: 'About Page', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" /></svg> },
            { id: 'terms', label: 'Terms & Conditions', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /><polyline points="10 9 9 9 8 9" /></svg> },
            { id: 'privacy', label: 'Privacy Policy', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg> },
            { id: 'social', label: 'Social Media', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><line x1="8.59" y1="13.51" x2="15.42" y2="17.49" /><line x1="15.41" y1="6.51" x2="8.59" y2="10.49" /></svg> },
        ];

        return (
            <aside className="dashboard-sidebar">
                {menuItems.map(item => (
                    <button
                        key={item.id}
                        className={`sidebar-link ${activeTab === item.id ? 'is-active' : ''}`}
                        onClick={() => setActiveTab(item.id)}
                    >
                        {item.icon}
                        <span>{item.label}</span>
                    </button>
                ))}
            </aside>
        );
    };

    return (
        <div className="election-dashboard-wrapper sidebar-layout">
            <header className="dashboard-header">
                <div className="header-text">
                    <h1 className="dashboard-title">Theme Builder</h1>
                    <span className="dashboard-subtitle">Control Center</span>
                </div>
                <div className="header-actions">
                    <Button className="btn-primary-glow" onClick={handleSave} isBusy={isSaving}>
                        {isSaving ? 'Saving...' : 'Save Changes'}
                    </Button>
                </div>
            </header>

            {notice && (
                <Notice status={notice.status} onRemove={() => setNotice(null)}>
                    {notice.message}
                </Notice>
            )}

            <div className="dashboard-main">
                <Sidebar />

                <div className="dashboard-content">
                    <div className="settings-card">
                        {activeTab === 'social' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Social Media Links</h2>
                                <div className="social-links-manager">
                                    <div className="social-links-manager__header">
                                        <h3 className="social-links-manager__title">Active Links</h3>
                                        <span className="social-links-manager__count">{socialLinks.length} link{socialLinks.length !== 1 ? 's' : ''}</span>
                                    </div>
                                    {socialLinks.map((link, index) => (
                                        <div key={index} className="social-link-card">
                                            <div className="social-link-card__header">
                                                <div className="social-link-card__platform-info">
                                                    <div className={`social-link-card__icon social-link-card__icon--${link.platform}`}>
                                                        {link.platform === 'facebook' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" /></svg>}
                                                        {link.platform === 'instagram' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" /><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" /></svg>}
                                                        {link.platform === 'twitter' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" /></svg>}
                                                        {link.platform === 'youtube' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" /><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" /></svg>}
                                                        {link.platform === 'x' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z" /><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" /></svg>}
                                                        {link.platform === 'tiktok' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" /></svg>}
                                                    </div>
                                                    <span className="social-link-card__platform-name">{link.platform.charAt(0).toUpperCase() + link.platform.slice(1)}</span>
                                                </div>
                                                <div className="social-link-card__header-actions">
                                                    <button type="button" className="social-link-card__move-btn" onClick={() => moveSocialLink(index, -1)} disabled={index === 0} title="Move up">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                                    </button>
                                                    <button type="button" className="social-link-card__move-btn" onClick={() => moveSocialLink(index, 1)} disabled={index === socialLinks.length - 1} title="Move down">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                                                    </button>
                                                    <button type="button" className="social-link-card__delete-btn" onClick={() => removeSocialLink(index)} title="Delete link">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div className="social-link-card__body">
                                                <div className="social-link-card__fields">
                                                    <div className="social-link-card__field social-link-card__field--select">
                                                        <label className="social-link-card__label">Platform</label>
                                                        <select
                                                            value={link.platform}
                                                            onChange={(e) => updateSocialLink(index, 'platform', e.target.value)}
                                                            className="social-link-card__select"
                                                        >
                                                            <option value="facebook">Facebook</option>
                                                            <option value="instagram">Instagram</option>
                                                            <option value="twitter">Twitter</option>
                                                            <option value="youtube">YouTube</option>
                                                            <option value="x">X (formerly LinkedIn)</option>
                                                            <option value="tiktok">TikTok</option>
                                                        </select>
                                                    </div>
                                                    <div className="social-link-card__field social-link-card__field--url">
                                                        <TextControl
                                                            label="Profile URL"
                                                            value={link.url}
                                                            onChange={(value) => updateSocialLink(index, 'url', value)}
                                                            placeholder="https://..."
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    <button type="button" className="social-links-manager__add-btn" onClick={addSocialLink}>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                        Add Social Link
                                    </button>
                                </div>
                            </div>
                        )}

                        {activeTab === 'news' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">All News Page Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <TextControl
                                            label="Page Heading"
                                            value={allNewsHeading}
                                            onChange={(value) => setAllNewsHeading(value)}
                                            help="Title displayed at the top of the 'All News' page."
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px' }}>
                                        <CustomRangeSlider
                                            label="All News Posts Per Page"
                                            help="Number of news posts to display per page on the main blog listing."
                                            value={allNewsCount}
                                            onChange={(value) => setAllNewsCount(value)}
                                            min={1}
                                            max={24}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                        <CustomRangeSlider
                                            label="Archive Posts Per Page"
                                            help="Number of news posts to display per page on Category and Tag archive pages."
                                            value={archivePostsCount}
                                            onChange={(value) => setArchivePostsCount(value)}
                                            min={1}
                                            max={24}
                                        />
                                    </div>
                                </PanelRow>
                            </div>
                        )}

                        {activeTab === 'parties' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Political Parties Page Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <TextControl
                                            label="Page Heading"
                                            value={partiesHeading}
                                            onChange={(value) => setPartiesHeading(value)}
                                            help="Title displayed at the top of the Political Parties page."
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '15px' }}>
                                        <TextControl
                                            label="Page Subheading"
                                            value={partiesSubheading}
                                            onChange={(value) => setPartiesSubheading(value)}
                                            help="Subheading text below the title."
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px' }}>
                                        <CustomRangeSlider
                                            label="Parties Per Page"
                                            help="Number of political parties to display per page."
                                            value={partiesCount}
                                            onChange={(value) => setPartiesCount(value)}
                                            min={1}
                                            max={20}
                                        />
                                    </div>
                                </PanelRow>
                            </div>
                        )}

                        {activeTab === 'home' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Home Page Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <CustomRangeSlider
                                            label="Hero Carousel Post Count"
                                            help="Number of featured posts to display in the main hero slider."
                                            value={heroCount}
                                            onChange={(value) => setHeroCount(value)}
                                            min={1}
                                            max={10}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                        <CustomRangeSlider
                                            label="Trending Ticker Post Count"
                                            help="Number of visible posts in the scrolling ticker."
                                            value={tickerCount}
                                            onChange={(value) => setTickerCount(value)}
                                            min={1}
                                            max={20}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                        <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Latest Stories Grid</h3>
                                        <TextControl
                                            label="Section Heading"
                                            value={gridHeading}
                                            onChange={(value) => setGridHeading(value)}
                                            help="Title displayed above the story grid."
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px' }}>
                                        <CustomRangeSlider
                                            label="Post Count"
                                            help="Number of latest stories to display."
                                            value={gridCount}
                                            onChange={(value) => setGridCount(value)}
                                            min={1}
                                            max={12}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                        <TextControl
                                            label="CTA Button Text"
                                            value={gridCtaText}
                                            onChange={(value) => setGridCtaText(value)}
                                        />
                                        <TextControl
                                            label="CTA Button URL"
                                            value={gridCtaUrl}
                                            onChange={(value) => setGridCtaUrl(value)}
                                            help="Leave empty to link to default blog archive."
                                        />
                                    </div>
                                </PanelRow>

                                {/* CTA Section */}
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                        <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Home CTA Section</h3>
                                        <TextControl
                                            label="Heading"
                                            value={ctaHeading}
                                            onChange={(value) => setCtaHeading(value)}
                                        />
                                        <div style={{ marginTop: '15px' }}>
                                            <TextControl
                                                label="Subheading"
                                                value={ctaSubheading}
                                                onChange={(value) => setCtaSubheading(value)}
                                            />
                                        </div>
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                        <TextControl
                                            label="Button Text"
                                            value={ctaText}
                                            onChange={(value) => setCtaText(value)}
                                        />
                                        <TextControl
                                            label="Button URL"
                                            value={ctaUrl}
                                            onChange={(value) => setCtaUrl(value)}
                                        />
                                    </div>
                                </PanelRow>
                            </div>
                        )}

                        {activeTab === 'about' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">About Page Settings</h2>
                                <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Header Section</h3>
                                <TextControl
                                    label="Page Title (Supports HTML)"
                                    value={headerTitle}
                                    onChange={(value) => setHeaderTitle(value)}
                                    help="Use &lt;span class=&quot;highlight&quot;&gt;text&lt;/span&gt; for the yellow highlight."
                                />

                                <div style={{ marginTop: '30px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Mission Section</h3>
                                    <TextControl
                                        label="Section Label"
                                        value={missionLabel}
                                        onChange={(value) => setMissionLabel(value)}
                                        help="Small text above the heading (e.g., Purpose)"
                                    />
                                    <div style={{ marginTop: '15px' }}>
                                        <TextControl
                                            label="Mission Heading"
                                            value={missionTitle}
                                            onChange={(value) => setMissionTitle(value)}
                                        />
                                    </div>
                                </div>
                                <div style={{ marginTop: '15px' }}>
                                    <label style={{ display: 'block', marginBottom: '5px' }}>Mission Content</label>
                                    <textarea
                                        value={missionContent}
                                        onChange={(e) => setMissionContent(e.target.value)}
                                        rows={10}
                                        className="styled-textarea"
                                    />
                                </div>
                                <div style={{ marginTop: '15px' }}>
                                    <ImageUpload
                                        label="Mission Image"
                                        value={missionImage}
                                        onChange={(value) => setMissionImage(value)}
                                    />
                                </div>

                                <div style={{ marginTop: '30px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Vision Section</h3>
                                    <TextControl
                                        label="Section Label"
                                        value={visionLabel}
                                        onChange={(value) => setVisionLabel(value)}
                                        help="Small text above the heading (e.g., Future)"
                                    />
                                    <div style={{ marginTop: '15px' }}>
                                        <TextControl
                                            label="Vision Heading"
                                            value={visionTitle}
                                            onChange={(value) => setVisionTitle(value)}
                                        />
                                    </div>
                                    <div style={{ marginTop: '15px' }}>
                                        <label style={{ display: 'block', marginBottom: '5px' }}>Vision Content</label>
                                        <textarea
                                            value={visionContent}
                                            onChange={(e) => setVisionContent(e.target.value)}
                                            rows={10}
                                            className="styled-textarea"
                                        />
                                    </div>
                                    <div style={{ marginTop: '15px' }}>
                                        <ImageUpload
                                            label="Vision Image"
                                            value={visionImage}
                                            onChange={(value) => setVisionImage(value)}
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'terms' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Terms & Conditions Page Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <TextControl
                                            label="Main Title"
                                            value={termsTitle}
                                            onChange={(value) => setTermsTitle(value)}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '15px' }}>
                                        <TextControl
                                            label="Last Updated Date"
                                            value={termsUpdatedDate}
                                            onChange={(value) => setTermsUpdatedDate(value)}
                                            help="Format: February 17, 2026"
                                        />
                                    </div>
                                </PanelRow>

                                <div className="sections-manager">
                                    <div className="sections-manager__header">
                                        <h3 className="sections-manager__title">Sections</h3>
                                        <span className="sections-manager__count">{termsSections.length} section{termsSections.length !== 1 ? 's' : ''}</span>
                                    </div>
                                    {termsSections.map((section, index) => (
                                        <div key={section.id || index} className="section-card">
                                            <div className="section-card__header">
                                                <div className="section-card__header-left">
                                                    <span className="section-card__badge">{index + 1}</span>
                                                    <span className="section-card__preview">{section.title || 'Untitled Section'}</span>
                                                </div>
                                                <div className="section-card__header-actions">
                                                    <button type="button" className="section-card__move-btn" onClick={() => moveTermsSection(index, -1)} disabled={index === 0} title="Move up">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__move-btn" onClick={() => moveTermsSection(index, 1)} disabled={index === termsSections.length - 1} title="Move down">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__delete-btn" onClick={() => removeTermsSection(index)} title="Delete section">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div className="section-card__body">
                                                <TextControl
                                                    label="Section Title"
                                                    value={section.title}
                                                    onChange={(value) => updateTermsSection(index, 'title', value)}
                                                />
                                                <div style={{ marginTop: '15px' }}>
                                                    <label className="section-card__field-label">Content <span className="section-card__html-badge">HTML</span></label>
                                                    <textarea
                                                        value={section.content}
                                                        onChange={(e) => updateTermsSection(index, 'content', e.target.value)}
                                                        rows={5}
                                                        className="styled-textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    <button type="button" className="sections-manager__add-btn" onClick={addTermsSection}>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                        Add New Section
                                    </button>
                                </div>
                            </div>
                        )}

                        {activeTab === 'privacy' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Privacy Policy Page Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <TextControl
                                            label="Main Title"
                                            value={privacyTitle}
                                            onChange={(value) => setPrivacyTitle(value)}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '15px' }}>
                                        <TextControl
                                            label="Subtitle"
                                            value={privacySubtitle}
                                            onChange={(value) => setPrivacySubtitle(value)}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '15px' }}>
                                        <TextControl
                                            label="Effective Date"
                                            value={privacyUpdatedDate}
                                            onChange={(value) => setPrivacyUpdatedDate(value)}
                                            help="Format: February 17, 2026"
                                        />
                                    </div>
                                </PanelRow>

                                <div className="sections-manager">
                                    <div className="sections-manager__header">
                                        <h3 className="sections-manager__title">Sections (Accordion)</h3>
                                        <span className="sections-manager__count">{privacySections.length} section{privacySections.length !== 1 ? 's' : ''}</span>
                                    </div>
                                    {privacySections.map((section, index) => (
                                        <div key={section.id || index} className="section-card">
                                            <div className="section-card__header">
                                                <div className="section-card__header-left">
                                                    <span className="section-card__badge">{index + 1}</span>
                                                    <span className="section-card__preview">{section.title || 'Untitled Section'}</span>
                                                </div>
                                                <div className="section-card__header-actions">
                                                    <button type="button" className="section-card__move-btn" onClick={() => movePrivacySection(index, -1)} disabled={index === 0} title="Move up">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__move-btn" onClick={() => movePrivacySection(index, 1)} disabled={index === privacySections.length - 1} title="Move down">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__delete-btn" onClick={() => removePrivacySection(index)} title="Delete section">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div className="section-card__body">
                                                <TextControl
                                                    label="Section Title"
                                                    value={section.title}
                                                    onChange={(value) => updatePrivacySection(index, 'title', value)}
                                                />
                                                <div style={{ marginTop: '15px' }}>
                                                    <label className="section-card__field-label">Content <span className="section-card__html-badge">HTML</span></label>
                                                    <textarea
                                                        value={section.content}
                                                        onChange={(e) => updatePrivacySection(index, 'content', e.target.value)}
                                                        rows={5}
                                                        className="styled-textarea"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    <button type="button" className="sections-manager__add-btn" onClick={addPrivacySection}>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                        Add New Privacy Section
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AdminDashboard;
