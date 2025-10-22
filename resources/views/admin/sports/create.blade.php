@extends('layouts.admin')

@section('title', isset($sport) ? 'Edit Sport' : 'Create Sport')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ isset($sport) ? 'Edit Sport' : 'Create Sport' }}</h2>
                <ul class="header-dropdown">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#" onClick="return false;">Add</a></li>
                            <li><a href="#" onClick="return false;">Edit</a></li>
                            <li><a href="#" onClick="return false;">Delete</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <form action="{{ isset($sport) ? route('admin.sports.update', $sport) : route('admin.sports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($sport)) @method('PUT') @endif

                    <h2 class="card-inside-title">Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $sport->name ?? '') }}" required placeholder="Enter sport name">
                        </div>
                    </div>

                    <h2 class="card-inside-title">Description</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea name="description" class="form-control no-resize" rows="4" placeholder="Enter sport description">{{ old('description', $sport->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Icon (optional)</h2>
                    <div class="form-group">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>File</span>
                                <input type="file" name="icon_path">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Upload icon (optional)">
                            </div>
                        </div>
                        @if(isset($sport) && $sport->icon_path)
                            <img src="{{ asset('storage/' . $sport->icon_path) }}" width="50" class="mt-2">
                        @endif
                    </div>

                    <h2 class="card-inside-title">Top Sport</h2>
                    <div class="form-group">
                        <div class="form-check m-l-10">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_top_sport" class="form-check-input"
                                    {{ old('is_top_sport', $sport->is_top_sport ?? false) ? 'checked' : '' }}>
                                Mark as Top Sport
                                <span class="form-check-sign">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary m-t-15 waves-effect">
                        {{ isset($sport) ? 'Update' : 'Create' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
