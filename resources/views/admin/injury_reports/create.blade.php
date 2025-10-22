@extends('layouts.admin')
@section('title', 'Create Injury Report')

@section('content')
@include('admin.injury_reports._form', ['action' => route('admin.injury_reports.store'), 'method' => 'POST'])
@endsection
