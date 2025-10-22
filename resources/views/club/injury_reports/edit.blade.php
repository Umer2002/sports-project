@extends('layouts.club-dashboard')
@section('title', 'Edit Injury Report')
@section('page_title', 'Edit Injury Report')
@section('content')
@include('club.injury_reports._form', [
    'action' => route('club.injury_reports.update', $injuryReport),
    'method' => 'PUT',
    'report' => $injuryReport
])
@endsection
