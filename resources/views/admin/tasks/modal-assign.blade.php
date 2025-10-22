<div class="modal fade" id="assignModal{{ $task->id }}" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" action="{{ route('admin.tasks.assign', $task->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Assign Task: {{ $task->title }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Select User</label>
            <select name="assigned_to" class="form-select" required>
              <option value="">-- Select --</option>
              @foreach(App\Models\User::all() as $user)
              <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary">Assign</button>
        </div>
      </form>
    </div>
  </div>
