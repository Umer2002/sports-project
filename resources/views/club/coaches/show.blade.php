@extends('layouts.club-dashboard')
@section('title', 'Coach Details')
@section('page_title', 'Coach Details')

@section('content')
<div class="row clearfix">
  <div class="col-lg-12">
    <div class="card shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title mb-0">
          {{ $coach->first_name }} {{ $coach->last_name }}
          @if($coach->gender)
            <span class="badge bg-secondary ms-2 text-capitalize">{{ $coach->gender }}</span>
          @endif
          @if($coach->age)
            <span class="badge bg-info ms-2">{{ $coach->age }} yrs</span>
          @endif
        </h4>

        <div class="d-flex gap-2">
          <a href="{{ route('club.coaches.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-list me-1"></i> Back
          </a>
          <a href="{{ route('club.coaches.edit', $coach) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit me-1"></i> Edit
          </a>
          <form action="{{ route('club.coaches.destroy', $coach) }}" method="POST" onsubmit="return confirm('Delete this coach?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
          </form>
        </div>
      </div>

      <div class="card-body">
        <div class="row g-4 align-items-start">
          {{-- Avatar / Photo --}}
          <div class="col-md-3 text-center">
            <div class="avatar-wrapper mx-auto">
              @if($coach->photo)
                <img src="{{ asset('storage/' . $coach->photo) }}" class="img-fluid rounded-circle border" alt="Coach Photo">
              @else
                <div class="avatar-fallback rounded-circle d-flex align-items-center justify-content-center">
                  <span class="fw-bold fs-3">{{ strtoupper(substr($coach->first_name,0,1)) }}{{ strtoupper(substr($coach->last_name,0,1)) }}</span>
                </div>
              @endif
            </div>
            <div class="mt-3 small text-muted">
              Created: {{ $coach->created_at?->format('d M Y') }}<br>
              Updated: {{ $coach->updated_at?->format('d M Y') }}
            </div>
          </div>

          {{-- Details --}}
          <div class="col-md-9">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="mb-2">
                  <div class="text-muted small">Full Name</div>
                  <div class="fw-semibold">{{ $coach->first_name }} {{ $coach->last_name }}</div>
                </div>
                <div class="mb-2">
                  <div class="text-muted small">Email</div>
                  <div class="fw-semibold">
                    <a href="mailto:{{ $coach->email }}">{{ $coach->email }}</a>
                  </div>
                </div>
                <div class="mb-2">
                  <div class="text-muted small">Phone</div>
                  <div class="fw-semibold">
                    <a href="tel:{{ preg_replace('/\s+/', '', $coach->phone) }}">{{ $coach->phone }}</a>
                  </div>
                </div>
                <div class="mb-2">
                  <div class="text-muted small">Sport</div>
                  <div class="fw-semibold">{{ $coach->sport?->name ?? '—' }}</div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-2">
                  <div class="text-muted small">Location</div>
                  <div class="fw-semibold">
                    {{-- Display via relations if you added them on Coach model --}}
                    @php
                      $country = $coach->country->name ?? null;
                      $state   = $coach->state->name   ?? null;
                      $city    = $coach->city->name    ?? null;
                    @endphp
                    {{ $city ?: '—' }}{{ $state ? ', '.$state : '' }}{{ $country ? ', '.$country : '' }}
                  </div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">User Account</div>
                  <div class="fw-semibold">
                    @if($coach->user)
                      {{ $coach->user->name }} <span class="text-muted">(#{{ $coach->user->id }})</span>
                    @else
                      —
                    @endif
                  </div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Teams</div>
                  <div class="fw-semibold">
                    @if($coach->teams && $coach->teams->count())
                      @foreach($coach->teams as $t)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $t->name }}</span>
                      @endforeach
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="mb-2">
                  <div class="text-muted small">Bio</div>
                  <div class="fw-normal">{{ $coach->bio ?: '—' }}</div>
                </div>
              </div>

              <div class="col-12">
                <div class="mb-2">
                  <div class="text-muted small">Social Links</div>
                  <div class="fw-normal">
                    @php
                      // socail_links is cast to array in your model; be tolerant if it’s null/string
                      $links = $coach->socail_links;
                      if (is_string($links)) {
                        $decoded = json_decode($links, true);
                        if (json_last_error() === JSON_ERROR_NONE) $links = $decoded;
                      }
                    @endphp
                    @if(is_array($links) && count($links))
                      <ul class="list-unstyled mb-0">
                        @foreach($links as $key => $val)
                          @if($val)
                            <li class="mb-1">
                              <span class="text-muted">{{ ucfirst($key) }}:</span>
                              @if(filter_var($val, FILTER_VALIDATE_URL))
                                <a href="{{ $val }}" target="_blank" rel="noopener">{{ $val }}</a>
                              @else
                                {{ $val }}
                              @endif
                            </li>
                          @endif
                        @endforeach
                      </ul>
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>

            </div> {{-- /row --}}
          </div> {{-- /col-9 --}}
        </div> {{-- /row g-4 --}}
      </div> {{-- /card-body --}}
    </div> {{-- /card --}}
  </div>
</div>
@endsection

@push('styles')
<style>
  .avatar-wrapper { width: 180px; height: 180px; }
  .avatar-wrapper img { width: 180px; height: 180px; object-fit: cover; }
  .avatar-fallback {
    width: 180px; height: 180px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb;
  }
</style>
@endpush
