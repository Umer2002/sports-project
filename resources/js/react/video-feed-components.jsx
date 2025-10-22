import React, {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from "react";

export const TABS = [
    { key: "all", label: "All" },
    { key: "skill", label: "Skill" },
    { key: "tutorial", label: "Tutorial" },
    { key: "challenge", label: "Challenge" },
    { key: "match", label: "Match" },
    { key: "training", label: "Training" },
    { key: "live", label: "Live" },
];

export const DEFAULT_ENDPOINT = "/player/api/videos";
export const DEFAULT_SHOW_BASE = "/player/videos/explore";

export function buildShowUrl(base, id) {
    if (!base) return `${DEFAULT_SHOW_BASE}/${id}`;
    return `${base.replace(/\/$/, "")}/${id}`;
}

function formatTimeAgo(value) {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";
    const seconds = Math.max(0, (Date.now() - date.getTime()) / 1000);
    if (seconds < 45) return "just now";
    if (seconds < 90) return "1 min ago";
    const minutes = seconds / 60;
    if (minutes < 60) return `${Math.round(minutes)} min ago`;
    const hours = minutes / 60;
    if (hours < 24) return `${Math.round(hours)} hr ago`;
    const days = hours / 24;
    if (days < 7) return `${Math.round(days)} d ago`;
    return date.toLocaleDateString();
}

function getCsrfToken() {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? ""
    );
}

export function useFetchVideos(endpoint, params) {
    const [state, setState] = useState({
        data: [],
        meta: { page: 1, pages: 1, total: 0 },
        loading: true,
    });
    const q = new URLSearchParams(params).toString();
    useEffect(() => {
        let alive = true;
        setState((s) => ({ ...s, loading: true }));
        const target = `${endpoint || DEFAULT_ENDPOINT}?${q}`;
        fetch(target, { credentials: "same-origin" })
            .then((r) => r.json())
            .then((json) => {
                if (!alive) return;
                setState({
                    data: json.data || [],
                    meta: json.meta || { page: 1, pages: 1, total: 0 },
                    loading: false,
                });
            })
            .catch(() => {
                if (!alive) return;
                setState((s) => ({ ...s, loading: false }));
            });
        return () => {
            alive = false;
        };
    }, [endpoint, q]);
    return state;
}

export function VideoCard({
    video,
    baseShowUrl,
    onOpenComments,
    onVideoUpdate,
}) {
    const mediaRef = useRef(null);
    const href = buildShowUrl(baseShowUrl, video.id);
    const mediaUrl = video.playback_url || video.url;
    const isLive =
        video.is_live || (mediaUrl || "").toLowerCase().endsWith(".m3u8");
    const timeAgo = formatTimeAgo(video.created_at);
    const csrf = useMemo(getCsrfToken, []);
    const [liked, setLiked] = useState(Boolean(video.is_liked));
    const [likesCount, setLikesCount] = useState(video.likes_count ?? 0);
    const [pendingLike, setPendingLike] = useState(false);

    useEffect(() => {
        setLiked(Boolean(video.is_liked));
        setLikesCount(video.likes_count ?? 0);
    }, [video.id, video.is_liked, video.likes_count]);

    useEffect(() => {
        if (isLive) {
            return undefined;
        }
        const el = mediaRef.current;
        if (!el) {
            return undefined;
        }
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (
                        entry.isIntersecting &&
                        entry.intersectionRatio > 0.55
                    ) {
                        const promise = el.play();
                        if (promise?.catch) {
                            promise.catch(() => {});
                        }
                    } else {
                        el.pause();
                        el.currentTime = 0;
                    }
                });
            },
            { threshold: [0.35, 0.6, 0.85] }
        );
        observer.observe(el);
        return () => {
            observer.disconnect();
            el.pause();
        };
    }, [isLive]);

    const handleLike = async (event) => {
        event.preventDefault();
        event.stopPropagation();
        if (pendingLike || !video.like_url || !video.unlike_url) {
            return;
        }
        const targetUrl = liked ? video.unlike_url : video.like_url;
        setPendingLike(true);
        try {
            const response = await fetch(targetUrl, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                },
                body: JSON.stringify({}),
            });
            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(
                    payload?.message || "Unable to update like right now."
                );
            }
            const payload = await response.json();
            const nextLiked = Boolean(payload?.liked);
            const nextCount =
                typeof payload?.likes_count === "number"
                    ? payload.likes_count
                    : likesCount;
            setLiked(nextLiked);
            setLikesCount(nextCount);
            onVideoUpdate?.({
                ...video,
                is_liked: nextLiked,
                likes_count: nextCount,
            });
        } catch (error) {
            console.error(error);
            alert(error.message || "Unable to update like right now.");
        } finally {
            setPendingLike(false);
        }
    };

    const handleComments = (event) => {
        event.preventDefault();
        event.stopPropagation();
        onOpenComments?.(video);
    };

    const handleShare = async (event) => {
        event.preventDefault();
        event.stopPropagation();
        const shareUrl = video.share_url || href;
        const title = video.title || document.title;
        try {
            if (navigator.share) {
                await navigator.share({ url: shareUrl, title });
                return;
            }
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(shareUrl);
                alert("Link copied to clipboard.");
                return;
            }
            const fallback = prompt("Copy this link to share", shareUrl);
            if (fallback !== null) {
                alert("Link ready! Share it with your teammates.");
            }
        } catch (error) {
            if (error?.name !== "AbortError") {
                console.error("Share failed", error);
                alert("Unable to share right now. Copy the link manually.");
            }
        }
    };

    return (
        <div className="video-card">
            <a className="video-card__media" href={href}>
                {isLive ? (
                    <div className="video-card__live">Live Stream</div>
                ) : (
                    <video
                        ref={mediaRef}
                        src={mediaUrl}
                        muted
                        playsInline
                        loop
                        preload="metadata"
                        style={{
                            width: "100%",
                            height: 500,
                            objectFit: "cover",
                        }}
                    />
                )}
            </a>
            <div className="video-card__body">
                <div className="video-card__meta">
                    <div>
                        <h5 className="video-card__title" title={video.title}>
                            {video.title}
                        </h5>
                        <div className="video-card__subtitle">
                            <span>
                                {(
                                    video.category ||
                                    video.video_type ||
                                    "Video"
                                ).toUpperCase()}
                            </span>
                            {timeAgo && <span>• {timeAgo}</span>}
                            {video.is_live && (
                                <span className="badge bg-danger">LIVE</span>
                            )}
                        </div>
                    </div>
                    <a className="btn btn-outline-light btn-sm" href={href}>
                        View
                    </a>
                </div>
                {video.description && (
                    <p className="video-card__description">
                        {video.description.length > 150
                            ? `${video.description.slice(0, 147)}…`
                            : video.description}
                    </p>
                )}
                <div className="video-card__stats">
                    <span>👍 {likesCount}</span>
                    <span>💬 {video.comments_count}</span>
                </div>
                <div className="video-card__actions">
                    <button
                        type="button"
                        className={`video-card__action ${
                            liked ? "is-active" : ""
                        }`}
                        onClick={handleLike}
                        disabled={pendingLike}
                    >
                        <i className="fa fa-thumbs-up" />{" "}
                        {liked ? "Liked" : "Like"}
                    </button>
                    <button
                        type="button"
                        className="video-card__action"
                        onClick={handleComments}
                    >
                        <i className="fa fa-comment" /> Comment
                    </button>
                    <button
                        type="button"
                        className="video-card__action"
                        onClick={handleShare}
                    >
                        <i className="fa fa-share" /> Share
                    </button>
                </div>
            </div>
        </div>
    );
}

export function ExplorePage({
    endpoint,
    baseShowUrl,
    initialVideos = [],
    initialMeta = null,
    onVideoUpdate,
    refreshSignal = 0,
    onLoadingStateChange,
}) {
    const [tab, setTab] = useState("all");
    const [page, setPage] = useState(1);
    const [videos, setVideos] = useState(initialVideos);
    const [meta, setMeta] = useState(
        initialMeta || { page: 1, pages: 1, total: initialVideos.length }
    );
    const [loading, setLoading] = useState(initialVideos.length === 0);
    const containerRef = useRef(null);
    const sentinelRef = useRef(null);
    const firstLoadRef = useRef(true);
    const csrf = useMemo(getCsrfToken, []);
    const [commentsState, setCommentsState] = useState({
        openId: null,
        video: null,
        comments: [],
        loading: false,
        posting: false,
        text: "",
        error: null,
    });

    const filters = useMemo(() => {
        if (tab === "all") return {};
        if (tab === "live") return { type: "live" };
        return { category: tab };
    }, [tab]);
    const filtersKey = useMemo(() => JSON.stringify(filters), [filters]);

    useEffect(() => {
        setVideos(initialVideos);
        setMeta(
            initialMeta || { page: 1, pages: 1, total: initialVideos.length }
        );
        setLoading(initialVideos.length === 0);
    }, [initialVideos, initialMeta]);

    useEffect(() => {
        if (firstLoadRef.current) {
            firstLoadRef.current = false;
            return;
        }
        setVideos([]);
        setMeta({ page: 1, pages: 1, total: 0 });
        setPage(1);
        setLoading(true);
    }, [filtersKey, endpoint, refreshSignal]);

    useEffect(() => {
        let cancelled = false;
        const params = new URLSearchParams({
            limit: 6,
            page,
            ...filters,
        }).toString();
        setLoading(true);
        fetch(`${endpoint || DEFAULT_ENDPOINT}?${params}`, {
            credentials: "same-origin",
        })
            .then((r) => r.json())
            .then((json) => {
                if (cancelled) return;
                const incoming = Array.isArray(json.data) ? json.data : [];
                setVideos((prev) => {
                    if (page === 1) {
                        return incoming;
                    }
                    const existingIds = new Set(prev.map((item) => item.id));
                    const merged = incoming.filter(
                        (item) => !existingIds.has(item.id)
                    );
                    return prev.concat(merged);
                });
                setMeta(
                    json.meta || { page, pages: page, total: incoming.length }
                );
                setLoading(false);
            })
            .catch(() => {
                if (!cancelled) {
                    setLoading(false);
                }
            });
        return () => {
            cancelled = true;
        };
    }, [endpoint, page, filtersKey, refreshSignal]);

    useEffect(() => {
        onLoadingStateChange?.(loading);
    }, [loading, onLoadingStateChange]);

    const hasMore = useMemo(() => page < (meta.pages || 1), [page, meta.pages]);

    useEffect(() => {
        const sentinel = sentinelRef.current;
        if (!sentinel) return undefined;
        const observer = new IntersectionObserver(
            (entries) => {
                const [entry] = entries;
                if (entry && entry.isIntersecting && !loading && hasMore) {
                    setPage((current) => {
                        if (current >= (meta.pages || current + 1)) {
                            return current;
                        }
                        return current + 1;
                    });
                }
            },
            { root: containerRef.current || null, threshold: 0.25 }
        );
        observer.observe(sentinel);
        return () => observer.disconnect();
    }, [loading, hasMore, meta.pages]);

    const updateVideoInState = useCallback(
        (videoId, patch) => {
            setVideos((prev) => {
                const index = prev.findIndex((item) => item.id === videoId);
                if (index === -1) {
                    return prev;
                }
                const base = prev[index];
                const updates =
                    typeof patch === "function" ? patch(base) : patch;
                if (!updates) {
                    return prev;
                }
                const updated = { ...base, ...updates };
                const next = [...prev];
                next[index] = updated;
                onVideoUpdate?.(updated);
                setCommentsState((state) =>
                    state.openId === videoId
                        ? { ...state, video: updated }
                        : state
                );
                return next;
            });
        },
        [onVideoUpdate]
    );

    const handleOpenComments = useCallback(
        (video) => {
            if (commentsState.openId === video.id) {
                setCommentsState({
                    openId: null,
                    video: null,
                    comments: [],
                    loading: false,
                    posting: false,
                    text: "",
                    error: null,
                });
                return;
            }
            setCommentsState({
                openId: video.id,
                video,
                comments: [],
                loading: true,
                posting: false,
                text: "",
                error: null,
            });
            fetch(video.comments_url, {
                headers: { Accept: "application/json" },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Unable to load comments right now.");
                    }
                    return response.json();
                })
                .then((payload) => {
                    setCommentsState((state) =>
                        state.openId === video.id
                            ? {
                                  ...state,
                                  comments: Array.isArray(payload.comments)
                                      ? payload.comments
                                      : [],
                                  loading: false,
                                  error: null,
                              }
                            : state
                    );
                    if (typeof payload.comments_count === "number") {
                        updateVideoInState(video.id, {
                            comments_count: payload.comments_count,
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                    setCommentsState((state) =>
                        state.openId === video.id
                            ? {
                                  ...state,
                                  loading: false,
                                  error:
                                      error.message ||
                                      "Unable to load comments right now.",
                              }
                            : state
                    );
                });
        },
        [commentsState.openId, updateVideoInState]
    );

    const handleCommentSubmit = async (event) => {
        event.preventDefault();
        const panel = commentsState;
        if (!panel.video || !panel.text.trim()) {
            return;
        }
        setCommentsState((state) => ({ ...state, posting: true, error: null }));
        try {
            const response = await fetch(panel.video.comment_url, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                },
                body: JSON.stringify({ content: panel.text.trim() }),
            });
            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || "Unable to add comment.");
            }
            const payload = await response.json();
            setCommentsState((state) =>
                state.openId === panel.openId
                    ? {
                          ...state,
                          posting: false,
                          text: "",
                          comments: payload.comment
                              ? [payload.comment, ...state.comments]
                              : state.comments,
                      }
                    : state
            );
            if (typeof payload.comments_count === "number") {
                updateVideoInState(panel.video.id, {
                    comments_count: payload.comments_count,
                });
            }
        } catch (error) {
            console.error(error);
            setCommentsState((state) =>
                state.openId === panel.openId
                    ? {
                          ...state,
                          posting: false,
                          error:
                              error.message ||
                              "Unable to add comment right now.",
                      }
                    : state
            );
        }
    };

    const closeComments = () => {
        setCommentsState({
            openId: null,
            video: null,
            comments: [],
            loading: false,
            posting: false,
            text: "",
            error: null,
        });
    };

    return (
        <div className="shorts-wrapper">
            <ul className="nav nav-pills mb-3">
                {TABS.map((t) => (
                    <li className="nav-item" key={t.key}>
                        <button
                            className={`nav-link ${
                                tab === t.key ? "active" : ""
                            }`}
                            onClick={() => setTab(t.key)}
                            type="button"
                        >
                            {t.label}
                        </button>
                    </li>
                ))}
            </ul>

            <div className="shorts-feed" ref={containerRef}>
                {videos.map((video) => {
                    const isOpen = commentsState.openId === video.id;
                    return (
                        <div className="video-feed-item" key={video.id}>
                            <VideoCard
                                video={video}
                                baseShowUrl={baseShowUrl}
                                onOpenComments={handleOpenComments}
                                onVideoUpdate={(next) =>
                                    updateVideoInState(next.id, next)
                                }
                            />
                            {isOpen && (
                                <div className="video-comments-panel">
                                    <div className="video-comments-header">
                                        <strong>Discussion</strong>
                                        <button
                                            type="button"
                                            className="btn btn-sm btn-outline-secondary"
                                            onClick={closeComments}
                                        >
                                            Close
                                        </button>
                                    </div>
                                    {commentsState.error && (
                                        <div className="alert alert-warning mb-2">
                                            {commentsState.error}
                                        </div>
                                    )}
                                    {commentsState.loading ? (
                                        <p className="text-muted">
                                            Loading comments…
                                        </p>
                                    ) : commentsState.comments.length === 0 ? (
                                        <p className="text-muted mb-3">
                                            Be the first to comment.
                                        </p>
                                    ) : (
                                        <div className="video-comments-list">
                                            {commentsState.comments.map(
                                                (comment) => (
                                                    <div
                                                        className="video-comment"
                                                        key={comment.id}
                                                    >
                                                        <div className="video-comment__header">
                                                            <strong>
                                                                {comment.name}
                                                            </strong>
                                                            <span
                                                                title={
                                                                    comment.created_at_exact ||
                                                                    undefined
                                                                }
                                                            >
                                                                {
                                                                    comment.created_at
                                                                }
                                                            </span>
                                                        </div>
                                                        <p className="video-comment__body">
                                                            {comment.content}
                                                        </p>
                                                    </div>
                                                )
                                            )}
                                        </div>
                                    )}
                                    <form
                                        className="video-comments-form"
                                        onSubmit={handleCommentSubmit}
                                    >
                                        <textarea
                                            rows={3}
                                            className="form-control"
                                            placeholder="Share your thoughts…"
                                            value={commentsState.text}
                                            onChange={(event) =>
                                                setCommentsState((state) => ({
                                                    ...state,
                                                    text: event.target.value,
                                                }))
                                            }
                                            maxLength={500}
                                            required
                                        />
                                        <div className="d-flex justify-content-end gap-2">
                                            <button
                                                type="button"
                                                className="btn btn-outline-secondary"
                                                onClick={closeComments}
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                className="btn btn-primary"
                                                disabled={commentsState.posting}
                                            >
                                                {commentsState.posting
                                                    ? "Posting…"
                                                    : "Post Comment"}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            )}
                        </div>
                    );
                })}
                {videos.length === 0 && !loading && (
                    <div className="video-feed-empty">
                        No videos yet. Try switching tabs or upload your first
                        clip.
                    </div>
                )}
                <div ref={sentinelRef} className="video-feed-sentinel">
                    {loading
                        ? "Loading more…"
                        : hasMore
                        ? "Scroll for more videos"
                        : "You have reached the end of the feed"}
                </div>
            </div>
        </div>
    );
}

export function LatestFiveWidget({ endpoint, baseShowUrl }) {
    const { data, loading } = useFetchVideos(endpoint, { limit: 5 });
    if (loading) return <div className="p-2">Loading…</div>;
    return (
        <div className="row g-2">
            {data.map((v) => (
                <div className="col-12 col-sm-6 col-lg-4" key={v.id}>
                    <a
                        href={buildShowUrl(baseShowUrl, v.id)}
                        className="text-decoration-none text-reset"
                    >
                        <div className="card h-100">
                            <div className="ratio ratio-16x9">
                                <video
                                    src={v.url}
                                    muted
                                    playsInline
                                    preload="metadata"
                                    style={{
                                        width: "100%",
                                        height: "100%",
                                        objectFit: "cover",
                                    }}
                                />
                            </div>
                            <div className="card-body py-2">
                                <div className="d-flex justify-content-between">
                                    <span
                                        className="small text-truncate"
                                        title={v.title}
                                    >
                                        {v.title}
                                    </span>
                                    {v.is_live && (
                                        <span className="badge bg-danger">
                                            LIVE
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            ))}
        </div>
    );
}
