@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Promotional Activities</h2>
        <a href="{{ route('volunteer.promotions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Promotion</a>
    </div>

    <div class="row g-4">
        @forelse($promotions as $promo)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">{{ $promo->title }}</h5>
                        <p class="text-muted small mb-2">{{ Str::limit($promo->description, 120) }}</p>
                        @if($promo->youtube_url)
                            <a href="{{ $promo->youtube_url }}" target="_blank" class="small mb-2"><i class="fab fa-youtube"></i> YouTube</a>
                        @endif
                        @if($promo->video_path)
                            <video controls style="width:100%;max-height:160px;" class="mb-2">
                                <source src="{{ asset('storage/'.$promo->video_path) }}" type="video/mp4">
                            </video>
                        @endif
                        <div class="mt-auto">
                            <div class="d-flex gap-2">
                                @php $shareUrl = urlencode(url('/')); $text = urlencode($promo->share_text ?: $promo->title); @endphp
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://twitter.com/intent/tweet?text={{ $text }}&url={{ $shareUrl }}"><i class="fab fa-x-twitter"></i></a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"><i class="fab fa-facebook"></i></a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://api.whatsapp.com/send?text={{ $text }}%20{{ $shareUrl }}"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center py-5 text-muted">No promotions yet</div></div>
            </div>
        @endforelse
    </div>

    @if($promotions->hasPages())
        <div class="mt-3">{{ $promotions->links() }}</div>
    @endif
</div>
@endsection
