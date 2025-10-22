@extends('layouts.admin')
@section('title', 'Edit Injury Report')

@section('content')
@include('admin.injury_reports._form', [
    'action' => route('admin.injury_reports.update.post', $injuryReport->id),
    'method' => 'POST',
    'report' => $injuryReport
])
@endsection
