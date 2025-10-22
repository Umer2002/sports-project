@extends('layouts.admin')
@section('content')
<section class="content-header">
    <h1>PayPal Links</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li><a href="{{ route('admin.bulk_donations.index') }}">Bulk Donations</a></li>
        <li class="active">PayPal Links</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">PayPal Donation Links</h4>
                </div>
                <div class="card-body">
                    @if(!empty($paypalLinks))
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Player Name</th>
                                    <th>PayPal Email</th>
                                    <th>Donation Amount</th>
                                    <th>PayPal Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paypalLinks as $link)
                                    <tr>
                                        <td>{{ $link['player_name'] }}</td>
                                        <td>{{ $link['paypal_email'] }}</td>
                                        <td>${{ number_format($link['amount'], 2) }}</td>
                                        <td>
                                            <a href="{{ $link['link'] }}" target="_blank" class="btn btn-primary">
                                                Donate via PayPal
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No PayPal links generated.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@stop
