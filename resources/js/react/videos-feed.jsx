import { createRoot } from 'react-dom/client';
import React from 'react';
import { DEFAULT_ENDPOINT, DEFAULT_SHOW_BASE, ExplorePage, LatestFiveWidget } from './video-feed-components.jsx';

function boot() {
  const exploreRoot = document.getElementById('react-videos-root');
  if (exploreRoot) {
    const { endpoint, baseShowUrl } = exploreRoot.dataset;
    createRoot(exploreRoot).render(
      <ExplorePage
        endpoint={endpoint || DEFAULT_ENDPOINT}
        baseShowUrl={baseShowUrl || DEFAULT_SHOW_BASE}
      />
    );
    const fallback = document.getElementById('react-videos-fallback');
    if (fallback) {
      fallback.remove();
    }
  }
  const latestRoot = document.getElementById('latest-videos-react');
  if (latestRoot) {
    const { endpoint, baseShowUrl } = latestRoot.dataset || {};
    createRoot(latestRoot).render(
      <LatestFiveWidget
        endpoint={endpoint || DEFAULT_ENDPOINT}
        baseShowUrl={baseShowUrl || DEFAULT_SHOW_BASE}
      />
    );
  }
}

boot();
