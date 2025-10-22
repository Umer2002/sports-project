<div class="mb-3">
    <label for="event_type" class="form-label">Event Type *</label>
    <input type="text" class="form-control" id="event_type" name="event_type" value="{{ old('event_type', $schedule->event_type ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="title" class="form-label">Title *</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $schedule->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $schedule->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="start_time" class="form-label">Start Time *</label>
    <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', isset($schedule) ? \Carbon\Carbon::parse($schedule->start_time)->format('Y-m-d\TH:i') : '') }}" required>
</div>

<div class="mb-3">
    <label for="end_time" class="form-label">End Time *</label>
    <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', isset($schedule) ? \Carbon\Carbon::parse($schedule->end_time)->format('Y-m-d\TH:i') : '') }}" required>
</div>

<div class="mb-3">
    <label for="location" class="form-label">Location</label>
    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $schedule->location ?? '') }}">
</div>

<div class="mb-3">
    <label for="background_color" class="form-label">Background Color</label>
    <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', $schedule->background_color ?? '#000000') }}">
</div>

<div class="mb-3">
    <label for="repetition" class="form-label">Repetition</label>
    <input type="text" class="form-control" id="repetition" name="repetition" value="{{ old('repetition', $schedule->repetition ?? '') }}">
</div>
