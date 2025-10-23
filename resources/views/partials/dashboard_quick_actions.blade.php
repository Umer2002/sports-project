
<!-- Action Button section -->
<div class="action-widget p-4 mb-0">
    <div class="section-header">
        <h2 class="section-title">Heading</h2>
    </div>
    <div class="d-flex flex-wrap gap-3 w-100">
        <a href="{{ route('admin.clubs.create') }}" class="action-btn green">
            <i class="fas fa-plus"></i> Add Club
        </a>
        <button type="button" class="action-btn cyan" data-bs-toggle="modal" data-bs-target="#assignAward">
            <a href="{{ route('admin.teams.wizard.step1') }}" style="color: white !important;">
                <i class="fas fa-user-plus"></i> Add Team
            </a>
        </button>
        <button type="button" class="action-btn teal" data-bs-toggle="modal"
            data-bs-target="#createTournamentModal">
            <i class="fas fa-sitemap"></i> Tournament Engine
        </button>
        <button type="button" class="action-btn blue" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
            <i class="fas fa-tasks"></i> To Do Tasks
        </button>
        <button type="button" class="action-btn teal" data-bs-toggle="modal"
            data-bs-target="#frontBlogPosts">
            <i class="fas fa-blog"></i> Front Blogs
        </button>
        <button type="button" class="action-btn teal" data-bs-toggle="modal"
            data-bs-target="#createBlogPostModel">
            <i class="fas fa-swatchbook"></i> Blog Post
        </button>

         <button type="button" class="action-btn teal" data-bs-toggle="modal"
            data-bs-target="#assigntoCalendar">
           <i class="fas fa-opencart"></i> Paid Training Ads
        </button>
        <button type="button" class="action-btn yellow" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
            <a href="{{ route('admin.tournaments.index') }}" style="color: white !important;">
                <i class="fas fa-folder"></i> Tournament Directory
            </a>
        </button>
    </div>
</div>

<style>
    /* Remove vertical gaps between right column divs */
    .right-column>div {
        margin-bottom: 0 !important;
    }

    .right-column {
        justify-content: flex-start;
    }

    .right-column>div:not(:last-child) {
        margin-bottom: 1rem !important;
    }

    /* Ensure chat container has no bottom margin */
    .chat-container {
        margin-bottom: 0 !important;
    }

    /* Ensure tournament engine card has minimal spacing */
    .gradient-card {
        margin-bottom: 0 !important;
    }

    /* Ensure task card has no top margin */
    .task-card {
        margin-top: 0 !important;
    }

    /* Action buttons styling */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        color: white !important;
        border: none;
        cursor: pointer;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: white !important;
    }

    .action-btn i {
        color: white !important;
    }

    .action-btn.green {
        background: linear-gradient(135deg, #4CAF50, #45a049);
    }

    .action-btn.cyan {
        background: linear-gradient(135deg, #00BCD4, #0097A7);
    }

    .action-btn.teal {
        background: linear-gradient(135deg, #009688, #00796B);
    }

    .action-btn.blue {
        background: linear-gradient(135deg, #2196F3, #1976D2);
    }

    .action-btn.orange {
        background: linear-gradient(135deg, #FF9800, #F57C00);
    }

    .action-btn.purple {
        background: linear-gradient(135deg, #9C27B0, #7B1FA2);
    }

    .action-btn.red {
        background: linear-gradient(135deg, #F44336, #D32F2F);
    }

    /* Progress bar gradients */
    .progress-bar.teal-grad {
        background: linear-gradient(90deg, #20c997 0%, #17a2b8 100%);
    }

    .progress-bar.purple-grad {
        background: linear-gradient(90deg, #6f42c1 0%, #e83e8c 100%);
    }

    /* Modal player selection styles */
    .player-item {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .player-item:last-child {
        border-bottom: none;
    }

    .player-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }

    .player-item.selected {
        background-color: #e3f2fd;
        border-left: 3px solid #007bff;
    }

    .player-item.selected .form-check-input {
        background-color: #007bff;
        border-color: #007bff;
    }

    .player-list {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    .player-list::-webkit-scrollbar {
        width: 6px;
    }

    .player-list::-webkit-scrollbar-track {
        background: #a3a0a0;
        border-radius: 3px;
    }

    .player-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .player-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .chip-badge {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
    }

    .visibility-btn {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .visibility-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Start button text color */
    .start-btn {
        color: white !important;
    }
</style>