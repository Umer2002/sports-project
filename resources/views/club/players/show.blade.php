@extends('layouts.club-dashboard')
@section('title', 'Player Details')
@section('page_title', 'Player Details')

@section('content')
<div class="row clearfix">
  <div class="col-lg-12">
    <div class="card shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title mb-0">
          @php
            $first   = trim($player->first_name ?? '');
            $last    = trim($player->last_name ?? '');
            $fallback= $player->user?->name ?? null;
            $full    = trim($first.' '.$last) ?: ($fallback ?? 'Player');
          @endphp
          {{ $full }}

          @if($player->position ?? false)
            <span class="badge bg-secondary ms-2">{{ $player->position }}</span>
          @endif
          @if(($player->jersey_number ?? false) !== null && $player->jersey_number !== '')
            <span class="badge bg-dark ms-2">#{{ $player->jersey_number }}</span>
          @endif
          @if($player->gender ?? false)
            <span class="badge bg-info ms-2 text-capitalize">{{ $player->gender }}</span>
          @endif
          @if(($player->age ?? null) !== null)
            <span class="badge bg-primary ms-2">{{ $player->age }} yrs</span>
          @elseif(!empty($player->dob))
            @php
              try { $age = \Carbon\Carbon::parse($player->dob)->age; } catch (\Throwable $e) { $age = null; }
            @endphp
            @if($age)
              <span class="badge bg-primary ms-2">{{ $age }} yrs</span>
            @endif
          @endif
        </h4>

        <div class="d-flex gap-2">
          <a href="{{ route('club.players.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-list me-1"></i> Back
          </a>
          <a href="{{ route('club.players.edit', $player) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit me-1"></i> Edit
          </a>
          <form action="{{ route('club.players.destroy', $player) }}" method="POST"
                onsubmit="return confirm('Delete this player?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
          </form>
        </div>
      </div>

      <div class="card-body">
        <div class="row g-4 align-items-start">
          {{-- Avatar / Photo & quick meta --}}
          <div class="col-md-3 text-center">
            <div class="avatar-wrapper mx-auto">
              @if($player->photo ?? false)
                <img src="{{ asset('storage/'.$player->photo) }}" class="img-fluid rounded-circle border" alt="Player Photo">
              @else
                @php
                  $i1 = strtoupper(mb_substr($first ?: ($fallback ?? 'P'), 0, 1));
                  $i2 = strtoupper(mb_substr($last ?: '', 0, 1));
                @endphp
                <div class="avatar-fallback rounded-circle d-flex align-items-center justify-content-center">
                  <span class="fw-bold fs-3">{{ $i1 }}{{ $i2 }}</span>
                </div>
              @endif
            </div>

            <div class="mt-3 small text-muted">
              Created: {{ $player->created_at?->format('d M Y') }}<br>
              Updated: {{ $player->updated_at?->format('d M Y') }}
            </div>

            {{-- Followers / Following (if user linked) --}}
            @if($player->user)
              <div class="d-flex justify-content-center gap-3 mt-3">
                <div class="text-center">
                  <div class="fw-bold">{{ $player->user->followers->count() }}</div>
                  <div class="small text-muted">Followers</div>
                </div>
                <div class="text-center">
                  <div class="fw-bold">{{ $player->user->following->count() }}</div>
                  <div class="small text-muted">Following</div>
                </div>
              </div>
            @endif
          </div>

          {{-- Details --}}
          <div class="col-md-9">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="mb-2">
                  <div class="text-muted small">Full Name</div>
                  <div class="fw-semibold">{{ $full }}</div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Email</div>
                  <div class="fw-semibold">
                    @php $email = $player->email ?? $player->user?->email; @endphp
                    @if($email)
                      <a href="mailto:{{ $email }}">{{ $email }}</a>
                    @else
                      —
                    @endif
                  </div>
                </div>

                @if($player->phone ?? false)
                <div class="mb-2">
                  <div class="text-muted small">Phone</div>
                  <div class="fw-semibold">
                    <a href="tel:{{ preg_replace('/\s+/', '', $player->phone) }}">{{ $player->phone }}</a>
                  </div>
                </div>
                @endif

                <div class="mb-2">
                  <div class="text-muted small">Sport</div>
                  <div class="fw-semibold">{{ $player->sport?->name ?? '—' }}</div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Club</div>
                  <div class="fw-semibold">{{ $player->club?->name ?? '—' }}</div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-2">
                  <div class="text-muted small">Position</div>
                  <div class="fw-semibold">{{ $player->position ?? '—' }}</div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Jersey Number</div>
                  <div class="fw-semibold">{{ $player->jersey_number ?? '—' }}</div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Height / Weight</div>
                  <div class="fw-semibold">
                    @php
                      $h = $player->height_cm ?? null;
                      $w = $player->weight_kg ?? null;
                    @endphp
                    {{ $h ? $h.' cm' : '—' }} / {{ $w ? $w.' kg' : '—' }}
                  </div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Preferred Foot</div>
                  <div class="fw-semibold text-capitalize">{{ $player->preferred_foot ?? '—' }}</div>
                </div>

                <div class="mb-2">
                  <div class="text-muted small">Teams</div>
                  <div class="fw-semibold">
                    @if($player->teams && $player->teams->count())
                      @foreach($player->teams as $t)
                        <span class="badge bg-light text-dark me-1 mb-1">{{ $t->name }}</span>
                      @endforeach
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>

              {{-- Bio --}}
              <div class="col-12">
                <div class="mb-2">
                  <div class="text-muted small">Bio</div>
                  <div class="fw-normal">{{ $player->bio ?? '—' }}</div>
                </div>
              </div>

              {{-- Social Links (array or JSON string, tolerant) --}}
              <div class="col-12">
                <div class="mb-2">
                  <div class="text-muted small">Social</div>
                  <div class="fw-normal">
                    @php
                      $links = $player->social_links ?? $player->socail_links ?? null; // handle typo too
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
                              <span class="text-muted">{{ ucfirst(str_replace('_',' ',$key)) }}:</span>
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
          </div> {{-- /col-md-9 --}}
        </div> {{-- /row g-4 --}}
      </div> {{-- /card-body --}}
    </div> {{-- /card --}}
  </div>
</div>
@endsection

@push('styles')
<style>
  .card { border-radius: 1rem; }
  .form-label { font-weight: 600; }
  .avatar-wrapper { width: 180px; height: 180px; }
  .avatar-wrapper img { width: 180px; height: 180px; object-fit: cover; }
  .avatar-fallback {
    width: 180px; height: 180px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb;
  }
</style>
@endpush
