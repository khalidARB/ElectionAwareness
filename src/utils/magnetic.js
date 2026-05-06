import React, { useEffect, useRef } from 'react';
import { motion } from 'framer-motion';

// This function checks for elements with .magnetic-link class 
// and applies a custom magnetic effect using Vanilla JS + Framer Motion API manual control
// or simply enhances them if we were rendering them in React.
// Since links are PHP rendered, we'll use a Vanilla JS hook that applies transforms.

export const initMagneticLinks = () => {
    const links = document.querySelectorAll('.magnetic-link');

    links.forEach(link => {
        link.addEventListener('mousemove', (e) => {
            const rect = link.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            // Move the text slightly towards the cursor
            link.style.transform = `translate(${x * 0.3}px, ${y * 0.5}px)`;
            link.style.display = 'inline-block'; // Ensure transform works
        });

        link.addEventListener('mouseleave', () => {
            link.style.transform = 'translate(0px, 0px)';
        });
    });
};
