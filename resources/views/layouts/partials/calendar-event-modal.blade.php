<div class="modal fade calendar-event-modal" id="clubCalendarEventModal" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="tmTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-one">
            <div class="modal-header modal-header-one">
                <div class="modal-headding">
                    <h1 class="modal-title fs-5" id="tmTitle">Event Title</h1>
                    <p id="tmWhen">—</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="big-panel">
                            <strong class="d-block mb-1 text-muted-a small">Location &amp; Map</strong>
                            <div id="tmVenue" class="mb-1">—</div>
                            <div class="map-placeholder mt-2" id="tmMap" style="min-height:260px;"></div>

                            <div class="filters mt-3">
                                <button class="filter-btn-a active" type="button">Hotels</button>
                                <button class="filter-btn-a" type="button">Distance</button>
                                <button class="filter-btn-a" type="button">Price</button>
                                <button class="filter-btn-a" type="button">Rating</button>
                            </div>

                            <div id="tmHotels" class="d-flex gap-2 flex-wrap mt-2 text-muted drag-text">
                                No nearby hotels have been added yet.
                            </div>

                            <div class="mt-4">
                                <small class="d-block mb-2 text-muted-b fw-semibold">Clubs</small>
                                <div id="tmClubs" class="calendar-chip-group"></div>
                            </div>

                            <div class="mt-3">
                                <small class="d-block mb-2 text-muted-b fw-semibold">Coaches</small>
                                <div id="tmCoaches" class="calendar-chip-group"></div>
                            </div>

                            <div class="mt-3">
                                <small class="d-block mb-2 text-muted-b fw-semibold">Players</small>
                                <div id="tmPlayers" class="calendar-chip-group"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="panel-sm">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Attending?</small>
                            <div class="d-flex gap-2">
                                <button class="attend-btn btn-yes" type="button">Yes</button>
                                <button class="attend-btn btn-maybe" type="button">Maybe</button>
                                <button class="attend-btn btn-no" type="button">No</button>
                            </div>
                        </div>

                        <div class="panel-sm">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Carpool / Transportation</small>
                            <div class="d-flex gap-2 mb-2">
                                <button class="carpool-btn" type="button">I can drive</button>
                                <button class="carpool-btn" type="button">Need a ride</button>
                            </div>
                            <small class="d-block text-muted-c">Seats available</small>
                            <input type="number" id="tmSeats" value="3" class="seat-input mt-1">
                        </div>

                        <div class="panel-sm panel-hig">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Coach Note</small>
                            <div id="tmCoachNote" class="arrive-text">—</div>
                        </div>

                        <div class="panel-sm">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Event Images</small>
                            <div id="tmImages" class="tm-image-previews empty">
                                <div class="text-muted small">No images uploaded yet.</div>
                            </div>
                            <div class="mb-2 text-muted drag-text">Drag &amp; drop or click to upload</div>
                            <div class="upload-btn-one">
                                <button class="upload-btn" id="tmUploadBtn" type="button">Upload</button>
                                <input type="file" id="tmUploadInput" accept="image/*" class="d-none">
                            </div>
                        </div>

                        <div class="panel-sm panel-hig weather-main">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Weather</small>
                            <div id="tmWeather" class="drag-text">—</div>
                            <img id="tmWeatherIcon" src="" alt="icon" style="width:40px;height:40px;display:none;">
                        </div>

                        <div class="panel-sm">
                            <small class="d-block mb-2 text-muted-b fw-semibold">Venue &amp; Links</small>
                            <div id="tmVenueName" class="drag-text">—</div>
                            <div class="d-flex gap-2 mt-2">
                                <button id="tmSaveBtn" class="save-btn save-btn-a" type="button">Save</button>
                                <button id="tmChatBtn" class="chat-btn" type="button">Open Team Chat</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-custom mb-3">
                    <button id="tmAddCalBtn" class="footer-btn1" type="button">Add to Calendar</button>
                    <button id="tmShareBtn" class="footer-btn1" type="button">Share</button>
                    <a id="tmMapBtn" class="footer-btn1 footer-btn-primary" href="#" target="_blank"
                        rel="noopener">Open Map</a>
                </div>
            </div>
        </div>
    </div>
</div>
