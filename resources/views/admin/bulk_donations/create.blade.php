@extends('layouts.admin')

@section('title', 'Create Bulk Donation')

@section('header_styles')
<link href="{{ asset('vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" />
<link href="{{ asset('vendors/iCheck/css/all.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="header">
                <h2><i class="material-icons">volunteer_activism</i> Create Bulk Donation</h2>
                <ul class="header-dropdown">
                    <li><a href="{{ route('admin.bulk_donations.index') }}">View All Donations</a></li>
                </ul>
            </div>

            <div class="body">
                <form action="{{ route('admin.bulk_donations.store') }}" method="POST" id="form_controls">
                    @csrf

                    {{-- Club Selection --}}
                    <h2 class="card-inside-title">Select Club</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="club_id" id="club" class="form-control" required>
                                <option value="">Select a Club</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Age Input --}}
                    <h2 class="card-inside-title">Select Age</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="age" id="age" class="form-control" min="1" required>
                        </div>
                    </div>

                    {{-- Player List (Checkboxes) --}}
                    <h2 class="card-inside-title">Select Players</h2>
                    <div class="form-group" id="players-list">
                        <!-- Player checkboxes will be injected here -->
                    </div>

                    {{-- Select All --}}
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="select-all-players" class="form-check-input">
                            <label for="select-all-players" class="form-check-label">Select All Players</label>
                        </div>
                    </div>

                    {{-- Donation Amount --}}
                    <h2 class="card-inside-title">Donation Amount</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="donation_amount" id="donation_amount" class="form-control" required>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="form-group text-end">
                        <button type="submit" class="btn btn-primary">Donate</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('vendors/iCheck/js/icheck.js') }}"></script>

<script>
$(document).ready(function () {
    $('#club, #age').on('change', function () {
        const clubId = $('#club').val();
        const age = $('#age').val();

        if (clubId && age) {
            $.ajax({
                url: "{{ url('admin/bulk_donations/get-players') }}",
                method: "GET",
                data: { club_id: clubId, age: age },
                success: function (players) {
                    $('#players-list').empty();
                    players.forEach(player => {
                        const checkboxHTML = `
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="player_${player.id}" name="players[]" value="${player.id}" data-paypal-email="${player.paypal_email}">
                                <label class="form-check-label" for="player_${player.id}">${player.name}</label>
                            </div>
                        `;
                        $('#players-list').append(checkboxHTML);
                    });
                }
            });
        }
    });

    $('#select-all-players').on('change', function () {
        const checked = $(this).is(':checked');
        $('#players-list input[type="checkbox"]').prop('checked', checked);
    });

    $('#form_controls').on('submit', function (e) {
        const playersWithoutPaypal = [];

        $('#players-list input[type="checkbox"]:checked').each(function () {
            const email = $(this).data('paypal-email');
            if (!email) {
                playersWithoutPaypal.push($(this).siblings('label').text());
                $(this).prop('checked', false);
            }
        });

        if (playersWithoutPaypal.length > 0) {
            alert('The following players do not have a PayPal email and have been deselected:\n' + playersWithoutPaypal.join(', ') + '.\nYou can now proceed with the remaining players.');
            e.preventDefault();
        }
    });
});
</script>
@endsection
