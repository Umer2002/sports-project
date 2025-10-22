@extends('layouts.admin')

@section('title', 'Player Stats Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Sport Stats Configuration</h2>
                    <p class="text-muted mb-0">Configure which 4 stats to track for each sport</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.player-stats.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Sport Configuration
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Sport Configurations Table Section -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sport Stat Configurations</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDeleteBtn" disabled>
                                <i class="bi bi-trash me-1"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($playerStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Sport</th>
                                        <th>Stat 1</th>
                                        <th>Stat 2</th>
                                        <th>Stat 3</th>
                                        <th>Stat 4</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($playerStats as $stat)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input stat-checkbox" 
                                                       value="{{ $stat->id }}">
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $stat->sport->name }}</div>
                                                <small class="text-muted">Sport Configuration</small>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $stat->stat1 ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $stat->stat2 ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $stat->stat3 ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $stat->stat4 ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $stat->created_at->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ $stat->created_at->format('g:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.player-stats.show', $stat) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.player-stats.edit', $stat) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.player-stats.destroy', $stat) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this stat?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $playerStats->firstItem() }} to {{ $playerStats->lastItem() }} of {{ $playerStats->total() }} results
                                </div>
                                <div>
                                    {{ $playerStats->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-gear text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="mb-2">No Sport Configurations Found</h5>
                            <p class="text-muted mb-4">Configure which 4 stats to track for each sport.</p>
                            <a href="{{ route('admin.player-stats.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add First Sport Configuration
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Confirm Bulk Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected player stats? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This will permanently remove all selected statistics.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="bulkDeleteForm" action="{{ route('admin.player-stats.bulk-delete') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const statCheckboxes = document.querySelectorAll('.stat-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        statCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Individual checkbox functionality
    statCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkDeleteButton();
            updateSelectAllCheckbox();
        });
    });

    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.stat-checkbox:checked');
        bulkDeleteBtn.disabled = checkedBoxes.length === 0;
    }

    function updateSelectAllCheckbox() {
        const checkedBoxes = document.querySelectorAll('.stat-checkbox:checked');
        const totalBoxes = statCheckboxes.length;
        
        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    // Bulk delete functionality
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.stat-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        // Add hidden inputs for selected IDs
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'stat_ids[]';
            input.value = checkbox.value;
            bulkDeleteForm.appendChild(input);
        });

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
        modal.show();
    });
});
</script>
@endpush
