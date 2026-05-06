import React from 'react';

const SkeletonPost = ({ type = 'grid' }) => {
    if (type === 'list') {
        return (
            <div className="skeleton-card list">
                <div className="skeleton-thumb"></div>
                <div className="skeleton-content">
                    <div className="skeleton-line short"></div>
                    <div className="skeleton-line title"></div>
                    <div className="skeleton-line"></div>
                </div>
            </div>
        );
    }

    return (
        <div className="skeleton-card grid">
            <div className="skeleton-image"></div>
            <div className="skeleton-content">
                <div className="skeleton-line title"></div>
                <div className="skeleton-line"></div>
                <div className="skeleton-line short"></div>
            </div>
        </div>
    );
};

export default SkeletonPost;
