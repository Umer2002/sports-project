@php
    $tournamentStoreAction = route('club.tournaments.store-modal');
    $clubTeams = $clubTeams ?? collect();
    $countries = \App\Models\Country::orderBy('name')->get(['id', 'name']);
    $states = collect();
    $cities = collect();
    
    // Sample blog posts data - replace with your actual data source
    $blogPosts = [
        ['id' => 1, 'title' => 'How Our Tournament Engine Powers Club Growth', 'content' => 'Write your intro paragraph here. Keep it short (2-3 lines). You can paste content and style it in Figma.', 'tag1' => 'tag 1', 'tag2' => 'tag 2'],
        ['id' => 2, 'title' => 'Building the Perfect Training Schedule', 'content' => 'Discover effective training methodologies that improve player performance. You can paste content and style it in Figma.', 'tag1' => 'training', 'tag2' => 'schedule'],
        ['id' => 3, 'title' => 'Player Development Strategies for Modern Clubs', 'content' => 'Learn how to develop players using data-driven insights. Keep it short (2-3 lines). You can paste content and style it.', 'tag1' => 'development', 'tag2' => 'strategy'],
        ['id' => 4, 'title' => 'Financial Management for Sports Organizations', 'content' => 'Master the art of financial planning and budget management. Write your intro paragraph here. Keep it short.', 'tag1' => 'finance', 'tag2' => 'management'],
        ['id' => 5, 'title' => 'Digital Transformation in Sports', 'content' => 'Embrace technology to streamline operations and enhance performance. You can paste content and style it in Figma.', 'tag1' => 'digital', 'tag2' => 'tech'],
        ['id' => 6, 'title' => 'Building Strong Community Partnerships', 'content' => 'Create lasting relationships with local communities and sponsors. Write your intro paragraph here. Keep it short.', 'tag1' => 'community', 'tag2' => 'partnership'],
        ['id' => 7, 'title' => 'Advanced Analytics for Player Performance', 'content' => 'Utilize data analytics to maximize player potential and team success. You can paste content and style it.', 'tag1' => 'analytics', 'tag2' => 'performance'],
        ['id' => 8, 'title' => 'Event Management Best Practices', 'content' => 'Learn how to organize successful tournaments and events. Write your intro paragraph here. Keep it short.', 'tag1' => 'events', 'tag2' => 'management'],
        ['id' => 9, 'title' => 'Youth Development Programs That Work', 'content' => 'Implement effective youth development strategies for long-term success. You can paste content and style it.', 'tag1' => 'youth', 'tag2' => 'development'],
        ['id' => 10, 'title' => 'Marketing Your Sports Organization', 'content' => 'Build your brand and attract more members through strategic marketing. Write your intro paragraph here.', 'tag1' => 'marketing', 'tag2' => 'branding'],
    ];
@endphp

<style>
/* Light Theme */
[data-theme='light'] .blog-builder-modal {
    background-color: #ffffff;
    color: #1f2937;
}

[data-theme='light'] .modal-content {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #e5e7eb !important;
    padding: 24px !important;
}

[data-theme='light'] .blog-builder-header {
    /* background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb; */
    color: #1f2937;
}

[data-theme='light'] .blog-builder-title {
    color: #1f2937;
    font-weight: 600;
}

[data-theme='light'] .blog-builder-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
}

[data-theme='light'] .post-card {
    background-color: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

[data-theme='light'] .post-card:hover {
    background-color: #f1f5f9;
    border-color: #d1d5db;
}

[data-theme='light'] .post-title {
    color: #1f2937;
    font-weight: 600;
}

[data-theme='light'] .post-content {
    color: #6b7280;
}

[data-theme='light'] .post-tag {
    background-color: #e0f2fe;
    color: #0891b2;
    border-radius: 8px;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

[data-theme='light'] .read-more-btn {
    background-color: #1f2937;
    color: #ffffff;
    border-radius: 8px;
    padding: 6px 16px;
    font-size: 0.875rem;
    font-weight: 700;
    border: none;
    min-width: 200px;
    transition: all 0.2s;
}

[data-theme='light'] .read-more-btn:hover {
    background-color: #374151;
    color: #ffffff;
}

[data-theme='light'] .pagination-container {
    background-color: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

[data-theme='light'] .pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

[data-theme='light'] .pagination-btn {
    background-color: #374151;
    color: #ffffff;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='light'] .pagination-btn:hover {
    background-color: #1f2937;
}

[data-theme='light'] .pagination-btn.active {
    background-color: #0891b2;
    color: #ffffff;
}

[data-theme='light'] .pagination-btn:disabled {
    background-color: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

/* Dark Theme */
[data-theme='dark'] .blog-builder-modal {
    background-color: #0f172a;
    color: #e5e7eb;
}

[data-theme='dark'] .modal-content {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
    border: 1px solid #1f2937 !important;
      padding: 24px !important;
}

[data-theme='dark'] .blog-builder-header {
    /* background-color: #1e293b;
    border-bottom: 1px solid #374151; */
    color: #e5e7eb;
}

[data-theme='dark'] .blog-builder-title {
    color: #e5e7eb;
    font-weight: 600;
}

[data-theme='dark'] .blog-builder-subtitle {
    color: #94a3b8;
    font-size: 0.875rem;
}

[data-theme='dark'] .post-card {
    background-color: #1e293b;
    border: 1px solid #374151;
    border-radius: 12px;
}

[data-theme='dark'] .post-card:hover {
    background-color: #334155;
    border-color: #475569;
}

[data-theme='dark'] .post-title {
    color: #e5e7eb;
    font-weight: 600;
}

[data-theme='dark'] .post-content {
    color: #94a3b8;
}

[data-theme='dark'] .post-tag {
    background-color: #0c4a6e;
    color: #38bdf8;
    border-radius: 8px;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

[data-theme='dark'] .read-more-btn {
    background: radial-gradient(5000% 5000% at 5000% 0%, #3B5D78 15%, #34E6D4 85.1%);
    color: #0f172a;
    border-radius: 8px;
    padding: 6px 16px;
    font-size: 0.875rem;
    font-weight: 700;
    border: none;
    min-width: 200px;
    transition: all 0.2s;
}

[data-theme='dark'] .read-more-btn:hover {
    background-color: #06b6d4;
    color: #0f172a;
}

[data-theme='dark'] .pagination-container {
    background-color: #1e293b;
    border-top: 1px solid #374151;
}

[data-theme='dark'] .pagination-info {
    color: #94a3b8;
    font-size: 0.875rem;
}

[data-theme='dark'] .pagination-btn {
    background-color: #374151;
    color: #e5e7eb;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='dark'] .pagination-btn:hover {
    background-color: #475569;
}

[data-theme='dark'] .pagination-btn.active {
    background-color: #3B5D78;
    color: #0f172a;
}

[data-theme='dark'] .pagination-btn:disabled {
    background-color: #1f2937;
    color: #6b7280;
    cursor: not-allowed;
}

/* Close button theme support */
[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}

/* Common styles */
.blog-builder-modal {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.post-card {
    transition: all 0.2s ease;
    margin-bottom: 1rem;
    padding: 1.5rem;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
}

.drop-image-area {
    width: 120px;
    height: 120px;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 1.5rem;
    text-align: center;
}

.post-tags {
    display: flex;
    gap: 0.5rem;
    margin: 0.75rem 0;
    flex-wrap: wrap;
}

.post-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-top: 1rem;
}

.pagination-container {
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .modal-xl {
        max-width: 95vw !important;
    }
    
    .blog-builder-header .d-flex {
        gap: 1rem;
    }
    
    .btn-close {
        align-self: flex-end;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
}

@media (max-width: 992px) {
    .modal-xl {
        max-width: 90vw !important;
    }
    
    .post-card {
        padding: 1rem;
    }
    
    .drop-image-area {
        width: 100px;
        height: 100px;
        margin-right: 1rem;
    }
    
    .blog-builder-title {
        font-size: 1.25rem !important;
    }
    
    .blog-builder-subtitle {
        font-size: 0.8rem !important;
    }
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95vw !important;
        margin: 0.5rem !important;
    }
    
    .modal-content {
        padding: 16px !important;
    }
    
    .blog-builder-header {
        padding: 1rem !important;
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .post-card {
        flex-direction: column;
        padding: 1rem;
        text-align: center;
    }
    
    .drop-image-area {
        margin-right: 0;
        margin-bottom: 1rem;
        width: 80px;
        height: 80px;
        align-self: center;
    }
    
    .post-title {
        font-size: 1.1rem !important;
        text-align: left;
    }
    
    .post-content {
        font-size: 0.875rem !important;
        text-align: left;
    }
    
    .post-tags {
        justify-content: flex-start;
    }
    
    .post-actions {
        justify-content: flex-start;
    }
    
    /* Header responsive adjustments */
    .blog-builder-header .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .blog-builder-header .d-flex.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
        width: 100%;
    }
    
    .pagination-controls {
        gap: 0.25rem;
    }
    
    .pagination-btn {
        width: 28px !important;
        height: 28px !important;
        font-size: 0.75rem !important;
    }
    
    .pagination-info {
        font-size: 0.75rem !important;
    }
    
    .blog-builder-title {
        font-size: 1.1rem !important;
    }
    
    .blog-builder-subtitle {
        font-size: 0.75rem !important;
        line-height: 1.4;
    }
}

@media (max-width: 576px) {
    .modal-xl {
        max-width: 100vw !important;
        margin: 0 !important;
        height: 100vh !important;
    }
    
    .modal-dialog {
        margin: 0 !important;
        height: 100vh !important;
    }
    
    .modal-content {
        height: 100vh !important;
        border-radius: 0 !important;
        padding: 12px !important;
    }
    
    .modal-body {
        max-height: calc(100vh - 200px) !important;
        padding: 0.75rem !important;
    }
    
    .post-card {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .drop-image-area {
        width: 120px;
        height: 120px;
        font-size: 0.7rem;
    }
    
    .post-title {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .post-content {
        font-size: 0.8rem !important;
        line-height: 1.4;
    }
    
    .post-tag {
        font-size: 0.7rem !important;
        padding: 3px 8px !important;
    }
    
    .read-more-btn {
        font-size: 0.75rem !important;
        padding: 5px 12px !important;
    }
    
    .blog-builder-header {
        padding: 0.75rem !important;
    }
    
    .blog-builder-title {
        font-size: 1rem !important;
        line-height: 1.3;
    }
    
    .blog-builder-subtitle {
        font-size: 0.7rem !important;
        line-height: 1.3;
    }
    
    .pagination-info {
        font-size: 0.7rem !important;
    }
    
    .pagination-btn {
        width: 24px !important;
        height: 24px !important;
        font-size: 0.7rem !important;
    }
}

/* Landscape phone adjustments */
/* @media (max-width: 896px) and (orientation: landscape) {
    .modal-body {
        max-height: calc(100vh - 150px) !important;
    }
} */

/* Ensure proper spacing on all devices */
@media (min-width: 576px) {
    .post-card {
        flex-direction: row;
    }
    
    .drop-image-area {
        margin-right: 1.5rem;
        margin-bottom: 0;
    }
       .f-modal > .modal-dialog > .modal-content{
        padding: 12px !important;

    }
}
.f-modal > .modal-dialog > .modal-content{
    padding: 24px;
}
</style>

<div class="f-modal modal fade" id="frontBlogPosts" tabindex="-1" aria-labelledby="frontBlogPostsLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content blog-builder-modal">
            <!-- Header -->
            <div class="blog-builder-header p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="blog-builder-title mb-1">Blog Post Builder — 5 sections front page</h4>
                        <p class="blog-builder-subtitle mb-0">
                            Edit text, swap images, and export slices. Use the window selector below to mark which front-page slot this post targets.
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="z-index: 99999999;"><i class="fas fa-xmark"></i></button>
                </div>
                
                <!-- Pagination Controls moved here -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="pagination-info">Post to Front-Page Window:</span>
                        <div class="pagination-controls">
                            <button type="button" class="pagination-btn" id="prevBtn" onclick="changePage(-1)">‹</button>
                            <div id="pageNumbers" class="d-flex gap-1">
                                <!-- Page numbers will be dynamically inserted -->
                            </div>
                            <button type="button" class="pagination-btn" id="nextBtn" onclick="changePage(1)">›</button>
                        </div>
                    </div>
                    <div class="pagination-info">
                        <span id="currentPageInfo">Set the block set to be active</span>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body p-4" style="overflow-y: auto;">
                <div id="blogPostsContainer">
                    <!-- Blog posts will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Blog posts data
    const blogPosts = @json($blogPosts);
    const postsPerPage = 5;
    let currentPage = 1;
    const totalPages = Math.ceil(blogPosts.length / postsPerPage);

    // Ensure modal inherits current admin theme
    const modal = document.getElementById('frontBlogPosts');
    
    function applyThemeToModal() {
        if (modal) {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            modal.setAttribute('data-theme', currentTheme);
            
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.setAttribute('data-theme', currentTheme);
            }
        }
    }
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            applyThemeToModal();
            renderPage(currentPage);
        });
        
        applyThemeToModal();
    }

    // Listen for theme changes and update modal
    document.addEventListener('themeChange', function(e) {
        if (modal) {
            modal.setAttribute('data-theme', e.detail.theme);
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.setAttribute('data-theme', e.detail.theme);
            }
        }
    });

    // Render blog posts for current page
    function renderPage(page) {
        const container = document.getElementById('blogPostsContainer');
        const startIndex = (page - 1) * postsPerPage;
        const endIndex = startIndex + postsPerPage;
        const postsToShow = blogPosts.slice(startIndex, endIndex);

        container.innerHTML = postsToShow.map((post, index) => `
            <div class="post-card d-flex">
                <div class="drop-image-area">
                    <span style="color: #94a3b8; font-size: 0.875rem;">Drop Image</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="post-title mb-2">Post Title — Section ${startIndex + index + 1}</h5>
                    <p class="post-content mb-3">${post.content}</p>
                    <div class="post-tags">
                        <span class="post-tag">${post.tag1}</span>
                        <span class="post-tag">${post.tag2}</span>
                    </div>
                    <div class="post-actions">
                        <button type="button" class="read-more-btn">Read More</button>
                    </div>
                </div>
            </div>
        `).join('');

        updatePagination();
    }

    // Update pagination controls
    function updatePagination() {
        // Update page numbers
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = '';
        
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => goToPage(i);
            pageNumbersContainer.appendChild(pageBtn);
        }

        // Update prev/next buttons
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;
    }

    // Navigation functions
    window.changePage = function(direction) {
        const newPage = currentPage + direction;
        if (newPage >= 1 && newPage <= totalPages) {
            goToPage(newPage);
        }
    };

    window.goToPage = function(page) {
        currentPage = page;
        renderPage(currentPage);
    };

    // Initialize first page
    renderPage(currentPage);
});
</script>
