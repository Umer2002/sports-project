@extends('layouts.admin')
@section('title', 'Ads')

@section('content')
<section class="content-header">
    <h1>Ads</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Ads</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">All Ads</h4>
        <a href="{{ route('admin.ads.create') }}" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Add Ad
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Media</th>
                        <th>Link</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ads as $ad)
                        <tr>
                            <td>{{ $ad->title }}</td>
                            @if($ad->type == 'image' && $ad->media)
                                <td>
                                    @php
                                        $imagePath = 'storage/' . $ad->media;
                                    @endphp
                                    @if(file_exists(public_path($imagePath)))
                                        <img src="{{ asset($imagePath) }}" alt="{{ $ad->title }}" width="80">
                                    @else
                                        <span class="text-muted">Image not found</span>
                                    @endif
                                </td>
                            @elseif($ad->type == 'video' && $ad->media)
                                <td>
                                    @php
                                        $videoPath = 'storage/' . $ad->media;
                                        $ext = strtolower(pathinfo($ad->media, PATHINFO_EXTENSION));
                                        $mimeSuffix = $ext === 'mov' ? 'quicktime' : $ext;
                                    @endphp
                                    @if(file_exists(public_path($videoPath)))
                                        <video width="140" height="84" controls preload="metadata">
                                            <source src="{{ asset($videoPath) }}" type="video/{{ $mimeSuffix }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <span class="text-muted">Video not found</span>
                                    @endif
                                </td>
                            @else
                                <td><span class="text-muted">No media</span></td>
                            @endif
                            <td><a href="{{ $ad->link }}" target="_blank">{{ $ad->link }}</a></td>
                            <td>{{ $ad->active ? 'Yes' : 'No' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this ad?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No ads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
