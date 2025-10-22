
<style>
body {
    background-color: #1e293b;
    color: #e2e8f0;
    font-family: 'Inter', sans-serif;
}

.modal-content {
    background-color: #0f172a;
    border: none;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
}

.tag {
    background-color: #0ea5e9;
    color: white;
    border-radius: 20px;
    padding: 5px 14px;
    display: inline-block;
    font-size: 0.85rem;
    font-weight: 500;
}

.article-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #f8fafc;
    margin-top: 12px;
}

.article-meta {
    font-size: 0.85rem;
    color: #94a3b8;
    margin-bottom: 15px;
}

.quote-box {
    background-color: #1e293b;
    border-left: 4px solid #38bdf8;
    padding: 15px;
    margin: 15px 0;
    border-radius: 8px;
    font-style: italic;
}

.gradient-box {
    background: linear-gradient(90deg, #06b6d4, #3b82f6);
    border-radius: 12px;
    padding: 16px;
    color: white;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.gradient-box button {
    background-color: white;
    color: #0f172a;
    font-weight: 600;
    border-radius: 10px;
    padding: 6px 18px;
    border: none;
    transition: all 0.3s;
}

.gradient-box button:hover {
    background-color: #f8fafc;
    transform: scale(1.05);
}

.related-posts {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.related-card {
    background-color: #1e293b;
    border-radius: 12px;
    padding: 20px;
    flex: 1;
    text-align: center;
    color: #cbd5e1;
    transition: 0.3s;
}

.related-card:hover {
    background-color: #334155;
}

.nav-tags {
    margin-top: 20px;
}

.nav-tags button {
    border-radius: 20px;
    padding: 6px 14px;
    background-color: #1e293b;
    border: 1px solid #334155;
    color: #cbd5e1;
    margin-right: 8px;
    transition: 0.3s;
}

.nav-tags button:hover {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.blog-post-1-div{
    
}

</style>

<!-- Modal -->
<div class="modal fade" id="createBlogPostModel" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="padding: 20px !important;">

        <div class="blog-post-1-div">

        </div>

    <span class="tag">Soccer</span>
    <h4 class="article-title mt-3">How Our Tournament Engine Powers Club Growth</h4>

    <p class="article-meta">By <strong>Insight Portal</strong> • April 2025 • 14k reads</p>

    <p>Clubs thrive when operations are smart—driven, tracked, and communicated at all phases.
    This post reviews our approach to scheduling players, organizing tournaments, and syncing club timelines.</p>

    <div class="quote-box">
        “Effective scheduling and payout-based participation and motivation.”
        <br><small>– Club Director</small>
    </div>

    <p>Figure: Tournament calendar with player availability.</p>

    <h6 class="mt-4">Key Points</h6>
    <ul>
        <li>On-time scheduling reduces admin time.</li>
        <li>Smart calendars sync player availability instantly.</li>
        <li>Data-driven insights improve future planning.</li>
    </ul>

    <div class="gradient-box">
        <div>
        <h6>Ready to set up your next tournament?</h6>
        <p class="mb-0">Start with our onboarding checklist.</p>
        </div>
        <button>Get Checklist</button>
    </div>

    <div class="nav-tags">
        <button>Management</button>
        <button>Operations</button>
        <button>Scheduling</button>
        <button>Automation</button>
    </div>

    <h6 class="mt-4">Related Posts</h6>
    <div class="related-posts">
        <div class="related-card">Building the Calendar: Scheduling in Velocity</div>
        <div class="related-card">Payments Simplified: Payouts for Clubs</div>
        <div class="related-card">How We Help Clubs Drive Retention</div>
    </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

