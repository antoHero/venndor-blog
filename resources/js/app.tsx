import { createInertiaApp } from '@inertiajs/react';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Every page (welcome, blog-post) builds its own chrome via BlogHeader /
    // BlogFooter — there is no authenticated app shell to wrap pages in.
    layout: () => null,
    strictMode: true,
    progress: {
        color: '#4B5563',
    },
});
