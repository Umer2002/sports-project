@extends('layouts.admin')
@section('title')
    @lang('product.title.create')
    @parent
@stop

@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/pages/form_layouts.css') }}" />
@stop

@section('content')
<section class="content-header">
    <h1>@lang('product.title.create')</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16" data-color="#000"></i>@lang('general.dashboard')</a></li>
        <li><a href="#">@lang('product.title.products')</a></li>
        <li class="active">@lang('product.title.create')</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>@lang('product.title.create')</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        @isset($product) @method('PUT') @endisset

                        <div class="form-group">
                            <label for="name">@lang('product.form.name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name ?? old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">@lang('product.form.category')</label>
                            <select name="category_id" class="form-select">
                                <option value="">@lang('product.form.select_category')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ isset($product) && $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">@lang('product.form.price')</label>
                            <input type="number" name="price" class="form-control" value="{{ $product->price ?? old('price') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">@lang('product.form.stock')</label>
                            <input type="number" name="stock" class="form-control" value="{{ $product->stock ?? old('stock') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">@lang('product.form.description')</label>
                            <textarea name="description" class="form-control" rows="5">{{ $product->description ?? old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">@lang('product.form.image')</label>
                            <input type="file" name="image" class="form-control">
                            @isset($product)
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="Product Image" width="100">
                                @endif
                            @endisset
                        </div>

                        <br />
                        <button type="submit" class="btn btn-success">@lang('button.save')</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">@lang('button.cancel')</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
