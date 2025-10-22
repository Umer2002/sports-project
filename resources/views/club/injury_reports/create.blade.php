@extends('layouts.club-dashboard')
@section('title', 'Create Injury Report')

@section('content')
@include('club.injury_reports._form', ['action' => route('club.injury_reports.store'), 'method' => 'POST'])
@endsection
