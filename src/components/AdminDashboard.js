import { useState, useEffect } from '@wordpress/element';
import ImageUpload from './ImageUpload';
import CustomRangeSlider from './CustomRangeSlider';
import { Button, Spinner, Notice, PanelRow, TextControl, ToggleControl } from '@wordpress/components';
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
    const [aboutSections, setAboutSections] = useState([]);

    // Terms & Conditions Settings State
    const [termsTitle, setTermsTitle] = useState('Terms of Service');
    const [termsSections, setTermsSections] = useState([]);
    const [termsUpdatedDate, setTermsUpdatedDate] = useState('');

    // Privacy Policy Settings State
    const [privacyTitle, setPrivacyTitle] = useState('Privacy Policy');
    const [privacySubtitle, setPrivacySubtitle] = useState('We value your privacy as much as your vote.');
    const [privacySections, setPrivacySections] = useState([]);
    const [privacyUpdatedDate, setPrivacyUpdatedDate] = useState('');

    // Newsletter Settings State
    const [newsHeading, setNewsHeading] = useState('Join our Newsletter');
    const [newsSubheading, setNewsSubheading] = useState('Get the latest election updates and deep dives directly in your inbox.');
    const [newsBtnText, setNewsBtnText] = useState('Subscribe Now');
    const [newsPlaceholder, setNewsPlaceholder] = useState('Your email address');

    // Products Page Settings State
    const [productsHeading, setProductsHeading] = useState('Awareness Gear');
    const [productsSubtitle, setProductsSubtitle] = useState('Support the movement with our official merchandise and premium reports.');
    const [productsEmptyText, setProductsEmptyText] = useState('No products available yet. Check back soon!');
    const [productsCtaText, setProductsCtaText] = useState('Call to Buy');
    const [productsGlobalPhone, setProductsGlobalPhone] = useState('');

    // Subscribers State
    const [subscribers, setSubscribers] = useState([]);
    const [isFetchingSubs, setIsFetchingSubs] = useState(false);

    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [notice, setNotice] = useState(null);
    const [lastSaved, setLastSaved] = useState(null);

    // Push Notifications State
    const [expoAccessToken, setExpoAccessToken] = useState('');
    const [enableAutoPush, setEnableAutoPush] = useState(true);
    const [manualPushTitle, setManualPushTitle] = useState('');
    const [manualPushBody, setManualPushBody] = useState('');
    const [isSendingPush, setIsSendingPush] = useState(false);
    const [pushTokens, setPushTokens] = useState([]);
    const [isFetchingTokens, setIsFetchingTokens] = useState(false);

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
                if (settings.election_theme_about_sections) {
                    try {
                        setAboutSections(JSON.parse(settings.election_theme_about_sections));
                    } catch (e) {
                        setAboutSections([]);
                    }
                }

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

                // Newsletter Fetch
                if (settings.election_theme_newsletter_heading) setNewsHeading(settings.election_theme_newsletter_heading);
                if (settings.election_theme_newsletter_subheading) setNewsSubheading(settings.election_theme_newsletter_subheading);
                if (settings.election_theme_newsletter_btn_text) setNewsBtnText(settings.election_theme_newsletter_btn_text);
                if (settings.election_theme_newsletter_placeholder) setNewsPlaceholder(settings.election_theme_newsletter_placeholder);

                // Products Settings Fetch
                if (settings.election_theme_products_heading) setProductsHeading(settings.election_theme_products_heading);
                if (settings.election_theme_products_subtitle) setProductsSubtitle(settings.election_theme_products_subtitle);
                if (settings.election_theme_products_empty_text) setProductsEmptyText(settings.election_theme_products_empty_text);
                if (settings.election_theme_products_cta_text) setProductsCtaText(settings.election_theme_products_cta_text);
                if (settings.election_theme_products_global_phone) setProductsGlobalPhone(settings.election_theme_products_global_phone);

                // Push Settings Fetch
                if (settings.election_theme_expo_access_token) setExpoAccessToken(settings.election_theme_expo_access_token);
                if (settings.hasOwnProperty('election_theme_enable_auto_push')) {
                    setEnableAutoPush(!!settings.election_theme_enable_auto_push);
                }

                // Fetch Subscribers
                fetchSubscribers();

                // Fetch Push Tokens
                fetchPushTokens();

                setIsLoading(false);
            })
            .catch((error) => {
                console.error('Error fetching settings:', error);
                setNotice({ status: 'error', message: 'Failed to load settings.' });
                setIsLoading(false);
            });
    }, []);

    const fetchSubscribers = () => {
        setIsFetchingSubs(true);
        console.log('Fetching subscribers from: /election-awareness/v1/subscribers');
        apiFetch({ path: '/election-awareness/v1/subscribers' })
            .then((data) => {
                console.log('Subscribers fetched successfully:', data);
                setSubscribers(Array.isArray(data) ? data : []);
                setIsFetchingSubs(false);
            })
            .catch((error) => {
                console.error('Error fetching subscribers:', error);
                setIsFetchingSubs(false);
                // Optionally set a notice for the subscriber list
            });
    };

    const handleDeleteSubscriber = (id) => {
        if (!confirm('Are you sure you want to delete this subscriber?')) return;

        apiFetch({
            path: `/election-awareness/v1/subscribers/${id}`,
            method: 'DELETE',
        })
            .then(() => {
                setNotice({ status: 'success', message: 'Subscriber deleted successfully.' });
                fetchSubscribers(); // Refresh list
            })
            .catch((error) => {
                console.error('Error deleting subscriber:', error);
                const errorMsg = error.message || 'Failed to delete subscriber.';
                setNotice({ status: 'error', message: errorMsg });
            });
    };

    const fetchPushTokens = () => {
        setIsFetchingTokens(true);
        console.log('Fetching push tokens from: /election/v1/push-tokens');
        apiFetch({ path: '/election/v1/push-tokens' })
            .then((data) => {
                console.log('Push tokens fetched successfully:', data);
                setPushTokens(Array.isArray(data) ? data : []);
                setIsFetchingTokens(false);
            })
            .catch((error) => {
                console.error('Error fetching push tokens:', error);
                setIsFetchingTokens(false);
            });
    };

    const handleDeletePushToken = (id) => {
        if (!confirm('Are you sure you want to delete this push token?')) return;

        apiFetch({
            path: `/election/v1/push-tokens/${id}`,
            method: 'DELETE',
        })
            .then(() => {
                setNotice({ status: 'success', message: 'Push token deleted successfully.' });
                fetchPushTokens(); // Refresh list
            })
            .catch((error) => {
                console.error('Error deleting push token:', error);
                const errorMsg = error.message || 'Failed to delete push token.';
                setNotice({ status: 'error', message: errorMsg });
            });
    };

    const handleSendManualPush = () => {
        if (!manualPushTitle || !manualPushBody) {
            alert('Please fill out both Title and Body.');
            return;
        }

        setIsSendingPush(true);
        setNotice(null);

        apiFetch({
            path: '/election/v1/send-manual-push',
            method: 'POST',
            data: {
                title: manualPushTitle,
                body: manualPushBody,
            },
        })
            .then((res) => {
                setIsSendingPush(false);
                if (res.success) {
                    setNotice({ status: 'success', message: res.message });
                    setManualPushTitle('');
                    setManualPushBody('');
                } else {
                    const errorMsg = res.errors && res.errors.length > 0 ? res.errors.join(', ') : res.message;
                    setNotice({ status: 'warning', message: `Partially failed: ${errorMsg}` });
                }
            })
            .catch((error) => {
                console.error('Error sending manual push notification:', error);
                const errorMsg = error.message || 'Failed to send manual push notification.';
                setNotice({ status: 'error', message: errorMsg });
                setIsSendingPush(false);
            });
    };

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
                election_theme_about_sections: JSON.stringify(aboutSections),
                election_theme_terms_title: termsTitle,
                election_theme_terms_sections: JSON.stringify(termsSections),
                election_theme_terms_updated_date: termsUpdatedDate,
                election_theme_privacy_title: privacyTitle,
                election_theme_privacy_subtitle: privacySubtitle,
                election_theme_privacy_sections: JSON.stringify(privacySections),
                election_theme_privacy_updated_date: privacyUpdatedDate,
                election_theme_newsletter_heading: newsHeading,
                election_theme_newsletter_subheading: newsSubheading,
                election_theme_newsletter_btn_text: newsBtnText,
                election_theme_newsletter_placeholder: newsPlaceholder,
                election_theme_products_heading: productsHeading,
                election_theme_products_subtitle: productsSubtitle,
                election_theme_products_empty_text: productsEmptyText,
                election_theme_products_cta_text: productsCtaText,
                election_theme_products_global_phone: productsGlobalPhone,
                election_theme_expo_access_token: expoAccessToken,
                election_theme_enable_auto_push: enableAutoPush
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

    const addAboutSection = () => {
        setAboutSections([...aboutSections, { 
            id: `about-${Date.now()}`, 
            label: '', 
            title: '', 
            content: '', 
            image: '', 
            imagePosition: 'right' 
        }]);
    };

    const updateAboutSection = (index, key, value) => {
        const newSections = [...aboutSections];
        newSections[index][key] = value;
        setAboutSections(newSections);
    };

    const removeAboutSection = (index) => {
        const newSections = aboutSections.filter((_, i) => i !== index);
        setAboutSections(newSections);
    };

    const moveAboutSection = (index, direction) => {
        const newIndex = index + direction;
        if (newIndex < 0 || newIndex >= aboutSections.length) return;
        const newSections = [...aboutSections];
        [newSections[index], newSections[newIndex]] = [newSections[newIndex], newSections[index]];
        setAboutSections(newSections);
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
            { id: 'products', label: 'Products Page', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> },
            { id: 'newsletter', label: 'Newsletter', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 17a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.5C2 7 4 5 6.5 5H17.5C20 5 22 7 22 9.5V17z" /><path d="M2 9l10 7 10-7" /></svg> },
            { id: 'push', label: 'Push Notifications', icon: <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg> },
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
                        {activeTab === 'push' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Push Notifications Settings</h2>

                                <div className="settings-group" style={{ marginBottom: '40px' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Configuration</h3>
                                    <PanelRow style={{ marginBottom: '20px' }}>
                                        <div style={{ width: '100%' }}>
                                            <ToggleControl
                                                label="Automatic Push Notifications on Publish"
                                                help="Automatically send a push notification to all users when a new news post is published."
                                                checked={enableAutoPush}
                                                onChange={(val) => setEnableAutoPush(val)}
                                            />
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <div style={{ width: '100%' }}>
                                            <TextControl
                                                label="Expo Push Access Token"
                                                value={expoAccessToken}
                                                onChange={(val) => setExpoAccessToken(val)}
                                                placeholder="Ex: ExT-..."
                                                help="Optional. Paste your Expo Push Access Token if you have access token authentication enabled for your Expo project."
                                            />
                                        </div>
                                    </PanelRow>
                                </div>

                                <div className="settings-group" style={{ paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)', marginBottom: '40px' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Send Manual Push Notification</h3>
                                    
                                    <PanelRow style={{ display: 'grid', gridTemplateColumns: '1fr', gap: '15px' }}>
                                        <TextControl
                                            label="Notification Title"
                                            value={manualPushTitle}
                                            onChange={(val) => setManualPushTitle(val)}
                                            placeholder="Compose a catchy title..."
                                        />
                                        <TextControl
                                            label="Notification Body / Message"
                                            value={manualPushBody}
                                            onChange={(val) => setManualPushBody(val)}
                                            placeholder="Type your message details here..."
                                        />
                                        
                                        <div style={{ marginTop: '10px' }}>
                                            <Button 
                                                onClick={handleSendManualPush} 
                                                disabled={isSendingPush || !manualPushTitle || !manualPushBody}
                                                style={{
                                                    background: 'linear-gradient(135deg, #FACC15 0%, #EAB308 100%)',
                                                    color: '#000',
                                                    borderRadius: '8px',
                                                    padding: '10px 24px',
                                                    height: 'auto',
                                                    fontSize: '0.9rem',
                                                    fontWeight: '700',
                                                    transition: 'all 0.3s ease',
                                                    cursor: 'pointer',
                                                    border: 'none',
                                                    boxShadow: '0 4px 15px rgba(250, 204, 21, 0.2)',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '8px'
                                                }}
                                                onMouseOver={(e) => {
                                                    e.currentTarget.style.transform = 'translateY(-2px)';
                                                    e.currentTarget.style.boxShadow = '0 6px 20px rgba(250, 204, 21, 0.4)';
                                                }}
                                                onMouseOut={(e) => {
                                                    e.currentTarget.style.transform = 'none';
                                                    e.currentTarget.style.boxShadow = '0 4px 15px rgba(250, 204, 21, 0.2)';
                                                }}
                                            >
                                                {isSendingPush ? <Spinner /> : (
                                                    <>
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><line x1="22" y1="2" x2="11" y2="13" /><polygon points="22 2 15 22 11 13 2 9 22 2" /></svg>
                                                        Send Notification
                                                    </>
                                                )}
                                            </Button>
                                        </div>
                                    </PanelRow>
                                </div>

                                <div style={{ paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                                        <h3 style={{ fontSize: '1.2rem', color: '#fff', margin: 0 }}>Registered Devices / Tokens</h3>
                                        <Button 
                                            onClick={fetchPushTokens} 
                                            disabled={isFetchingTokens}
                                            style={{
                                                background: 'rgba(250, 204, 21, 0.1)',
                                                border: '1px solid #FACC15',
                                                color: '#FACC15',
                                                borderRadius: '8px',
                                                padding: '8px 16px',
                                                height: 'auto',
                                                fontSize: '0.85rem',
                                                fontWeight: '600',
                                                transition: 'all 0.3s ease',
                                                cursor: 'pointer',
                                                boxShadow: '0 0 10px rgba(250, 204, 21, 0.1)',
                                                display: 'flex',
                                                alignItems: 'center',
                                                gap: '8px'
                                            }}
                                            onMouseOver={(e) => {
                                                e.currentTarget.style.background = '#FACC15';
                                                e.currentTarget.style.color = '#000';
                                                e.currentTarget.style.boxShadow = '0 0 20px rgba(250, 204, 21, 0.4)';
                                            }}
                                            onMouseOut={(e) => {
                                                e.currentTarget.style.background = 'rgba(250, 204, 21, 0.1)';
                                                e.currentTarget.style.color = '#FACC15';
                                                e.currentTarget.style.boxShadow = '0 0 10px rgba(250, 204, 21, 0.1)';
                                            }}
                                        >
                                            {isFetchingTokens ? <Spinner /> : (
                                                <>
                                                    <span className="dashicons dashicons-update" style={{ fontSize: '16px', width: '16px', height: '16px' }}></span>
                                                    Refresh List
                                                </>
                                            )}
                                        </Button>
                                    </div>

                                    {pushTokens.length === 0 ? (
                                        <div style={{ padding: '30px', background: 'rgba(255,255,255,0.02)', borderRadius: '12px', textAlign: 'center', color: 'rgba(255,255,255,0.4)' }}>
                                            No devices registered yet.
                                        </div>
                                    ) : (
                                        <div style={{ maxHeight: '400px', overflowY: 'auto', background: 'rgba(0,0,0,0.2)', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.05)' }}>
                                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.9rem' }}>
                                                <thead>
                                                    <tr style={{ textAlign: 'left', borderBottom: '1px solid rgba(255,255,255,0.1)' }}>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>Device / Platform</th>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>Expo Token</th>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>User / Email</th>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>Registered At</th>
                                                        <th style={{ padding: '15px', color: '#FACC15', textAlign: 'right' }}>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {pushTokens.map((token, idx) => (
                                                        <tr key={token.id || idx} style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}>
                                                            <td style={{ padding: '12px 15px', color: '#fff' }}>
                                                                <strong style={{ display: 'block' }}>{token.device_name || 'Unknown Device'}</strong>
                                                                <span style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.4)' }}>{token.platform || 'Unknown Platform'}</span>
                                                            </td>
                                                            <td style={{ padding: '12px 15px', color: '#fff', fontFamily: 'monospace', fontSize: '0.8rem' }}>
                                                                {token.expo_token || 'N/A'}
                                                            </td>
                                                            <td style={{ padding: '12px 15px', color: '#fff' }}>
                                                                {token.user_id ? (
                                                                    <>
                                                                        <span style={{ display: 'block' }}>{token.display_name || `User ID: ${token.user_id}`}</span>
                                                                        <span style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.4)' }}>{token.user_email}</span>
                                                                    </>
                                                                ) : (
                                                                    <span style={{ color: 'rgba(255,255,255,0.4)' }}>Guest</span>
                                                                )}
                                                            </td>
                                                            <td style={{ padding: '12px 15px', color: 'rgba(255,255,255,0.5)' }}>
                                                                {new Date(token.created_at).toLocaleDateString()}
                                                            </td>
                                                            <td style={{ padding: '12px 15px', textAlign: 'right' }}>
                                                                <Button 
                                                                    isDestructive 
                                                                    isSmall 
                                                                    onClick={() => handleDeletePushToken(token.id)}
                                                                >
                                                                    Remove
                                                                </Button>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

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
                                                        {(link.platform === 'twitter' || link.platform === 'x') && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z" /><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" /></svg>}
                                                        {link.platform === 'youtube' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" /><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" /></svg>}
                                                        {link.platform === 'linkedin' && <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.23 0H1.77C.8 0 0 .77 0 1.72v20.56C0 23.23.8 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.72V1.72C24 .77 23.2 0 22.23 0zM7.12 20.45H3.56V9h3.56v11.45zM5.34 7.58c-1.14 0-2.06-.93-2.06-2.06 0-1.14.92-2.06 2.06-2.06 1.14 0 2.06.92 2.06 2.06 0 1.13-.92 2.06-2.06 2.06zM20.45 20.45h-3.56v-5.6c0-1.34-.03-3.06-1.87-3.06-1.87 0-2.15 1.46-2.15 2.96v5.7h-3.56V9h3.42v1.56h.05c.48-.9 1.63-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29z"/></svg>}
                                                        {link.platform === 'tiktok' && <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" /></svg>}
                                                    </div>
                                                    <span className="social-link-card__platform-name">{(link.platform === 'x' || link.platform === 'twitter') ? 'X' : link.platform.charAt(0).toUpperCase() + link.platform.slice(1)}</span>
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
                                                            <option value="x">X (Twitter)</option>
                                                            <option value="linkedin">LinkedIn</option>
                                                            <option value="youtube">YouTube</option>
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

                        {activeTab === 'products' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Products Page Settings</h2>
                                
                                <div className="settings-group" style={{ marginBottom: '40px' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Header & Content</h3>
                                    <PanelRow>
                                        <div style={{ width: '100%' }}>
                                            <TextControl
                                                label="Page Heading"
                                                value={productsHeading}
                                                onChange={(val) => setProductsHeading(val)}
                                            />
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <div style={{ width: '100%', marginTop: '15px' }}>
                                            <TextControl
                                                label="Page Subtitle"
                                                value={productsSubtitle}
                                                onChange={(val) => setProductsSubtitle(val)}
                                            />
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <div style={{ width: '100%', marginTop: '15px' }}>
                                            <TextControl
                                                label="Empty State Message"
                                                value={productsEmptyText}
                                                onChange={(val) => setProductsEmptyText(val)}
                                                help="Text shown when no products are published."
                                            />
                                        </div>
                                    </PanelRow>
                                </div>

                                <div className="settings-group" style={{ paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <h3 style={{ fontSize: '1.1rem', marginBottom: '20px', color: '#fff' }}>Button & Fallback</h3>
                                    <PanelRow>
                                        <div style={{ width: '100%' }}>
                                            <TextControl
                                                label="Button Text"
                                                value={productsCtaText}
                                                onChange={(val) => setProductsCtaText(val)}
                                                help='Default is "Call to Buy"'
                                            />
                                        </div>
                                    </PanelRow>
                                    <PanelRow>
                                        <div style={{ width: '100%', marginTop: '15px' }}>
                                            <TextControl
                                                label="WhatsApp Number"
                                                value={productsGlobalPhone}
                                                onChange={(val) => setProductsGlobalPhone(val)}
                                                help="Include country code (e.g., 88017...). Fallback if per-product number is missing."
                                            />
                                        </div>
                                    </PanelRow>
                                </div>
                            </div>
                        )}
                {activeTab === 'newsletter' && (
                            <div className="settings-section">
                                <h2 className="settings-section-title">Newsletter Section Settings</h2>
                                <PanelRow>
                                    <div style={{ width: '100%' }}>
                                        <TextControl
                                            label="Section Heading"
                                            value={newsHeading}
                                            onChange={(value) => setNewsHeading(value)}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '15px' }}>
                                        <TextControl
                                            label="Section Subheading"
                                            value={newsSubheading}
                                            onChange={(value) => setNewsSubheading(value)}
                                        />
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div style={{ width: '100%', marginTop: '20px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                        <TextControl
                                            label="Button Text"
                                            value={newsBtnText}
                                            onChange={(value) => setNewsBtnText(value)}
                                        />
                                        <TextControl
                                            label="Input Placeholder"
                                            value={newsPlaceholder}
                                            onChange={(value) => setNewsPlaceholder(value)}
                                        />
                                    </div>
                                </PanelRow>

                                <div style={{ marginTop: '40px', paddingTop: '30px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                                        <h3 style={{ fontSize: '1.2rem', color: '#fff', margin: 0 }}>Recent Subscribers</h3>
                                        <Button 
                                            onClick={fetchSubscribers} 
                                            disabled={isFetchingSubs}
                                            style={{
                                                background: 'rgba(250, 204, 21, 0.1)',
                                                border: '1px solid #FACC15',
                                                color: '#FACC15',
                                                borderRadius: '8px',
                                                padding: '8px 16px',
                                                height: 'auto',
                                                fontSize: '0.85rem',
                                                fontWeight: '600',
                                                transition: 'all 0.3s ease',
                                                cursor: 'pointer',
                                                boxShadow: '0 0 10px rgba(250, 204, 21, 0.1)',
                                                display: 'flex',
                                                alignItems: 'center',
                                                gap: '8px'
                                            }}
                                            onMouseOver={(e) => {
                                                e.currentTarget.style.background = '#FACC15';
                                                e.currentTarget.style.color = '#000';
                                                e.currentTarget.style.boxShadow = '0 0 20px rgba(250, 204, 21, 0.4)';
                                            }}
                                            onMouseOut={(e) => {
                                                e.currentTarget.style.background = 'rgba(250, 204, 21, 0.1)';
                                                e.currentTarget.style.color = '#FACC15';
                                                e.currentTarget.style.boxShadow = '0 0 10px rgba(250, 204, 21, 0.1)';
                                            }}
                                        >
                                            {isFetchingSubs ? <Spinner /> : (
                                                <>
                                                    <span className="dashicons dashicons-update" style={{ fontSize: '16px', width: '16px', height: '16px' }}></span>
                                                    Refresh List
                                                </>
                                            )}
                                        </Button>
                                    </div>

                                    {subscribers.length === 0 ? (
                                        <div style={{ padding: '30px', background: 'rgba(255,255,255,0.02)', borderRadius: '12px', textAlign: 'center', color: 'rgba(255,255,255,0.4)' }}>
                                            No subscribers yet.
                                        </div>
                                    ) : (
                                        <div style={{ maxHeight: '400px', overflowY: 'auto', background: 'rgba(0,0,0,0.2)', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.05)' }}>
                                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.9rem' }}>
                                                <thead>
                                                    <tr style={{ textAlign: 'left', borderBottom: '1px solid rgba(255,255,255,0.1)' }}>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>Email Address</th>
                                                        <th style={{ padding: '15px', color: '#FACC15' }}>Joined Date</th>
                                                        <th style={{ padding: '15px', color: '#FACC15', textAlign: 'right' }}>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {subscribers.map((sub, idx) => (
                                                        <tr key={sub.id || idx} style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}>
                                                            <td style={{ padding: '12px 15px', color: '#fff' }}>{sub.email}</td>
                                                            <td style={{ padding: '12px 15px', color: 'rgba(255,255,255,0.5)' }}>{new Date(sub.created_at).toLocaleDateString()}</td>
                                                            <td style={{ padding: '12px 15px', textAlign: 'right' }}>
                                                                <Button 
                                                                    isDestructive 
                                                                    isSmall 
                                                                    onClick={() => handleDeleteSubscriber(sub.id)}
                                                                >
                                                                    Delete
                                                                </Button>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    )}
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

                                <div className="sections-manager" style={{ marginTop: '30px', paddingTop: '20px', borderTop: '1px solid rgba(255,255,255,0.05)' }}>
                                    <div className="sections-manager__header">
                                        <h3 className="sections-manager__title">About Page Sections</h3>
                                        <span className="sections-manager__count">{aboutSections.length} section{aboutSections.length !== 1 ? 's' : ''}</span>
                                    </div>
                                    
                                    {aboutSections.map((section, index) => (
                                        <div key={section.id || index} className="section-card">
                                            <div className="section-card__header">
                                                <div className="section-card__header-left">
                                                    <span className="section-card__badge">{index + 1}</span>
                                                    <span className="section-card__preview">{section.title || 'Untitled Section'}</span>
                                                </div>
                                                <div className="section-card__header-actions">
                                                    <button type="button" className="section-card__move-btn" onClick={() => moveAboutSection(index, -1)} disabled={index === 0} title="Move up">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__move-btn" onClick={() => moveAboutSection(index, 1)} disabled={index === aboutSections.length - 1} title="Move down">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                                                    </button>
                                                    <button type="button" className="section-card__delete-btn" onClick={() => removeAboutSection(index)} title="Delete section">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div className="section-card__body">
                                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginBottom: '15px' }}>
                                                    <TextControl
                                                        label="Section Label"
                                                        value={section.label}
                                                        onChange={(value) => updateAboutSection(index, 'label', value)}
                                                        placeholder="e.g. Purpose"
                                                    />
                                                    <TextControl
                                                        label="Section Title"
                                                        value={section.title}
                                                        onChange={(value) => updateAboutSection(index, 'title', value)}
                                                        placeholder="e.g. Our Mission"
                                                    />
                                                </div>
                                                
                                                <div style={{ marginBottom: '15px' }}>
                                                    <label className="section-card__field-label">Content (Supports HTML)</label>
                                                    <textarea
                                                        value={section.content}
                                                        onChange={(e) => updateAboutSection(index, 'content', e.target.value)}
                                                        rows={5}
                                                        className="styled-textarea"
                                                    />
                                                </div>

                                                <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: '15px', alignItems: 'end' }}>
                                                    <ImageUpload
                                                        label="Section Image"
                                                        value={section.image}
                                                        onChange={(value) => updateAboutSection(index, 'image', value)}
                                                    />
                                                    <div className="social-link-card__field">
                                                        <label className="social-link-card__label">Image Position</label>
                                                        <select
                                                            value={section.imagePosition}
                                                            onChange={(e) => updateAboutSection(index, 'imagePosition', e.target.value)}
                                                            className="social-link-card__select"
                                                            style={{ width: '100%' }}
                                                        >
                                                            <option value="left">Left</option>
                                                            <option value="right">Right</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    
                                    <button type="button" className="sections-manager__add-btn" onClick={addAboutSection}>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                                        Add About Section
                                    </button>
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
