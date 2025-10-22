import React, { useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { buildShowUrl } from './video-feed-components.jsx';

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.getAttribute('content') ?? '';
}

function classNames(...values) {
  return values.filter(Boolean).join(' ');
}

function ensureLivePlayback(videoElement, src, isLive) {
  if (!videoElement || !src) {
    return () => {};
  }

  if (!isLive) {
    videoElement.src = src;
    return () => {};
  }

  if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
    videoElement.src = src;
    return () => {};
  }

  let destroyed = false;
  let hlsInstance = null;

  function attach() {
    if (destroyed) return;
    const { Hls } = window;
    if (Hls?.isSupported()) {
      hlsInstance = new Hls();
      hlsInstance.loadSource(src);
      hlsInstance.attachMedia(videoElement);
    }
  }

  if (window.Hls) {
    attach();
  } else {
    const existing = document.querySelector('script[data-hls]');
    if (existing) {
      existing.addEventListener('load', attach, { once: true });
    } else {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
      script.async = true;
      script.dataset.hls = 'true';
      script.addEventListener('load', attach, { once: true });
      document.body.appendChild(script);
    }
  }

  return () => {
    destroyed = true;
    if (hlsInstance) {
      hlsInstance.destroy();
    }
  };
}

function useAsyncAction() {
  const [loading, setLoading] = useState(false);
  const run = async (fn) => {
    if (loading) return;
    setLoading(true);
    try {
      await fn();
    } finally {
      setLoading(false);
    }
  };
  return [loading, run];
}

function Comment({ comment }) {
  return (
    <div className="story-comment">
      <div className="story-comment-header">
        <span>{comment.name}</span>
        <span className="story-comment-meta" title={comment.created_at_exact || undefined}>{comment.created_at}</span>
      </div>
      <p className="story-comment-body">{comment.content}</p>
    </div>
  );
}

function RelatedVideoCard({ video, baseShowUrl }) {
  const href = buildShowUrl(baseShowUrl, video.id);
  const preview = video.playback_url || video.url;
  const isLive = video.is_live;
  return (
    <a href={href} className="card border-0 shadow-sm text-reset text-decoration-none">
      <div className="row g-0">
        <div className="col-5">
          <div className="ratio ratio-16x9">
            {isLive ? (
              <div className="d-flex align-items-center justify-content-center bg-dark text-white">Live</div>
            ) : (
              <video src={preview} muted playsInline preload="metadata" className="w-100 h-100" style={{ objectFit: 'cover' }} />
            )}
          </div>
        </div>
        <div className="col-7">
          <div className="p-2">
            <div className="fw-semibold text-truncate" title={video.title}>{video.title || 'Video'}</div>
            <div className="small text-muted text-truncate">{String(video.category || video.video_type || 'video').toUpperCase()}</div>
            <div className="small text-muted mt-1">{video.created_at_human || ''}</div>
          </div>
        </div>
      </div>
    </a>
  );
}

function VideoDetailApp({ initialVideo, relatedVideos, endpoints, showBaseUrl }) {
  const [video, setVideo] = useState(initialVideo);
  const [comments, setComments] = useState(initialVideo.comments || []);
  const [commentText, setCommentText] = useState('');
  const [posting, post] = useAsyncAction();
  const [liking, likeAction] = useAsyncAction();
  const [refreshingComments, refreshComments] = useAsyncAction();
  const videoRef = useRef(null);
  const csrf = useMemo(getCsrfToken, []);

  useEffect(() => {
    const el = videoRef.current;
    if (!el) return undefined;
    if (!video.playback_url && video.url) {
      el.src = video.url;
      return undefined;
    }
    return ensureLivePlayback(el, video.playback_url, video.is_live);
  }, [video.playback_url, video.url, video.is_live]);

  const handleShare = async () => {
    const shareUrl = endpoints.shareUrl;
    if (!shareUrl) return;
    const title = video.title || document.title;
    try {
      if (navigator.share) {
        await navigator.share({ url: shareUrl, title });
        return;
      }
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(shareUrl);
        alert('Link copied to clipboard.');
        return;
      }
      const fallback = prompt('Copy this link to share', shareUrl);
      if (fallback !== null) {
        alert('Link ready! Share it with your teammates.');
      }
    } catch (error) {
      if (error?.name === 'AbortError') return;
      console.error('Share failed', error);
      alert('Unable to share right now. Copy the link manually.');
    }
  };

  const submitComment = async (event) => {
    event.preventDefault();
    if (!commentText.trim()) {
      return;
    }
    post(async () => {
      const response = await fetch(endpoints.commentUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ content: commentText.trim() }),
      });
      if (!response.ok) {
        const payload = await response.json().catch(() => ({}));
        throw new Error(payload?.message || 'Unable to add comment.');
      }
      const payload = await response.json();
      if (payload.comment) {
        setComments((prev) => [payload.comment, ...prev]);
      }
      if (typeof payload.comments_count === 'number') {
        setVideo((prev) => ({ ...prev, comments_count: payload.comments_count }));
      }
      setCommentText('');
    }).catch((error) => {
      console.error(error);
      alert(error.message || 'Unable to add comment right now.');
    });
  };

  const toggleLike = async () => {
    const targetUrl = video.is_liked ? endpoints.unlikeUrl : endpoints.likeUrl;
    if (!targetUrl) {
      return;
    }
    likeAction(async () => {
      const response = await fetch(targetUrl, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({}),
      });
      if (!response.ok) {
        const payload = await response.json().catch(() => ({}));
        throw new Error(payload?.message || 'Unable to update like.');
      }
      const payload = await response.json();
      setVideo((prev) => ({
        ...prev,
        is_liked: Boolean(payload?.liked),
        likes_count: typeof payload?.likes_count === 'number' ? payload.likes_count : prev.likes_count,
      }));
    }).catch((error) => {
      console.error(error);
      alert(error.message || 'Unable to update like right now.');
    });
  };

  const fetchComments = async () => {
    refreshComments(async () => {
      const response = await fetch(endpoints.commentsUrl, {
        headers: { Accept: 'application/json' },
      });
      if (!response.ok) {
        throw new Error('Unable to load comments right now.');
      }
      const payload = await response.json();
      if (Array.isArray(payload.comments)) {
        setComments(payload.comments);
      }
      if (typeof payload.comments_count === 'number') {
        setVideo((prev) => ({ ...prev, comments_count: payload.comments_count }));
      }
    }).catch((error) => {
      console.error(error);
      alert(error.message || 'Unable to refresh comments right now.');
    });
  };

  const backHref = endpoints.backUrl;

  return (
    <div className="row g-3">
      <div className="col-xl-8">
        <div className="card shadow-sm border-0">
          <div className="ratio ratio-16x9 bg-black rounded-top">
            <video
              ref={videoRef}
              controls
              playsInline
              poster={video.thumbnail || undefined}
              className="w-100 h-100"
              style={{ objectFit: 'cover' }}
            >
              <track kind="captions" />
            </video>
          </div>
          <div className="card-body">
            <div className="d-flex flex-wrap justify-content-between align-items-start gap-3">
              <div className="d-flex align-items-center gap-3">
                <div className="story-author-avatar">
                  {video.author?.photo ? (
                    <img src={video.author.photo} alt={video.author?.name || 'Author'} />
                  ) : (
                    <span>{video.author?.initials || 'T'}</span>
                  )}
                </div>
                <div>
                  <h4 className="mb-1">{video.title || 'Team Story'}</h4>
                  <div className="small text-muted">
                    {video.created_at_exact || video.created_at_human || 'Recently shared'}
                    {video.team_name ? ` · ${video.team_name}` : ''}
                    {video.privacy === 'private' && ' · Private'}
                  </div>
                </div>
              </div>
              <div className="d-flex flex-wrap align-items-center gap-2 video-action-buttons">
                <span className="badge video-action-badge">{video.likes_count} Likes</span>
                <span className="badge video-action-badge">{video.comments_count} Comments</span>
                <button
                  type="button"
                  className={classNames(
                    'btn btn-sm video-action-btn',
                    video.is_liked ? 'video-action-btn--active' : 'video-action-btn--outline'
                  )}
                  onClick={toggleLike}
                  disabled={liking}
                >
                  <i className="fa fa-thumbs-up me-1" />
                  {video.is_liked ? 'Liked' : 'Like'}
                </button>
                <button
                  type="button"
                  className="btn btn-sm video-action-btn video-action-btn--filled"
                  onClick={handleShare}
                >
                  <i className="fa fa-share-nodes me-1" /> Share
                </button>
              </div>
            </div>

            <div className="mt-3 d-flex flex-wrap gap-2">
              <span className="story-chip">
                <i className="fa fa-hashtag" /> Team Feed
              </span>
              {video.team_name && (
                <span className="story-chip">
                  <i className="fa fa-users" /> {video.team_name}
                </span>
              )}
            </div>

            {video.description && (
              <div className="mt-3 story-body-content">
                <p>{video.description}</p>
              </div>
            )}
          </div>
        </div>

        <div className="card shadow-sm border-0 mt-3">
          <div className="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 className="mb-0">Discussion</h5>
            <div className="d-flex align-items-center gap-2">
              <span className="text-muted small">{video.comments_count} comments</span>
              <button type="button" className="btn btn-link btn-sm" onClick={fetchComments} disabled={refreshingComments}>
                {refreshingComments ? 'Refreshing…' : 'Refresh'}
              </button>
            </div>
          </div>
          <div className="card-body">
            <form className="story-comment-form" onSubmit={submitComment}>
              <div className="mb-2 w-100">
                <textarea
                  rows={3}
                  className="form-control"
                  placeholder="Share your thoughts..."
                  value={commentText}
                  onChange={(event) => setCommentText(event.target.value)}
                  maxLength={500}
                  required
                />
              </div>
              <div className="d-flex justify-content-end">
                <button
                  type="submit"
                  className="btn video-action-btn video-action-btn--filled"
                  disabled={posting}
                >
                  {posting ? 'Posting…' : 'Post Comment'}
                </button>
              </div>
            </form>

            <div className="story-comments-list mt-3">
              {comments.length === 0 ? (
                <p className="text-muted">Be the first to comment.</p>
              ) : (
                comments.map((comment) => <Comment key={comment.id} comment={comment} />)
              )}
            </div>
          </div>
        </div>
      </div>

      <div className="col-xl-4 d-flex flex-column gap-3">
        <div className="card border-0 shadow-sm">
          <div className="card-body">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <h5 className="mb-0">Need to update?</h5>
              {backHref && (
                <a href={backHref} className="btn btn-sm btn-outline-secondary">Back</a>
              )}
            </div>
            <p className="text-muted mb-3">Made a typo or have more to share? Head back to the editor to update your post any time.</p>
            <a href={showBaseUrl || backHref || '#'} className="btn btn-outline-primary btn-sm">
              <i className="fa fa-arrow-left me-1" /> Explore feed
            </a>
          </div>
        </div>

        {relatedVideos.length > 0 && (
          <div className="card border-0 shadow-sm">
            <div className="card-header bg-white">
              <h5 className="mb-0">More from your network</h5>
            </div>
            <div className="card-body d-flex flex-column gap-2">
              {relatedVideos.map((related) => (
                <RelatedVideoCard key={related.id} video={related} baseShowUrl={showBaseUrl || '/player/videos/explore'} />
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

function boot() {
  const container = document.getElementById('video-detail-root');
  if (!container) {
    return;
  }

  const {
    video: videoAttr,
    related: relatedAttr,
    likeUrl,
    unlikeUrl,
    commentUrl,
    commentsUrl,
    shareUrl,
    backUrl,
    showBaseUrl,
  } = container.dataset;

  let videoData = {};
  let relatedVideos = [];

  try {
    if (videoAttr) {
      videoData = JSON.parse(videoAttr);
    }
    if (relatedAttr) {
      relatedVideos = JSON.parse(relatedAttr);
    }
  } catch (error) {
    console.warn('Failed to parse video data', error);
  }

  const endpoints = {
    likeUrl,
    unlikeUrl,
    commentUrl,
    commentsUrl,
    shareUrl,
    backUrl,
  };

  createRoot(container).render(
    <VideoDetailApp
      initialVideo={videoData}
      relatedVideos={relatedVideos}
      endpoints={endpoints}
      showBaseUrl={showBaseUrl}
    />
  );
}

boot();
