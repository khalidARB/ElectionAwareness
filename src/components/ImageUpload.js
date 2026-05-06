import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';

const ImageUpload = ({ label, value, onChange }) => {
    const [isDragging, setIsDragging] = useState(false);

    const openMediaLibrary = () => {
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('WordPress Media Library is not available.');
            return;
        }

        const mediaFrame = wp.media({
            title: 'Select or Upload Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        mediaFrame.on('select', () => {
            const attachment = mediaFrame.state().get('selection').first().toJSON();
            onChange(attachment.url);
        });

        mediaFrame.open();
    };

    const handleDragOver = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);

        const files = e.dataTransfer?.files;
        if (files && files.length > 0) {
            // Open the media library — WP handles the actual upload
            openMediaLibrary();
        }
    };

    const getFilename = (url) => {
        if (!url) return '';
        try {
            return url.split('/').pop().split('?')[0];
        } catch {
            return url;
        }
    };

    return (
        <div className="img-upload">
            {label && <label className="img-upload__label">{label}</label>}

            {/* Preview state */}
            {value ? (
                <div className="img-upload__preview-card">
                    <div
                        className="img-upload__preview-image"
                        style={{ backgroundImage: `url(${value})` }}
                        onClick={openMediaLibrary}
                        title="Click to replace"
                    />
                    <div className="img-upload__preview-info">
                        <span className="img-upload__filename" title={value}>
                            {getFilename(value)}
                        </span>
                        <div className="img-upload__actions">
                            <button
                                type="button"
                                className="img-upload__btn img-upload__btn--replace"
                                onClick={openMediaLibrary}
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                Replace
                            </button>
                            <button
                                type="button"
                                className="img-upload__btn img-upload__btn--remove"
                                onClick={() => onChange('')}
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="3 6 5 6 21 6" />
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            ) : (
                /* Empty / drop zone state */
                <div
                    className={`img-upload__dropzone ${isDragging ? 'img-upload__dropzone--active' : ''}`}
                    onClick={openMediaLibrary}
                    onDragOver={handleDragOver}
                    onDragLeave={handleDragLeave}
                    onDrop={handleDrop}
                >
                    <div className="img-upload__dropzone-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                    </div>
                    <p className="img-upload__dropzone-text">
                        <strong>Click to upload</strong> or drag and drop
                    </p>
                    <p className="img-upload__dropzone-hint">PNG, JPG, SVG or WebP</p>
                </div>
            )}
        </div>
    );
};

export default ImageUpload;
