@php
    $tournamentStoreAction = route('club.tournaments.store-modal');
    $clubTeams = $clubTeams ?? collect();
    $countries = \App\Models\Country::orderBy('name')->get(['id', 'name']);
    $states = collect();
    $cities = collect();
@endphp

<style>
/* Theme Colors Only */
[data-theme='light'] {
    --modal-bg: #ffffff;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --hero-bg: #F1F1F1;
    --hero-text: #0B1A22;
    --hero-subtitle: #5B6C7E;
    --tag-bg: #10d9c4;
    --tag-text: #000000;
    --avatar-bg: #E2E8F0;
    --avatar-text: #000000;
    --share-border: #6b7280;
    --content-bg: #f8fafc;
    --content-text: #374151;
    --content-title: #1f2937;
    --quote-bg: #f8fafc;
    --quote-border: #10d9c4;
    --quote-text: #374151;
    --quote-author: #6b7280;
}

[data-theme='dark'] {
    --modal-bg: #0f172a;
    --text-primary: #e5e7eb;
    --text-secondary: #94a3b8;
    --border-color: #1f2937;
    --hero-bg: #2F323C;
    --hero-text: #ffffff;
    --hero-subtitle: #d1d5db;
    --tag-bg: #10d9c4;
    --tag-text: #000000;
    --avatar-bg: #223142;
    --avatar-text: #fff;
    --share-border: #6b7280;
    --content-bg: #0f172a;
    --content-text: #cbd5e1;
    --content-title: #e5e7eb;
    --quote-bg: #2F323C;
    --quote-border: #10d9c4;
    --quote-text: #e5e7eb;
    --quote-author: #94a3b8;
}

/* Modal CSS */
.modal-content {
    background-color: var(--modal-bg);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

/* Hero Section */
.hero-section {
    background-color: var(--hero-bg);
    padding: 3rem 2rem;
    min-height: 350px;
    border-radius: 16px;
    position: relative;
    display: flex;
    flex-direction: column;
    /* justify-content: center; */
}

.category-tag {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    color: var(--tag-text);
    padding: 0.5rem 1.5rem;
    width: fit-content;
    border-radius: 25px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    display: inline-block;
    margin-bottom: 2rem;
    text-transform: uppercase;
}

.hero-title {
    font-size: 1.5rem;
    font-weight: 400;
    color: var(--hero-text);
    line-height: 1.1;
    margin-bottom: 1.5rem;
    max-width: 800px;
}

.hero-subtitle {
    font-size: 1rem;
    color: var(--hero-subtitle);
    line-height: 1.5;
    font-weight:400;
    max-width: 600px;
}

.author-info {
    margin-top:20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.author-details {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.author-avatar {
    width: 50px;
    height: 50px;
    background-color: var(--avatar-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--avatar-text);
    font-weight: 700;
    font-size: 1.125rem;
}

.author-text {
    color: var(--hero-text);
    font-size: 0.875rem;
}

.author-name {
    font-weight: 400;
    margin-bottom: 0.25rem;
    letter-spacing: 0.05em;
}

.author-meta {
    color: var(--hero-subtitle);
}

.share-section {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.share-label {
    color: var(--hero-subtitle);
    font-size: 0.875rem;
    letter-spacing: 0.05em;
}

.share-buttons {
    display: flex;
    gap: 0.75rem;
}

.share-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--share-border);
    background-color: transparent;
    color: var(--hero-subtitle);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    font-weight: 500;
}

.share-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--hero-text);
}

.share-btn.primary {
   background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);

    color: var(--tag-text);
    border-color: var(--tag-bg);
    font-weight: 700;
}

[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Overview Section */
.overview-section {
    /* background-color: var(--content-bg); */
    /* padding: 2rem; */
    margin-top: 2rem;
    /* border-radius: 12px; */
}

.overview-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--content-title);
    margin-bottom: 1.5rem;
}

.overview-text {
    font-size: 1rem;
    color: var(--content-text);
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.quote-block {
    background-color: var(--quote-bg);
    border-left: 4px solid var(--quote-border);
    padding: 1.5rem;
    margin: 2rem 0;
    border-radius: 0 8px 8px 0;
}

.quote-text {
    font-style: italic;
    font-size: 1.125rem;
    color: var(--quote-text);
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.quote-author {
    color: var(--quote-author);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Key Points Section */
.key-points-section {
    /* padding: 2rem; */
    margin-top: 2rem;
}

.key-points-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--content-title);
    margin-bottom: 1.5rem;
}

.key-points-list {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.key-points-list li {
    padding: 0.25rem 0;
    color: var(--content-text);
    position: relative;
    padding-left: 1.5rem;
    font-size: 1rem;
    line-height: 1.6;
}

.key-points-list li::before {
    content: "•";
    color: var(--tag-bg);
    font-weight: bold;
    position: absolute;
    left: 0;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    border-radius: 12px;
    padding: 2rem;
    color: #08121A;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cta-content h3 {
    font-size: 1rem;
    font-weight: 400;
    margin-bottom: 0.5rem;
}

.cta-content p {
    opacity: 0.9;
    margin: 0;
}

.cta-button {
    background-color: #ffffff;
    color: #08121A;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 400;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.cta-button:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
}

/* Tags Section */
.tags-section {
    /* padding: 2rem; */
    margin-top: 2rem;
}

.tags-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--content-title);
    margin-bottom: 1rem;
    letter-spacing: 0.05em;
}

.tags-list {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.tag-item {
    background-color: transparent;
    color: var(--content-text);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid var(--share-border);
    cursor: pointer;
    transition: all 0.2s;
}

.tag-item:hover {
    border-color: var(--tag-bg);
    color: var(--content-title);
}

.tag-item.active {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    color: #ffffff;
    border-color: transparent;
}

/* Related Posts Section */
.related-postss {
    margin-top: 3rem;
    padding: 2rem;
}

.related-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--content-title);
    margin-bottom: 1.5rem;
    letter-spacing: 0.05em;
}

.related-grid {
    display: flex;
    gap: 1.5rem;
    justify-content: space-between;
}

.related-card {
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    padding: 0 !important;
    overflow: hidden;
    transition: all 0.2s;
    cursor: pointer;
    flex: 1;
    max-width: calc(33.333% - 1rem);
}

.related-card:hover {
    transform: translateY(-2px);
}

.related-card-gradient {
    height: 120px;
    display: flex;
    border-radius: 16px;
    align-items: center;
    justify-content: center;
}

.related-card-gradient.gradient-1 {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
}

.related-card-gradient.gradient-2 {
    background: linear-gradient(135deg, #7C3AED 0%, #39A2FF 100%);
}

.related-card-gradient.gradient-3 {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
}

.related-card-content {
    background-color: var(--content-bg);
    padding: 1.5rem;
}

.related-card h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--content-title);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.related-card p {
    color: var(--content-text);
    font-size: 0.875rem;
    margin: 0;
    line-height: 1.4;
}
.hero-section-2 {
    background-color: var(--hero-bg);
    padding: 3rem 2rem;
    min-height: 350px;
    border-radius: 16px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: end;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-section {
        padding: 2rem 1.5rem;
        min-height: 300px;
    }
    
    .hero-title {
        font-size: 1.25rem;
    }
    
    .hero-subtitle {
        font-size: 0.9rem;
    }
    
    .author-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .share-section {
        align-self: flex-end;
    }
    
    .cta-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .related-grid {
        flex-direction: column;
    }
    
    .related-card {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .modal-content {
        padding: 1rem !important;
    }
    
    .p-4 {
        padding: 1rem !important;
    }
    
    .hero-section {
        padding: 1.5rem 1rem;
        min-height: 250px;
    }
    
    .hero-title {
        font-size: 1.125rem;
        line-height: 1.3;
    }
    
    .hero-subtitle {
        font-size: 0.875rem;
        margin-bottom: 2rem;
    }
    
    
    .share-buttons {
        gap: 0.5rem;
    }
    
    .share-btn {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }
    
    .overview-section,
    .key-points-section,
    .tags-section {
        margin-top: 1.5rem;
    }
    
    .overview-title,
    .key-points-title,
    .tags-title,
    .related-title {
        font-size: 1.125rem;
    }
    
    .quote-block {
        padding: 1rem;
        margin: 1.5rem 0;
    }
    
    .quote-text {
        font-size: 1rem;
    }
    
    .cta-section {
        padding: 1.5rem;
    }
    
    .cta-content h3 {
        font-size: 0.9rem;
    }
    
    .cta-content p {
        font-size: 0.875rem;
    }
    
    .cta-button {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .tags-list {
        gap: 0.5rem;
    }
    
    .tag-item {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .related-postss {
        padding: 1rem 0;
    }
    
    .related-card-gradient {
        height: 100px;
    }
    
    .related-card-content {
        padding: 1rem;
    }
    
    .hero-section-2 {
        padding: 1.5rem 1rem;
        min-height: 200px;
    }
}

@media (max-width: 576px) {
    .f-scroll{
        overflow-y:auto;
        height:100%;
    }
    .modal-fade {
        padding: 0.5rem !important;
    }
    
    .hero-section {
        padding: 1rem;
        min-height: 200px;
    }
    
    .hero-title {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .hero-subtitle {
        font-size: 0.8rem;
        margin-bottom: 1.5rem;
    }
    
    .category-tag {
        padding: 0.4rem 1rem;
        font-size: 0.7rem;
        margin-bottom: 1.5rem;
    }
    
    .author-avatar {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .author-text {
        font-size: 0.8rem;
    }
    
    .share-label {
        font-size: 0.8rem;
    }
    
    .share-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
    
    .overview-text,
    .key-points-list li {
        font-size: 0.9rem;
    }
    
    .quote-text {
        font-size: 0.95rem;
    }
    
    .quote-author {
        font-size: 0.8rem;
    }
    
    .related-card-gradient {
        height: 80px;
    }
    
    .related-card h4 {
        font-size: 0.9rem;
    }
    
    .related-card p {
        font-size: 0.8rem;
    }
    .f-modal > .modal-dialog > .modal-content{
        padding: 12px !important;
    }
}
.f-modal > .modal-dialog > .modal-content{
    padding: 24px;
}
</style>

<div class="f-modal modal fade" id="createBlogPostModel" tabindex="-1" aria-labelledby="createBlogPostModelLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute" style="top: 1.5rem; right: 1.5rem; z-index: 999999;" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-xmark"></i></button>
            
            <!-- Hero Section -->
             <div class="p-4 f-scroll">
            <div class="hero-section">
                <div class="category-tag">SOCCER</div>
                <h1 class="hero-title">How Our Tournament Engine Powers Club Growth</h1>
                <p class="hero-subtitle">A behind-the-scenes look at scheduling, player onboarding, and payouts.</p>
                
            </div>
            <div class="author-info">
                <div class="author-details">
                    <div class="author-avatar">VP</div>
                    <div class="author-text">
                        <div class="author-name">BY VINCENT PORTER</div>
                        <div class="author-meta">AUG 2025 • 8 MIN READ</div>
                    </div>
                </div>
                
                <div class="share-section">
                    <span class="share-label">SHARE</span>
                    <div class="share-buttons">
                        <button class="share-btn primary">P</button>
                        <button class="share-btn">X</button>
                        <button class="share-btn">f</button>
                        <button class="share-btn">in</button>
                        <button class="share-btn">🔗</button>
                    </div>
                </div>
            </div>
            <!-- New Section here -->
            
            <!-- Overview Section -->
            <div class="overview-section">
                <h2 class="overview-title">Overview</h2>
                <p class="overview-text">Clubs thrive when operations are smooth—fixtures, travel, and communication all in one place.</p>
                <p class="overview-text">This post outlines our approach to onboarding players, organizing tournaments, and paying out incentives.</p>
                
                <div class="quote-block">
                    <p class="quote-text">"Frictionless scheduling and payouts boost participation and retention."</p>
                    <p class="quote-author">— Club Director</p>
                </div>
            </div>
             <div class="hero-section-2">
                <h2 class="hero-subtitle">Figure: Tournament calendar with player availability.</h2>
            </div>
            <!-- Key Points Section -->
            <div class="key-points-section">
                <h2 class="key-points-title">Key points</h2>
                <ul class="key-points-list">
                    <li>One tap onboarding reduces admin time</li>
                    <li>Smart payouts per player keep it simple</li>
                    <li>Live help chat resolves issues quickly</li>
                </ul>
                
                <!-- CTA Section -->
                <div class="cta-section">
                    <div class="cta-content">
                        <h3>Ready to set up your next tournament?</h3>
                        <p>Start with our onboarding checklist.</p>
                    </div>
                    <button class="cta-button">Get Checklist</button>
                </div>
            </div>
            
            <!-- Tags Section -->
            <div class="tags-section">
                <h3 class="tags-title">TAGS</h3>
                <div class="tags-list">
                    <span class="tag-item active">tournaments</span>
                    <span class="tag-item">payments</span>
                    <span class="tag-item">onboarding</span>
                    <span class="tag-item">payments</span>
                </div>
            </div>
            
            <!-- Related Posts Section -->
            <div class="related-postss">
                <h2 class="related-title">RELATED POSTS</h2>
                <div class="related-grid">
                    <div class="related-card">
                        <div class="related-card-gradient gradient-1"></div>
                        <div class="related-card-content">
                            <h4>Building the Calendar</h4>
                            <p>Scheduling & availability</p>
                        </div>
                    </div>
                    
                    <div class="related-card">
                        <div class="related-card-gradient gradient-2"></div>
                        <div class="related-card-content">
                            <h4>Payouts, Simplified</h4>
                            <p>Per-player model</p>
                        </div>
                    </div>
                    
                    <div class="related-card">
                        <div class="related-card-gradient gradient-3"></div>
                        <div class="related-card-content">
                            <h4>Live Help & Chat</h4>
                            <p>Faster resolutions</p>
                        </div>
                    </div>
                </div>
            </div>
           
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('createBlogPostModel');
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            modal.setAttribute('data-theme', currentTheme);
        });
        
        document.addEventListener('themeChange', function(e) {
            modal.setAttribute('data-theme', e.detail.theme);
        });
    }
});
</script>
