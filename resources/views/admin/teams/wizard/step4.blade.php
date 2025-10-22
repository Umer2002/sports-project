@extends('layouts.admin')
@section('title', 'Build Team Formation')

@section('header_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
    .player-card {
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 6px;
        cursor: move;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .player-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: hidden;
        background: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #495057;
        flex-shrink: 0;
    }
    .player-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .player-card__info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    .player-card__name {
        font-weight: 600;
        color: #212529;
    }
    .player-card__meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .drop-zone {
        min-height: 120px;
        background: #f1f1f1;
        border: 2px dashed #ccc;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 20px;
    }
    .drop-zone.empty::before {
        content: attr(data-placeholder);
        display: block;
        color: #9ca3af;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="row clearfix">
    @include('admin.teams.wizard._progress')

    <div class="col-lg-12">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-2xl" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-2xl" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $playersByPosition = $team->players->groupBy(fn ($player) => $player->pivot->position_id);
            $benchPlayers = $playersByPosition->get(null, collect());
        @endphp

        <div class="card">
            <div class="header bg-primary text-white">
                <h2>Step 4: Arrange Formation for <strong>{{ $team->name }}</strong></h2>
            </div>
            <div class="body">
                @if ($team->players->isEmpty())
                    <div class="alert alert-warning mb-4">
                        No players have been selected yet. Add players in Step 3 to build a formation.
                    </div>
                @endif

                @if ($positions->isEmpty())
                    <div class="alert alert-info mb-4">
                        No positions are defined for {{ $team->sport->name ?? 'this sport' }}. Players can be arranged in the Reserves area or dragged into custom slots later.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.teams.wizard.finalizeFormation', $team) }}" id="formationForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <h5>Reserves / Unassigned</h5>
                            <div id="playerPool" class="drop-zone {{ $benchPlayers->isEmpty() ? 'empty' : '' }}" data-position="" data-placeholder="Drag players here to keep them unassigned">
                                @foreach ($benchPlayers as $player)
                                    @include('components.team-wizard.player-card', ['player' => $player])
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h5>Assign to Positions</h5>
                            <div class="row">
                                @forelse ($positions as $position)
                                    @php
                                        $positionPlayers = $playersByPosition->get($position->id, collect());
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <strong>{{ $position->position_name }}</strong>
                                        <div class="drop-zone {{ $positionPlayers->isEmpty() ? 'empty' : '' }}" data-position="{{ $position->id }}" data-placeholder="Drag players into {{ $position->position_name }}">
                                            @foreach ($positionPlayers as $player)
                                                @include('components.team-wizard.player-card', ['player' => $player])
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="drop-zone empty" data-position="" data-placeholder="Drag players here to keep them as reserves"></div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="players" id="playersJson">

                        <div class="form-group mt-4 d-flex justify-content-between">
                            <a href="{{ route('admin.teams.wizard.step3', $team) }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Back to Player Selection
                            </a>
                        <button type="submit" class="btn btn-success" {{ $team->players->isEmpty() ? 'disabled' : '' }}>
                            <i class="ti ti-check"></i> Finalize Formation
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function syncEmptyState(zone) {
        if (!zone) return;
        zone.classList.toggle('empty', zone.querySelectorAll('.player-card').length === 0);
    }

    document.querySelectorAll('.drop-zone').forEach(zone => {
        new Sortable(zone, {
            group: 'players',
            animation: 150,
            onAdd: (evt) => {
                syncEmptyState(evt.to);
                if (evt.from) syncEmptyState(evt.from);
            },
            onRemove: (evt) => {
                if (evt.from) syncEmptyState(evt.from);
                if (evt.to) syncEmptyState(evt.to);
            },
            onSort: (evt) => {
                if (evt.to) syncEmptyState(evt.to);
            }
        });
        syncEmptyState(zone);
    });

    document.getElementById('formationForm').addEventListener('submit', function () {
        const formation = [];

        document.querySelectorAll('.drop-zone').forEach(zone => {
            const positionId = zone.getAttribute('data-position');
            zone.querySelectorAll('.player-card').forEach(card => {
                formation.push({
                    id: card.getAttribute('data-id'),
                    position_id: positionId ? positionId : null
                });
            });
        });

        document.getElementById('playersJson').value = JSON.stringify({ players: formation });
    });
</script>
@endsection
