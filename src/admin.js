import { createRoot } from '@wordpress/element';
import AdminDashboard from './components/AdminDashboard';
import './admin.css';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('election-theme-dashboard');
    if (container) {
        const root = createRoot(container);
        root.render(<AdminDashboard />);
    }
});
