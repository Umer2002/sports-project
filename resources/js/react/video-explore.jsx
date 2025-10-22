import React, { useCallback, useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import {
  DEFAULT_ENDPOINT,
  DEFAULT_SHOW_BASE,
  ExplorePage,
  buildShowUrl,
} from './video-feed-components.jsx';

function VideoCardGrid({ videos, baseShowUrl }) {
  if (!videos?.length) {
    return <div className="text-muted">You haven’t shared any videos yet. Upload one above to get started.</div>;
  }

  return (
    <div className="row g-3">
      {videos.map((video) => {
        const href = buildShowUrl(baseShowUrl, video.id);
        const preview = video.playback_url || video.url;
        const isLive = video.is_live;
        return (
          <div className="col-12 col-sm-6 col-lg-4" key={video.id}>
            <a href={href} className="card h-100 text-decoration-none text-reset">
              <div className="ratio ratio-16x9">
                {isLive ? (
                  <div className="d-flex align-items-center justify-content-center bg-dark text-white">Live Stream</div>
                ) : (
                  <video
                    src={preview}
                    muted
                    playsInline
                    preload="metadata"
                    className="w-100 h-100"
                    style={{ objectFit: 'cover' }}
                  />
                )}
              </div>
              <div className="card-body py-2">
                <h6 className="mb-1 text-truncate" title={video.title}>{video.title}</h6>
                <div className="small text-muted">{String(video.category || video.video_type || 'video').toUpperCase()}</div>
                <div className="small text-muted mt-2">👍 {video.likes_count ?? 0} · 💬 {video.comments_count ?? 0}</div>
              </div>
            </a>
          </div>
        );
      })}
    </div>
  );
}

function useRefreshingList(initial, endpoint, params) {
  const [items, setItems] = useState(initial ?? []);
  const [loading, setLoading] = useState(false);

  const refresh = async () => {
    setLoading(true);
    try {
      const query = new URLSearchParams(params).toString();
      const response = await fetch(`${endpoint}?${query}`, { credentials: 'same-origin' });
      const json = await response.json();
      if (Array.isArray(json.data)) {
        setItems(json.data);
      }
    } catch (error) {
      console.error('Unable to refresh videos', error);
    } finally {
      setLoading(false);
    }
  };

  return { items, loading, refresh, setItems };
}

function VideoExploreApp({
  initialMyVideos,
  initialCommunity,
  endpoint,
  baseShowUrl,
}) {
  const {
    items: myVideos,
    loading: myLoading,
    refresh: refreshMine,
    setItems: setMyVideos,
  } = useRefreshingList(initialMyVideos, endpoint, { limit: 12, scope: 'mine' });
  const [refreshKey, setRefreshKey] = useState(0);
  const [communityLoading, setCommunityLoading] = useState(false);

  useEffect(() => {
    refreshMine();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleVideoUpdate = useCallback((updatedVideo) => {
    setMyVideos((prev) => prev.map((item) => (item.id === updatedVideo.id ? { ...item, ...updatedVideo } : item)));
  }, [setMyVideos]);

  const handleRefreshCommunity = useCallback(() => {
    setCommunityLoading(true);
    setRefreshKey((value) => value + 1);
    refreshMine().finally(() => setCommunityLoading(false));
  }, [refreshMine]);

  return (
    <div className="d-grid gap-3">
      <div className="card border-0 shadow-sm">
        <div className="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>My Recent Uploads</strong>
          <button type="button" className="btn btn-link btn-sm" onClick={refreshMine} disabled={myLoading}>
            {myLoading ? 'Refreshing…' : 'Refresh'}
          </button>
        </div>
        <div className="card-body">
          <VideoCardGrid videos={myVideos} baseShowUrl={baseShowUrl} />
        </div>
      </div>

      <div className="card border-0 shadow-sm">
        <div className="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>Community Feed</strong>
          <button
            type="button"
            className="btn btn-link btn-sm"
            onClick={handleRefreshCommunity}
            disabled={communityLoading}
          >
            {communityLoading ? 'Refreshing…' : 'Refresh'}
          </button>
        </div>
        <div className="card-body p-3">
          <ExplorePage
            key={refreshKey}
            endpoint={endpoint}
            baseShowUrl={baseShowUrl}
            initialVideos={refreshKey === 0 ? initialCommunity : []}
            onVideoUpdate={handleVideoUpdate}
            refreshSignal={refreshKey}
            onLoadingStateChange={setCommunityLoading}
          />
        </div>
      </div>
    </div>
  );
}

function boot() {
  const container = document.getElementById('video-explore-app');
  if (!container) {
    return;
  }
  const {
    myVideos: myVideosAttr,
    communityVideos: communityAttr,
    endpoint,
    baseShowUrl,
  } = container.dataset;
  let initialMyVideos = [];
  let initialCommunity = [];
  try {
    if (myVideosAttr) {
      initialMyVideos = JSON.parse(myVideosAttr);
    }
    if (communityAttr) {
      initialCommunity = JSON.parse(communityAttr);
    }
  } catch (error) {
    console.warn('Failed to parse initial videos data', error);
  }

  createRoot(container).render(
    <VideoExploreApp
      initialMyVideos={initialMyVideos}
      initialCommunity={initialCommunity}
      endpoint={endpoint || DEFAULT_ENDPOINT}
      baseShowUrl={baseShowUrl || DEFAULT_SHOW_BASE}
    />
  );
  const fallback = document.getElementById('video-explore-fallback');
  if (fallback) {
    fallback.remove();
  }
}

boot();
