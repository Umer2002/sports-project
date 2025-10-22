@extends('layouts.player')

@section('title', 'Player Dashboard')

@section('content')


    <div class="row">
        <div class="col-9">

            <div class="block-header">
                <div class="row">

                    <!-- Weather Info -->
                    <div class="col-6">
                        @if($player->sport && $player->sport->icon_path)
                            <img src="{{ asset('storage/' . $player->sport->icon_path) }}" width="62" height="164" class="img-fluid" alt="{{ $player->sport->name }}">
                        @else
                            <img src="{{ url('assets/images/player/football.png') }}" width="62" height="164" class="img-fluid" alt="Default Sport">
                        @endif
                        @if($weather)
                            <span class="fs-5" style="font-size: 32px !important;">{{ $weather['temp'] }}°C</span>
                            <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png" width="75" height="75" class="img-fluid">
                            <span class="fw-semibold" style="font-size: 13px !important;">H:{{ $weather['temp_max'] }}° L:{{ $weather['temp_min'] }}°</span>
                            <span class="text-muted">{{ $weather['city'] }}, {{ $weather['country'] }}</span>
                            <div class="d-flex flex-column ms-2">
                                <span class="text-muted">{{ $weather['condition'] }}</span>
                            </div>
                        @else
                            <span class="fs-5" style="font-size: 32px !important;">--°C</span>
                            <img src="{{ url('assets/images/player/rain.png') }}" width="75" height="75" class="img-fluid">
                            <span class="fw-semibold" style="font-size: 13px !important;">H:--° L:--°</span>
                            <span class="text-muted">Weather Unavailable</span>
                        @endif
                    </div>

                    <!-- Share Profile -->
                    <div class="col-6">
                        <span class="fw-semibold mb-2">Share Profile</span>
                        @php
                        $socials = is_array($player->social_links)
                                ? $player->social_links
                                : json_decode($player->social_links, true) ?? [];
                          @endphp
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Public Profile Link -->
                            <a href="{{ route('public.player.profile', $player->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Public Profile
                            </a>

                            @if (isset($socials['facebook']))
                                <a href="{{ $socials['facebook'] }}"><img src="{{ url('assets/images/social/fb.webp') }}" width="30" height="20"
                                    class="img-fluid"></a>
                            @endif
                            @if (isset($socials['twitter']))
                                <a href="{{ $socials['twitter'] }}"><img src="{{ url('assets/images/social/x.webp') }}" width="30" height="20"
                                    class="img-fluid"></a>
                            @endif
                            @if (isset($socials['instagram']))
                                <a href="{{ $socials['instagram'] }}"><img src="{{ url('assets/images/social/insta.webp') }}" width="30" height="20"
                                    class="img-fluid"></a>
                            @endif
                        </div>


                    </div>

                </div>

            </div>



            <style>
                /* ─── Player-card utility classes (generated) ─────────────────────────────── */
                ._na1 {
                    max-width: 1256px;
                    margin: 0 auto;
                    height: 500px;
                }

                ._na2 {
                    border-radius: 40px;
                    opacity: 0.16;
                }

                ._na3 {
                    top: 25px;
                    left: 10px;
                    font-family: Gilroy, sans-serif;
                    font-weight: 700;
                    font-size: 23px;
                    line-height: 52px;
                    letter-spacing: 0;
                    color: #ffffff;
                }

                ._na4 {
                    top: 170px;
                    left: 10px;
                    width: 110px;
                    height: 36px;
                    border-radius: 8px;
                    padding: 6px 8px;
                    background: #ffffff0d;
                    gap: 8px;
                }

                ._na5 {
                    width: 18px;
                    height: 18px;
                }

                ._na6 {
                    font-family: Gilroy, sans-serif;
                    font-weight: 400;
                    font-size: 8px;
                    line-height: 18px;
                    color: #ffffff;
                }

                ._na7 {
                    top: 170px;
                    left: 130px;
                    width: 110px;
                    height: 36px;
                    border-radius: 8px;
                    padding: 6px 8px;
                    background: #ffffff0d;
                    gap: 8px;
                }

                ._na8 {
                    top: 230px;
                    left: 10px;
                    width: 200px;
                    height: 40px;
                    border-radius: 8px;
                    padding: 8px 10px;
                    gap: 8px;
                }

                ._na9 {
                    top: 280px;
                    left: 8px;
                    width: 220px;
                    height: 155px;
                    border-radius: 40px;
                    background: #ffffff14;
                }

                ._na10 {
                    margin-top: 10px;
                    margin-left: 15px;
                    gap: 50px;
                }

                ._na11 {
                    width: 43px;
                    height: 24px;
                    font-family: Gilroy;
                    font-weight: 700;
                    font-size: 10px;
                    line-height: 24px;
                    color: #ffffff;
                }

                ._na12 {
                    width: 82px;
                    height: 24px;
                    font-family: Gilroy;
                    font-weight: 400;
                    font-size: 10px;
                    line-height: 24px;
                    color: #ffffff;
                }

                ._na13 {
                    width: 194px;
                    height: 1px;
                    background-color: #ffffff30;
                    margin: 12px 20px 0;
                }

                ._na14 {
                    top: 10px;
                    left: 190px;
                    font-family: Gilroy, sans-serif;
                    font-weight: 900;
                    font-size: 100px;
                    line-height: 150px;
                    mix-blend-mode: soft-light;
                    color: #ebe8e3;
                    pointer-events: none;
                }

                ._na15 {
                    top: 100px;
                    left: 160px;
                }

                ._na16 {
                    height: 280px;
                    object-fit: contain;
                    margin-top: 130px;
                }

                ._na17 {
                    top: 15px;
                    left: 320px;
                    width: 88px;
                    height: 33px;
                    border-radius: 40px;
                    background: linear-gradient(180deg, #27417c 0%, #081c49 100%);
                    font-family: Gilroy, sans-serif;
                    font-size: 14px;
                    color: #ffffff;
                }

                ._na18 {
                    top: 55px;
                    left: 310px;
                    width: 200px;
                    height: 95px;
                    border-radius: 30px;
                    background: linear-gradient(180deg, #27417c 0%, #081c49 100%);
                    padding: 12px 0;
                }

                ._na19 {
                    gap: 12px;
                }

                ._na20 {
                    width: 48px;
                    height: 20px;
                    font-family: Gilroy, sans-serif;
                    font-weight: 600;
                    font-size: 8px;
                    line-height: 20px;
                    border-radius: 10px;
                }

                ._na21 {
                    width: 54px;
                    height: 44px;
                    color: #d5f40b;
                    font-family: Gilroy, sans-serif;
                    font-weight: 500;
                    font-size: 20px;
                    line-height: 44px;
                    border-radius: 10px;
                }

                ._na22 {
                    top: 125px;
                    left: 10px;
                    width: 140px;
                    height: 40px;
                    border-radius: 8px;
                    padding: 8px 10px;
                    gap: 8px;
                }

                ._na23 {
                    top: 125px;
                    left: 160px;
                    width: 130px;
                    height: 40px;
                    border-radius: 8px;
                    padding: 8px 10px;
                    gap: 8px;
                }

                ._na24 {
                    top: 180px;
                    left: 50px;
                    width: 200px;
                    height: 40px;
                    border-radius: 8px;
                    padding: 8px 10px;
                    gap: 8px;
                }

                ._na25 {
                    top: 225px;
                    left: 50px;
                    width: 220px;
                    height: 155px;
                    border-radius: 40px;
                    background: #ffffff14;
                }

                ._na26 {
                    width: 24px;
                    height: 24px;
                }

                /* ─────────────────────────────────────────────────────────────────────────── */
            </style>

            <div class="container mt-4">
                <div class="row">
                    <div class="col-3 d-none d-md-block">
                        @if($player->club && $player->club->logo)
                            <img src="{{ asset('storage/' . $player->club->logo) }}" alt="{{ $player->club->name }}" class="ms-3 img-fluid" />
                        @else
                            <img src="{{ url('assets/images/player/covage.png') }}" alt="Default Club Logo" class="ms-3 img-fluid" />
                        @endif
                    </div>
                    <div class="col-lg-9 col-md-12">
                        <div class="counter-box text-center white">
                            <div class="position-relative text-center _na1">
                                <!-- Background image -->
                                <img src="{{ url('assets/images/player/bg.jpg') }}" class="img-fluid w-100 h-auto _na2"
                                    alt="Background" />

                                <!-- Player name -->
                                <div class="position-absolute _na3">
                                    {{ ucfirst($player->name) }}
                                </div>

                                <!-- Midfielder badge -->
                                @if (!empty($player->position))
                                <div class="position-absolute d-flex align-items-center _na4 text-center">
                                    <!-- <img src="{{ url('assets/images/player/Soccer.png') }}" alt="Midfielder"
                                        class="_na5" /> -->
                                    <div class="_na6  w-100 text-center"> {{ ucfirst($player->position->position_name) }}</div>
                                </div>
                                @endif


                                <!-- Right foot badge -->
                                <!-- <div class="position-absolute d-flex align-items-center _na7">
                                    <img src="{{ url('assets/images/player/Soccer.png') }}" alt="Right foot"
                                        class="_na5" />
                                    <div class="_na6">Right-Foot</div>
                                </div> -->

                                <!-- International career -->
                                <div class="position-absolute d-flex align-items-center _na8">
                                    <div class="_na6">INTL CAREER - {{ date('d M Y', strtotime($player->debut)) }}</div>
                                </div>

                                <!-- Personal info box -->
                                <div class="position-absolute _na9">
                                    <div class="d-flex align-items-center _na10">
                                        <div class="_na11">BORN</div>
                                        <div class="_na12">{{ $player->birthday }}</div>
                                    </div>
                                    <div class="_na13"></div>
                                    <div class="d-flex align-items-center _na10">
                                        <div class="_na11">AGE</div>
                                        <div class="_na12">{{ $player->age }}</div>
                                    </div>
                                    <div class="_na13"></div>
                                    <div class="d-flex align-items-center _na10">
                                        <div class="_na11">NATIONALITY</div>
                                        <div class="_na12">{{ $player->nationality }}</div>
                                    </div>
                                </div>

                                <!-- Large jersey number -->
                                <div class="position-absolute _na14">{{ $player->jersey_no }}</div>

                                <!-- Player image -->
                                <div class="position-absolute _na15">
                                @if (!empty($player->photo))
                                    <img src="{{ asset('uploads/players/' . $player->photo) }}" alt="Player" class="img-fluid _na16" />
                                    @endif
                                </div>

                                <!-- Stats pill -->
                                <div
                                    class="position-absolute d-inline-flex align-items-center justify-content-center _na17">
                                    Stats
                                </div>

                                <!-- Stats container -->
                                <div class="position-absolute _na18">
                                    <!-- Labels -->
                                    @if (isset($statsWithValues) && count($statsWithValues) >= 3)
                                        <!-- Labels -->
                                        <div class="d-flex justify-content-around align-items-center px-2 mb-2 _na19">
                                            @for ($i = 0; $i < 3; $i++)
                                                <div class="text-white text-center _na20">
                                                    {{ $statsWithValues[$i]->name ?? 'N/A' }}
                                                </div>
                                            @endfor
                                        </div>

                                        <!-- Numbers -->
                                        <div class="d-flex justify-content-around align-items-center px-2 _na19">
                                            @for ($i = 0; $i < 3; $i++)
                                                <div class="d-flex align-items-center justify-content-center _na21">
                                                    {{ $statsWithValues[$i]->value ?? '-' }}
                                                </div>
                                            @endfor
                                        </div>
                                    @else
                                        <div class="text-center text-warning">
                                            Not enough stats available to display (expected at least 3).
                                        </div>
                                    @endif


                                    <!-- Clubs -->
                                    @if (!empty($player->club))
                                    <div class="position-absolute d-flex align-items-center _na22">
                                        <img src="{{ asset('storage/' . $player->club->logo) }}" alt="{{ $player->club->name }}" class="_na26" />
                                        <div class="_na6">{{ $player->club->name }}</div>
                                    </div>
                                    @endif


                                    @if (!empty($player->jersey_no))
                                    <!-- Jersey note -->
                                    <div class="position-absolute d-flex align-items-center _na24">
                                        <div class="_na6">JERSEY NUMBER - {{$player->jersey_no}}</div>
                                    </div>
                                    @endif
                                    <!-- Physical attributes box -->
                                    <div class="position-absolute _na25">
                                        <div class="d-flex align-items-center _na10">
                                            <div class="_na11">HEIGHT</div>
                                            <div class="_na12"> {{ $player->height }}</div>
                                        </div>
                                        <div class="_na13"></div>
                                        <div class="d-flex align-items-center _na10">
                                            <div class="_na11">WEIGHT</div>
                                            <div class="_na12"> {{ $player->weight }}</div>
                                        </div>
                                        <div class="_na13"></div>
                                        <div class="d-flex align-items-center _na10">
                                            <div class="_na11">DEBUT</div>
                                            <div class="_na12"> {{ date('d M Y', strtotime($player->debut)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="container mt-4">
                <div class="row">
                    <!-- Award Item -->
                    @php
                        // Create a set of player reward IDs for quick lookup
                        $playerRewards = DB::table('rewards')
                            ->join('player_rewards', 'player_rewards.reward_id', '=', 'rewards.id')
                            ->select('rewards.id', 'rewards.name', 'rewards.image')
                            ->where('player_rewards.user_id', $player->user_id)
                            ->get();
                        $playerRewardIds = $playerRewards->pluck('id')->toArray();
                    @endphp

                    @foreach ($allRewards as $reward)
                        <div class="col-2 col-6 col-md-2 d-flex justify-content-center mb-4">
                            @php $isPlayerReward = in_array($reward->id, $playerRewardIds); @endphp
                            <div class="reward-img-wrapper">
                                <img src="{{ url('images/' . $reward->image) }}" alt="Reward"
                                    class="img-fluid {{ $isPlayerReward ? 'highlighted-reward-img' : 'normal-reward-img' }}"
                                    style="height: auto;">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>





            <!-- Quick Action Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-arrow-left-right fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">Transfer Requests</h5>
                                    <p class="card-text mb-2">Request to transfer to another club or sport</p>
                                    <a href="{{ route('player.transfers.index') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-plus me-1"></i>Manage Transfers
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-gamepad fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">Pickup Games</h5>
                                    <p class="card-text mb-2">Create or join casual games with friends</p>
                                    <a href="{{ route('player.pickup-games.index') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-plus me-1"></i>Browse Games
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('partials.dashboard_player_stat_cards')

            <div class="card bg-dark p-3 mb-4">
                <h5 class="text-white mb-3">My Pickup Games Calendar</h5>
                <div id="calendar"></div>
            </div>
        </div>
        <div class="col-3">
            @include('partials.dashboard_chat')
            @include('partials.dashboard_posts')
        </div>
    </div>


    @include('players.partials.pickup-modal')

    <div class="row mt-4">
        @forelse($ads as $ad)
            <div class="col-12 {{ $ad->type == 'video' ? 'col-md-6' : 'col-md-2' }} mb-2 d-flex justify-content-center">
                @if($ad->type == 'image' && $ad->media)
                    @php
                        $imagePath = 'storage/' . $ad->media;
                    @endphp
                    @if(file_exists(public_path($imagePath)))
                        <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener">
                            <img class="img-fluid rounded" src="{{ asset($imagePath) }}" alt="{{ $ad->title }}">
                        </a>
                    @else
                        <span class="text-muted">Image not found</span>
                    @endif
                @elseif($ad->type == 'video' && $ad->media)
                    @php
                        $videoPath = 'storage/' . $ad->media;
                        $ext = strtolower(pathinfo($ad->media, PATHINFO_EXTENSION));
                        $mimeSuffix = $ext === 'mov' ? 'quicktime' : $ext;
                    @endphp
                    @if(file_exists(public_path($videoPath)))
                        <video class="w-100 rounded" controls preload="metadata">
                            <source src="{{ asset($videoPath) }}" type="video/{{ $mimeSuffix }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <span class="text-muted">Video not found</span>
                    @endif
                @else
                    <span class="text-muted">No media</span>
                @endif
            </div>
        @empty
            <div class="col-12 text-center text-muted">No ads assigned to you.</div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        function setupCalendar() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach ($pickupGames as $game)
                        {
                            id: {{ $game->id }},
                            title: '{{ $game->sport->name }} Game',
                            start: '{{ $game->game_datetime }}',
                            extendedProps: {
                                joined: {{ $game->participants->contains($user->id) ? 'true' : 'false' }},
                                location: '{{ $game->location }}'
                            }
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    const joined = info.event.extendedProps.joined;
                    const gameId = info.event.id;
                    const location = info.event.extendedProps.location;

                    document.getElementById('pickupModalLabel').textContent =
                        `${info.event.title} @ ${location}`;
                    document.getElementById('pickupModalJoinBtn').dataset.id = gameId;
                    document.getElementById('pickupModalLeaveBtn').dataset.id = gameId;

                    document.getElementById('pickupModalJoinBtn').classList.toggle('d-none', joined);
                    document.getElementById('pickupModalLeaveBtn').classList.toggle('d-none', !joined);

                    new bootstrap.Modal(document.getElementById('pickupModal')).show();
                }
            });
            calendar.render();
        }

        function joinGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }

        function leaveGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }

        window.addEventListener('DOMContentLoaded', setupCalendar);
    </script>
@endpush

<style>
    /* ─── Player-card utility classes (generated) ─────────────────────────────── */
    ._na1 {
        max-width: 1256px;
        margin: 0 auto;
        height: 500px;
    }

    ._na2 {
        border-radius: 40px;
        opacity: 0.16;
    }

    ._na3 {
        top: 25px;
        left: 10px;
        font-family: Gilroy, sans-serif;
        font-weight: 700;
        font-size: 23px;
        line-height: 52px;
        letter-spacing: 0;
        color: #ffffff;
    }

    ._na4 {
        top: 170px;
        left: 10px;
        width: 110px;
        height: 36px;
        border-radius: 8px;
        padding: 6px 8px;
        background: #ffffff0d;
        gap: 8px;
    }

    ._na5 {
        width: 18px;
        height: 18px;
    }

    ._na6 {
        font-family: Gilroy, sans-serif;
        font-weight: 400;
        font-size: 8px;
        line-height: 18px;
        color: #ffffff;
    }

    ._na7 {
        top: 170px;
        left: 130px;
        width: 110px;
        height: 36px;
        border-radius: 8px;
        padding: 6px 8px;
        background: #ffffff0d;
        gap: 8px;
    }

    ._na8 {
        top: 230px;
        left: 10px;
        width: 200px;
        height: 40px;
        border-radius: 8px;
        padding: 8px 10px;
        gap: 8px;
    }

    ._na9 {
        top: 280px;
        left: 8px;
        width: 220px;
        height: 155px;
        border-radius: 40px;
        background: #ffffff14;
    }

    ._na10 {
        margin-top: 10px;
        margin-left: 15px;
        gap: 50px;
    }

    ._na11 {
        width: 43px;
        height: 24px;
        font-family: Gilroy;
        font-weight: 700;
        font-size: 10px;
        line-height: 24px;
        color: #ffffff;
    }

    ._na12 {
        width: 82px;
        height: 24px;
        font-family: Gilroy;
        font-weight: 400;
        font-size: 10px;
        line-height: 24px;
        color: #ffffff;
    }

    ._na13 {
        width: 194px;
        height: 1px;
        background-color: #ffffff30;
        margin: 12px 20px 0;
    }

    ._na14 {
        top: 10px;
        left: 190px;
        font-family: Gilroy, sans-serif;
        font-weight: 900;
        font-size: 100px;
        line-height: 150px;
        mix-blend-mode: soft-light;
        color: #ebe8e3;
        pointer-events: none;
    }

    ._na15 {
        top: 100px;
        left: 160px;
    }

    ._na16 {
        height: 280px;
        object-fit: contain;
        margin-top: 130px;
    }

    ._na17 {
        top: 15px;
        left: 320px;
        width: 88px;
        height: 33px;
        border-radius: 40px;
        background: linear-gradient(180deg, #27417c 0%, #081c49 100%);
        font-family: Gilroy, sans-serif;
        font-size: 14px;
        color: #ffffff;
    }

    ._na18 {
        top: 55px;
        left: 310px;
        width: 200px;
        height: 95px;
        border-radius: 30px;
        background: linear-gradient(180deg, #27417c 0%, #081c49 100%);
        padding: 12px 0;
    }

    ._na19 {
        gap: 12px;
    }

    ._na20 {
        width: 48px;
        height: 20px;
        font-family: Gilroy, sans-serif;
        font-weight: 600;
        font-size: 8px;
        line-height: 20px;
        border-radius: 10px;
    }

    ._na21 {
        width: 54px;
        height: 44px;
        color: #d5f40b;
        font-family: Gilroy, sans-serif;
        font-weight: 500;
        font-size: 20px;
        line-height: 44px;
        border-radius: 10px;
    }

    ._na22 {
        top: 125px;
        left: 10px;
        width: 140px;
        height: 40px;
        border-radius: 8px;
        padding: 8px 10px;
        gap: 8px;
    }

    ._na23 {
        top: 125px;
        left: 160px;
        width: 130px;
        height: 40px;
        border-radius: 8px;
        padding: 8px 10px;
        gap: 8px;
    }

    ._na24 {
        top: 180px;
        left: 50px;
        width: 200px;
        height: 40px;
        border-radius: 8px;
        padding: 8px 10px;
        gap: 8px;
    }

    ._na25 {
        top: 225px;
        left: 50px;
        width: 220px;
        height: 155px;
        border-radius: 40px;
        background: #ffffff14;
    }

    ._na26 {
        width: 24px;
        height: 24px;
    }

    /* ─────────────────────────────────────────────────────────────────────────── */

    /* ─── Responsive overrides ─────────────────────────────── */
    @media (max-width: 991.98px) {
        ._na1 { height: auto; min-height: 300px; }
        ._na3 { font-size: 16px; line-height: 32px; }
        ._na14 { font-size: 48px; line-height: 60px; }
        ._na16 { margin-top: 40px; height: 120px; }
        ._na18, ._na9, ._na25 {
            position: static !important;
            width: 100% !important;
            height: auto !important;
            left: 0 !important;
            top: auto !important;
            margin-bottom: 1rem;
        }
        ._na10 {
            flex-direction: column;
            gap: 10px;
            margin-left: 0;
        }
        ._na20, ._na21 {
            width: 33vw !important;
            min-width: 60px;
            max-width: 100px;
            font-size: 12px;
            height: auto;
            line-height: 1.2;
        }
    }
    @media (max-width: 767.98px) {
        ._na1 { height: auto; min-height: 200px; }
        ._na3 { font-size: 12px; line-height: 20px; }
        ._na14 { font-size: 32px; line-height: 40px; }
        ._na16 { margin-top: 20px; height: 80px; }
        ._na18, ._na9, ._na25 {
            position: static !important;
            width: 100% !important;
            height: auto !important;
            left: 0 !important;
            top: auto !important;
            margin-bottom: 1rem;
        }
        ._na10 {
            flex-direction: column;
            gap: 8px;
            margin-left: 0;
        }
        ._na20, ._na21 {
            width: 90vw !important;
            min-width: 60px;
            max-width: 100%;
            font-size: 14px;
            height: auto;
            line-height: 1.2;
        }
    }

    .reward-img-wrapper {
        padding: 4px;
        border-radius: 8px;
    }
    .normal-reward-img {
        max-width: 70px;
        transition: max-width 0.3s, box-shadow 0.3s;
    }
    .highlighted-reward-img {
        max-width: 110px;
        box-shadow: 0 0 15px #ffc107;
        z-index: 2;
        transition: max-width 0.3s, box-shadow 0.3s;
    }
</style>
