import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/player-dashboard.js",
                "resources/js/club-dashboard.js",
                "resources/js/video-composer.js",
                "resources/js/react/team-chat.jsx",
                "resources/js/react/videos-feed.jsx",
                "resources/js/react/video-explore.jsx",
                "resources/js/react/video-detail.jsx",
                "resources/css/club.css",
                "resources/css/player.css",
                'resources/js/club-dashboard.js',
                'resources/js/video-composer.js',
                'resources/js/player-dashboard.js',
            ],
            refresh: true,
        }),
    ],
});
