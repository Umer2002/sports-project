@extends('layouts.club-dashboard')
@section('title','Club Setup')
@section('page_title', 'Club Setup')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Complete Your Club Setup</h4>
      </div>
        <div class="card-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif
          <form method="POST" action="{{ route('club.storeSetup') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Club Name</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name.' Club') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Sport</label>
              <select name="sport_id" class="form-select" required>
                <option value="">-- Select Sport --</option>
                @foreach($sports as $id => $name)
                  <option value="{{ $id }}" {{ old('sport_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
            </div>
            <div class="text-end">
              <button class="btn btn-success">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

