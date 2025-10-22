/* resources/js/club-dashboard.js */

/* -------------------------------
   Small utilities
---------------------------------*/
const AppCfg = (function () {
  const cfg = window.App || {};
  const r = cfg.routes || {};
  const fill = (tpl, id) => (tpl || '').replace('__ID__', encodeURIComponent(String(id)));
  const fill2 = (tpl, type, id) =>
    (tpl || '')
      .replace('__TYPE__', encodeURIComponent(String(type)))
      .replace('__ID__', encodeURIComponent(String(id)));
  const toArray = (value) =>
    Array.isArray(value) ? value : value != null ? [value].flat() : [];
  return {
    csrf: cfg.csrf || '',
    userId: Number(cfg.userId || 0),
    userRoles: toArray(cfg.userRoles || []),
    routes: {
      chatSend: r.chatSend || '/player/chat/send',
      chatInitiate: (userId) => fill(r.chatInitiateTpl, userId),
      chatMessages: (chatId) => fill(r.chatMessagesTpl, chatId),
      tournamentJoin: (tournamentId) => fill(r.tournamentJoinTpl, tournamentId),
      teamPlayers: (teamId) => fill(r.teamPlayersTpl, teamId),
      calendarItem: (type, id) => fill2(r.calendarItemTpl, type, id),
      calendarPreference: (type, id) => fill2(r.calendarPreferenceTpl, type, id),
      calendarUpload: (type, id) => fill2(r.calendarUploadTpl, type, id),
      calendarIcs: (type, id) => fill2(r.calendarIcsTpl, type, id),
      teamChat: (teamId, scope = 'player') => {
        const tpl =
          scope === 'club' ? r.teamChatClubTpl : scope === 'player' ? r.teamChatPlayerTpl : r.teamChatPlayerTpl;
        return fill(tpl, teamId);
      },
    },
    events: Array.isArray(cfg.events) ? cfg.events : [],
  };
})();

function getModalInstance(el) {
  if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
  return window.bootstrap.Modal.getOrCreateInstance(el);
}

function safeHtml(text) {
  if (text == null) return '';
  const div = document.createElement('div');
  div.textContent = String(text);
  return div.innerHTML;
}

async function fetchJson(url, opts = {}) {
  const headers = new Headers(opts.headers || {});
  if (!headers.has('X-Requested-With')) headers.set('X-Requested-With', 'XMLHttpRequest');
  if (!headers.has('Accept')) headers.set('Accept', 'application/json');
  // Only add JSON content-type if body is JSON
  if (opts.body && typeof opts.body === 'string' && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json');
  }
  if (AppCfg.csrf && !headers.has('X-CSRF-TOKEN')) {
    headers.set('X-CSRF-TOKEN', AppCfg.csrf);
  }
  const res = await fetch(url, { credentials: 'same-origin', ...opts, headers });
  const ct = res.headers.get('content-type') || '';
  const data = ct.includes('application/json') ? await res.json().catch(() => null) : null;
  if (!res.ok) {
    const msg =
      (data && (data.message || data.error)) ||
      (res.status === 419 ? 'Session expired. Please refresh.' :
       res.status === 401 ? 'Please log in.' :
       `Request failed (${res.status}).`);
    throw new Error(msg);
  }
  return data;
}

function onReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  } else {
    fn();
  }
}

/* -------------------------------
   Dynamic modal loader (optional)
---------------------------------*/
async function loadModal(id, file) {
  try {
    let host = document.getElementById('modals');
    if (!host) {
      host = document.createElement('div');
      host.id = 'modals';
      document.body.appendChild(host);
    }
    const res = await fetch(file);
    const html = await res.text();
    host.insertAdjacentHTML('beforeend', html);
    console.log(`Modal ${id} loaded successfully`);
  } catch (err) {
    console.error(`Error loading modal ${id}:`, err);
  }
}

/* -----------------------------------
   Calendar modal orchestration
-------------------------------------*/
const CalendarModal = (function () {
  let modalEl = null;
  let modal = null;
  let current = null;
  let seatTimer = null;
  let bound = false;

  const refs = {};
  const roles = AppCfg.userRoles || [];

  function cacheRefs() {
    modalEl = document.getElementById('clubCalendarEventModal');
    if (!modalEl) return;

    refs.title = modalEl.querySelector('#tmTitle');
    refs.when = modalEl.querySelector('#tmWhen');
    refs.venue = modalEl.querySelector('#tmVenue');
    refs.venueName = modalEl.querySelector('#tmVenueName');
    refs.map = modalEl.querySelector('#tmMap');
    refs.mapBtn = modalEl.querySelector('#tmMapBtn');
    refs.hotels = modalEl.querySelector('#tmHotels');
    refs.clubs = modalEl.querySelector('#tmClubs');
    refs.coaches = modalEl.querySelector('#tmCoaches');
    refs.players = modalEl.querySelector('#tmPlayers');
    refs.chatBtn = modalEl.querySelector('#tmChatBtn');
    refs.saveBtn = modalEl.querySelector('#tmSaveBtn');
    refs.addCalBtn = modalEl.querySelector('#tmAddCalBtn');
    refs.shareBtn = modalEl.querySelector('#tmShareBtn');
    refs.attendButtons = Array.from(modalEl.querySelectorAll('.attend-btn'));
    refs.carpoolButtons = Array.from(modalEl.querySelectorAll('.carpool-btn'));
    refs.seatsInput = modalEl.querySelector('#tmSeats');
    refs.uploadBtn = modalEl.querySelector('#tmUploadBtn') || modalEl.querySelector('.upload-btn');
    refs.uploadInput = modalEl.querySelector('#tmUploadInput');
    refs.images = modalEl.querySelector('#tmImages');
    refs.coachNote = modalEl.querySelector('#tmCoachNote');
    refs.weather = modalEl.querySelector('#tmWeather');
    refs.weatherIcon = modalEl.querySelector('#tmWeatherIcon');
  }

  function init() {
    cacheRefs();
    if (!modalEl) return;
    modal = getModalInstance(modalEl);
    bindActions();
    reset();
    modalEl.addEventListener('hidden.bs.modal', () => {
      current = null;
      reset();
    });
  }

  function setText(el, value) {
    if (!el) return;
    el.textContent = value != null && value !== '' ? value : '—';
  }

  function reset() {
    setText(refs.title, 'Event');
    setText(refs.when, '—');
    setText(refs.venue, '—');
    setText(refs.venueName, '—');
    setCoachNote(null);
    setWeather(null);
    setMapPlaceholder('Map will appear here once details load.');
    setHotelsPlaceholder('No nearby hotels have been added yet.');
    renderSummary(refs.clubs, null);
    renderSummary(refs.coaches, null);
    renderSummary(refs.players, null);
    renderPreference({});
    configureButtons({ routes: {} });
  }

  function prepareSkeleton(fcEvent) {
    if (fcEvent) {
      setText(refs.title, fcEvent.title || 'Event');
      const start = fcEvent.start;
      setText(
        refs.when,
        start ? start.toLocaleString([], { dateStyle: 'medium', timeStyle: 'short' }) : '—'
      );
      const loc = fcEvent.extendedProps?.location || fcEvent.extendedProps?.venue || 'Loading venue...';
      setText(refs.venue, loc);
      setText(refs.venueName, loc);
    } else {
      reset();
    }
    setCoachNote(null);
    setWeather(null);
    setMapPlaceholder('Loading map...');
    setHotelsPlaceholder('Loading hotels...');
    renderSummary(refs.clubs, null);
    renderSummary(refs.coaches, null);
    renderSummary(refs.players, null);
    renderPreference({});
    configureButtons({ routes: {} });
  }

  function setLoading(state) {
    if (!modalEl) return;
    modalEl.classList.toggle('calendar-modal-loading', state);
    toggleControls(state);
  }

  function toggleControls(disabled) {
    const controls = [
      ...(refs.attendButtons || []),
      ...(refs.carpoolButtons || []),
      refs.seatsInput,
      refs.uploadBtn,
      refs.saveBtn,
      refs.addCalBtn,
      refs.shareBtn,
      refs.chatBtn,
    ];

    controls.forEach((ctrl) => {
      if (!ctrl) return;
      if (disabled) {
        ctrl.dataset.prevDisabled = ctrl.disabled ? '1' : '0';
        ctrl.disabled = true;
      } else {
        const prev = ctrl.dataset.prevDisabled;
        delete ctrl.dataset.prevDisabled;
        if (prev === '1') {
          ctrl.disabled = true;
        } else if (prev === '0') {
          ctrl.disabled = false;
        }
      }
    });
  }

  function setMapPlaceholder(message) {
    if (!refs.map) return;
    refs.map.innerHTML = `<div class="text-muted small">${safeHtml(message)}</div>`;
  }

  function setHotelsPlaceholder(message) {
    if (!refs.hotels) return;
    refs.hotels.innerHTML = `<div class="text-muted small">${safeHtml(message)}</div>`;
  }

  async function load(type, id, fcEvent) {
    setLoading(true);
    try {
      const data = await fetchJson(AppCfg.routes.calendarItem(type, id));
      current = { ...data, fcEvent, type: data.type || type, id: data.id || id };
      render(current);
      modal && modal.show();
    } catch (e) {
      notify(e.message || 'Unable to load event details right now.', 'error');
    } finally {
      setLoading(false);
    }
  }

  function render(data) {
    setText(refs.title, data.title || 'Event');
    setText(refs.when, data.when || '—');
    setVenue(data.venue);
    setMap(data.map);
    renderHotels(data.hotels);
    renderSummary(refs.clubs, data.clubs);
    renderSummary(refs.coaches, data.coaches);
    renderSummary(refs.players, data.players);
    setCoachNote(data.description);
    setWeather(null);
    const prefsEnabled = data.preferences_enabled !== false;
    renderPreference(data.preference || {});
    applyPreferenceAvailability(prefsEnabled);
    current.preferences_enabled = prefsEnabled;
    configureButtons(data);
  }

  function setVenue(venue = {}) {
    if (!venue) venue = {};
    setText(refs.venueName, venue.name || venue.line || '—');
    setText(refs.venue, venue.line || venue.name || '—');
  }

  function setMap(map = {}) {
    if (!refs.map) return;
    if (map.embed) {
      refs.map.innerHTML = `<iframe src="${map.embed}" width="100%" height="260" style="border:0;"
        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
    } else {
      setMapPlaceholder('Map unavailable for this event.');
    }
    if (refs.mapBtn) {
      const href = map.view || '';
      if (href) {
        refs.mapBtn.href = href;
        refs.mapBtn.classList.remove('disabled');
        refs.mapBtn.setAttribute('aria-disabled', 'false');
      } else {
        refs.mapBtn.href = '#';
        refs.mapBtn.classList.add('disabled');
        refs.mapBtn.setAttribute('aria-disabled', 'true');
      }
    }
  }

  function renderHotels(list) {
    if (!refs.hotels) return;
    refs.hotels.innerHTML = '';
    if (!Array.isArray(list) || list.length === 0) {
      setHotelsPlaceholder('No nearby hotels have been added yet.');
      return;
    }
    list.forEach((hotel) => {
      const card = document.createElement('div');
      card.className = 'tm-hotel-card';
      card.innerHTML = `
        <div class="tm-hotel-name">${safeHtml(hotel.name || 'Hotel')}</div>
        <div class="tm-hotel-address text-muted">${safeHtml(hotel.address || '')}</div>`;
      if (hotel.maps_url) {
        const link = document.createElement('a');
        link.href = hotel.maps_url;
        link.target = '_blank';
        link.rel = 'noopener';
        link.className = 'tm-hotel-link';
        link.textContent = 'View on map';
        card.appendChild(link);
      }
      refs.hotels.appendChild(card);
    });
  }

  function renderSummary(container, summary) {
    if (!container) return;
    container.innerHTML = '';
    if (!summary || !Array.isArray(summary.list) || summary.list.length === 0) {
      container.innerHTML = '<span class="text-muted small">—</span>';
      return;
    }
    summary.list.forEach((item) => {
      const chip = document.createElement('span');
      chip.className = 'calendar-chip';
      chip.textContent = item;
      container.appendChild(chip);
    });
    if (summary.overflow > 0) {
      const overflow = document.createElement('span');
      overflow.className = 'calendar-chip overflow';
      overflow.textContent = `+${summary.overflow}`;
      container.appendChild(overflow);
    }
  }

  function renderPreference(pref) {
    current = current || {};
    current.preference = pref || {};
    setAttendState(pref?.attending_status || null);
    setCarpoolState(pref?.carpool_status || null);
    setSeats(pref?.seats_available);
    renderAttachments(pref?.attachments || []);
  }

  function setAttendState(status) {
    (refs.attendButtons || []).forEach((btn) => {
      if (!btn.dataset.attendance) return;
      btn.classList.toggle('active', btn.dataset.attendance === status);
    });
  }

  function setCarpoolState(status) {
    (refs.carpoolButtons || []).forEach((btn) => {
      if (!btn.dataset.carpool) return;
      btn.classList.toggle('active', btn.dataset.carpool === status);
    });
  }

  function setSeats(value) {
    if (!refs.seatsInput) return;
    if (value == null || value === '') {
      refs.seatsInput.value = '';
    } else {
      refs.seatsInput.value = Number(value);
    }
  }

  function renderAttachments(list) {
    if (!refs.images) return;
    refs.images.innerHTML = '';
    if (!Array.isArray(list) || list.length === 0) {
      refs.images.classList.add('empty');
      refs.images.innerHTML = '<div class="text-muted small">No images uploaded yet.</div>';
      return;
    }
    refs.images.classList.remove('empty');
    list.forEach((item) => {
      if (!item?.url) return;
      const link = document.createElement('a');
      link.href = item.url;
      link.target = '_blank';
      link.rel = 'noopener';
      link.className = 'tm-image-thumb';
      link.title = item.name || 'Event image';
      link.innerHTML = `<img src="${safeHtml(item.url)}" alt="${safeHtml(item.name || 'Event image')}">`;
      refs.images.appendChild(link);
    });
  }

  function setCoachNote(note) {
    setText(refs.coachNote, note);
  }

  function setWeather(weather) {
    if (refs.weather) {
      setText(refs.weather, weather?.text || '—');
    }
    if (refs.weatherIcon) {
      if (weather?.icon) {
        refs.weatherIcon.src = weather.icon;
        refs.weatherIcon.style.display = '';
      } else {
        refs.weatherIcon.style.display = 'none';
      }
    }
  }

  function applyPreferenceAvailability(enabled) {
    const controls = [
      ...(refs.attendButtons || []),
      ...(refs.carpoolButtons || []),
    ];

    controls.forEach((btn) => {
      if (btn) btn.disabled = !enabled;
      if (btn && !enabled) btn.classList.remove('active');
    });

    if (refs.seatsInput) {
      refs.seatsInput.disabled = !enabled;
      if (!enabled) refs.seatsInput.value = '';
    }

    if (refs.uploadBtn) refs.uploadBtn.disabled = !enabled;
    if (refs.uploadInput) refs.uploadInput.disabled = !enabled;
    if (refs.saveBtn) refs.saveBtn.disabled = !enabled;
  }

  function configureButtons(data) {
    if (refs.addCalBtn) {
      const icsUrl = data.routes?.ics || '';
      refs.addCalBtn.dataset.icsUrl = icsUrl;
      refs.addCalBtn.disabled = !icsUrl;
    }

    if (refs.shareBtn) {
      refs.shareBtn.disabled = false;
    }

    if (refs.chatBtn) {
      refs.chatBtn.disabled = !data.team;
      if (data.team?.name) {
        refs.chatBtn.title = `Open chat for ${data.team.name}`;
      } else {
        refs.chatBtn.removeAttribute('title');
      }
    }
  }

  async function savePreference(patch = {}, opts = {}) {
    if (!current) return false;
    if (current.preferences_enabled === false) {
      if (!opts.quiet) {
        notify('Event preferences will be available once the latest migrations run.', 'info');
      }
      return false;
    }
    const payload = JSON.stringify(patch ?? {});
    try {
      const res = await fetchJson(AppCfg.routes.calendarPreference(current.type, current.id), {
        method: 'POST',
        body: payload,
      });
      if (res?.preference) {
        renderPreference(res.preference);
      }
      if (opts.successMessage) {
        notify(opts.successMessage, 'success');
      }
      return true;
    } catch (e) {
      if (!opts.quiet) {
        notify(e.message || 'Unable to save your preference.', 'error');
      }
      return false;
    }
  }

  async function handleAttend(status) {
    if (!current) return;
    const previous = current.preference?.attending_status || null;
    setAttendState(status);
    const ok = await savePreference(
      { attending_status: status },
      { successMessage: 'Attendance updated.' }
    );
    if (!ok) {
      setAttendState(previous);
    }
  }

  async function handleCarpool(status) {
    if (!current) return;
    const previous = current.preference?.carpool_status || null;
    setCarpoolState(status);
    const ok = await savePreference(
      { carpool_status: status },
      { successMessage: 'Carpool preference saved.' }
    );
    if (!ok) {
      setCarpoolState(previous);
    }
  }

  function handleSeatsInput() {
    if (!refs.seatsInput || !current) return;
    const raw = refs.seatsInput.value.trim();
    const value = raw === '' ? null : Number.parseInt(raw, 10);
    if (raw !== '' && (Number.isNaN(value) || value < 0)) {
      return;
    }
    clearTimeout(seatTimer);
    seatTimer = window.setTimeout(() => {
      savePreference(
        { seats_available: value },
        { successMessage: 'Seats updated.' }
      );
    }, 500);
  }

  async function uploadImages(files) {
    if (!current || !files.length) return;
    if (current.preferences_enabled === false) {
      notify('Image uploads will be available once the latest migrations run.', 'info');
      return;
    }
    for (const file of files) {
      await uploadSingle(file);
    }
    if (refs.uploadInput) {
      refs.uploadInput.value = '';
    }
  }

  async function uploadSingle(file) {
    const formData = new FormData();
    formData.append('image', file);
    try {
      const res = await fetchJson(AppCfg.routes.calendarUpload(current.type, current.id), {
        method: 'POST',
        body: formData,
      });
      if (res?.attachments) {
        current.preference = current.preference || {};
        current.preference.attachments = res.attachments;
        renderAttachments(res.attachments);
      }
      notify('Image uploaded successfully.', 'success');
    } catch (e) {
      notify(e.message || 'Image upload failed.', 'error');
    }
  }

  function addToCalendar() {
    if (!current || !refs.addCalBtn) return;
    const url = refs.addCalBtn.dataset.icsUrl;
    if (!url) {
      notify('Calendar file is unavailable.', 'error');
      return;
    }
    window.open(url, '_blank', 'noopener');
    savePreference({ calendar_added: true }, { quiet: true });
    notify('Calendar download started.', 'success');
  }

  async function shareEvent() {
    if (!current) return;
    const textParts = [
      current.title || 'Event',
      current.when || '',
      current.venue?.line || current.venue?.name || '',
    ].filter(Boolean);
    const payload = {
      title: current.title || 'Event',
      text: textParts.join('\n'),
      url: window.location.href,
    };
    try {
      if (navigator.share) {
        await navigator.share(payload);
      } else if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(payload.text + '\n' + payload.url);
        notify('Event details copied to clipboard.', 'success');
      } else {
        notify('Sharing is not supported in this browser.', 'error');
      }
    } catch (e) {
      notify('Unable to share this event right now.', 'error');
    }
  }

  function openTeamChat() {
    if (!current?.team) {
      notify('Team chat is unavailable for this event.', 'error');
      return;
    }
    const role = roles.includes('club') ? 'club' : 'player';
    const url = current.team.routes?.[role] || current.team.routes?.player;
    if (!url) {
      notify('Unable to open team chat.', 'error');
      return;
    }
    window.open(url, '_blank', 'noopener');
  }

  function bindActions() {
    if (bound || !modalEl) return;
    bound = true;

    (refs.attendButtons || []).forEach((btn) => {
      if (btn.classList.contains('btn-yes')) {
        btn.dataset.attendance = 'yes';
      } else if (btn.classList.contains('btn-maybe')) {
        btn.dataset.attendance = 'maybe';
      } else {
        btn.dataset.attendance = 'no';
      }
      btn.addEventListener('click', () => handleAttend(btn.dataset.attendance));
    });

    (refs.carpoolButtons || []).forEach((btn) => {
      const label = btn.textContent.toLowerCase();
      btn.dataset.carpool = label.includes('drive') ? 'driver' : 'rider';
      btn.addEventListener('click', () => handleCarpool(btn.dataset.carpool));
    });

    if (refs.seatsInput) {
      refs.seatsInput.addEventListener('input', handleSeatsInput);
    }

    if (refs.uploadBtn && refs.uploadInput) {
      refs.uploadInput.setAttribute('multiple', 'multiple');
      refs.uploadBtn.addEventListener('click', () => refs.uploadInput.click());
      refs.uploadInput.addEventListener('change', () => {
        const files = Array.from(refs.uploadInput.files || []);
        uploadImages(files);
      });
    }

    if (refs.addCalBtn) {
      refs.addCalBtn.addEventListener('click', addToCalendar);
    }

    if (refs.shareBtn) {
      refs.shareBtn.addEventListener('click', shareEvent);
    }

    if (refs.chatBtn) {
      refs.chatBtn.addEventListener('click', openTeamChat);
    }

    if (refs.saveBtn) {
      refs.saveBtn.addEventListener('click', () => {
        savePreference({}, { successMessage: 'Preferences saved.' });
      });
    }
  }

  function notify(message, type = 'info') {
    const alert = document.createElement('div');
    const cls =
      type === 'success' ? 'success' : type === 'error' ? 'danger' : 'secondary';
    alert.className = `alert alert-${cls} alert-dismissible fade show calendar-alert`;
    alert.style.cssText =
      'position:fixed;top:20px;right:20px;z-index:2000;min-width:220px;';
    alert.innerHTML = `${safeHtml(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(alert);
    setTimeout(() => {
      alert.classList.remove('show');
      setTimeout(() => alert.remove(), 300);
    }, 3000);
  }

  return {
    init,
    openFromCalendar(event) {
      if (!modalEl) {
        init();
      }
      if (!modalEl) {
        notify('Event modal is not available.', 'error');
        return;
      }
      const props = event.extendedProps || {};
      if (!props.resource_type || !props.resource_id) {
        notify('Unable to open this calendar item.', 'error');
        return;
      }
      prepareSkeleton(event);
      load(props.resource_type, props.resource_id, event);
    },
  };
})();

/* -----------------------------------
   Calendar (FullCalendar) + Event modal
-------------------------------------*/
function initCalendar() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl || typeof FullCalendar === 'undefined') return;

  function eventContent(arg) {
    const p = arg.event.extendedProps || {};
    const wrapper = document.createElement('div');
    wrapper.classList.add('club-calendar-event');

    const counts = [];
    if (p.player_count) counts.push(`${safeHtml(p.player_count)} players`);
    if (p.coaches_count) counts.push(`${safeHtml(p.coaches_count)} coaches`);
    if (p.club_count) counts.push(`${safeHtml(p.club_count)} clubs`);

    const sub = counts.length ? counts.join(' • ') : p.location ? safeHtml(p.location) : '';
    wrapper.innerHTML = `
      <div class="fc-event-main-line">${safeHtml(arg.event.title)}</div>
      <div class="fc-event-sub-line">${sub}</div>`;

      return { domNodes: [wrapper] };
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: AppCfg.events,
    eventContent,
    eventClick(info) {
      info.jsEvent.preventDefault();
      CalendarModal.openFromCalendar(info.event);
    },
  });

  calendar.render();
}

/* -----------------------------------
   Tournament search filters
-------------------------------------*/
function initTournamentSearch() {
  const container = document.querySelector('[data-tournament-search]');
  if (!container) return;

  const rows = Array.from(container.querySelectorAll('[data-tournament-row]'));
  const filters = Array.from(container.querySelectorAll('[data-filter]'));
  const applyBtn = container.querySelector('[data-filter-apply]');
  const resetBtn = container.querySelector('[data-filter-reset]');
  const resultCount = container.querySelector('[data-result-count]');
  const stateSelect = container.querySelector('[data-filter="state"]');
  const citySelect = container.querySelector('[data-filter="city"]');

  const formatLabel = (text) => (text || '').trim();

  const cityMap = new Map();
  rows.forEach((row) => {
    const state = row.dataset.state || '';
    const cityKey = row.dataset.city || '';
    const cityLabel = formatLabel(row.dataset.cityLabel || '');
    if (!cityKey || !cityLabel) return;
    if (!cityMap.has(state)) {
      cityMap.set(state, new Map());
    }
    cityMap.get(state).set(cityKey, cityLabel);
    if (!cityMap.has('')) {
      cityMap.set('', new Map());
    }
    cityMap.get('').set(cityKey, cityLabel);
  });

  const renderCityOptions = (stateKey) => {
    if (!citySelect) return;
    const defaultOption = citySelect.querySelector('option[value=""]');
    citySelect.innerHTML = '';
    if (defaultOption) {
      citySelect.appendChild(defaultOption.cloneNode(true));
    } else {
      const opt = document.createElement('option');
      opt.value = '';
      opt.textContent = 'All Cities';
      citySelect.appendChild(opt);
    }

    const cities = cityMap.get(stateKey) || cityMap.get('') || new Map();
    Array.from(cities.entries())
      .sort((a, b) => a[1].localeCompare(b[1]))
      .forEach(([key, label]) => {
        const opt = document.createElement('option');
        opt.value = key;
        opt.textContent = label;
        citySelect.appendChild(opt);
      });
  };

  renderCityOptions('');

  const state = {};

  const applyFilters = () => {
    let visible = 0;
    rows.forEach((row) => {
      let show = true;
      if (state.state && row.dataset.state !== state.state) show = false;
      if (show && state.city && row.dataset.city !== state.city) show = false;
      if (show && state.sport && row.dataset.sport !== state.sport) show = false;
      if (show && state.division && row.dataset.division !== state.division) show = false;
      if (show && state.status && row.dataset.status !== state.status) show = false;
      if (show && state.month && row.dataset.month !== state.month) show = false;

      row.classList.toggle('d-none', !show);
      if (show) visible += 1;
    });

    if (resultCount) {
      resultCount.textContent = `Results • ${visible} Tournament${visible === 1 ? '' : 's'}`;
    }
  };

  filters.forEach((control) => {
    const filterKey = control.dataset.filter;
    control.addEventListener('change', () => {
      state[filterKey] = control.value || '';
      if (filterKey === 'state') {
        renderCityOptions(state[filterKey] || '');
        state.city = '';
      }
      applyFilters();
    });
  });

  applyBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    applyFilters();
  });

  resetBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    filters.forEach((control) => {
      control.value = '';
    });
    stateSelect && renderCityOptions('');
    Object.keys(state).forEach((key) => {
      state[key] = '';
    });
    applyFilters();
  });

  applyFilters();
}

/* -------------------------------
   Chat modal + roster + polling
---------------------------------*/
const ClubChat = (function () {
  let activeChatId = null;
  let pollTimer = null;

  function els() {
    const chatModalEl = document.getElementById('clubChatModal');
    return {
      chatModalEl,
      modal: getModalInstance(chatModalEl),
      messages: chatModalEl?.querySelector('#clubChatMessages') || null,
      name: chatModalEl?.querySelector('#clubChatUserName') || null,
      status: chatModalEl?.querySelector('#clubChatUserStatus') || null,
      initials: chatModalEl?.querySelector('#clubChatUserInitials') || null,
      form: chatModalEl?.querySelector('#clubChatForm') || null,
      input: chatModalEl?.querySelector('#clubChatInput') || null,
      sendBtn: chatModalEl?.querySelector('#clubSendBtn') || null,
      attachBtn: chatModalEl?.querySelector('#clubChatAttach') || null,
      cameraBtn: chatModalEl?.querySelector('#clubChatCamera') || null,
      emojiBtn: chatModalEl?.querySelector('#clubChatEmoji') || null,
      voiceBtn: chatModalEl?.querySelector('#clubChatVoice') || null,
      fileInput: chatModalEl?.querySelector('#clubChatFileInput') || null,
      attRow: chatModalEl?.querySelector('#clubChatAttachmentRow') || null,
      attPreview: chatModalEl?.querySelector('#clubChatAttachmentPreview') || null,
      roster: document.getElementById('clubContactList'),
    };
  }

  const ui = els();

  function setSendEnabled(isRecording = false) {
    if (!ui.sendBtn || !ui.input) return;
    const hasText = Boolean((ui.input.value || '').trim());
    const hasAttachments = ui.attPreview && ui.attPreview.childElementCount > 0;
    ui.sendBtn.disabled = (!hasText && !hasAttachments && !isRecording) || !activeChatId;
  }

  function resetAttachments() {
    if (ui.attPreview) ui.attPreview.innerHTML = '';
    if (ui.attRow) ui.attRow.classList.add('d-none');
    setSendEnabled();
  }

  function appendPreview(fileUrl, fileName) {
    if (!ui.attPreview || !ui.attRow) return;
    const wrap = document.createElement('div');
    wrap.className = 'chat-thumb';
    wrap.innerHTML = `
      <img src="${safeHtml(fileUrl)}" alt="${safeHtml(fileName)}">
      <button type="button" aria-label="Remove attachment">&times;</button>`;
    wrap.querySelector('button').addEventListener('click', () => {
      wrap.remove();
      if (ui.attPreview.childElementCount === 0) ui.attRow.classList.add('d-none');
      setSendEnabled();
    });
    ui.attPreview.appendChild(wrap);
    ui.attRow.classList.remove('d-none');
    setSendEnabled();
  }

  function fmtTime(value) {
    try {
      return new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch {
      return '';
    }
  }

  function renderMessages(list) {
    if (!ui.messages) return;
    if (!Array.isArray(list) || list.length === 0) {
      ui.messages.innerHTML = '<div class="text-muted text-center py-4">No messages yet. Start the conversation!</div>';
      return;
    }
    ui.messages.innerHTML = '';
    list.forEach((msg) => {
      const mine = Number(msg.sender_id) === AppCfg.userId;
      const bubbleClass = mine ? 'agent' : 'contact';
      const row = document.createElement('div');
      row.className = `d-flex flex-column ${mine ? 'align-items-end' : 'align-items-start'} mb-2`;
      row.innerHTML = `
        <div class="club-chat-bubble ${bubbleClass}">
          <div>${safeHtml(String(msg.content || '')).replace(/\n/g, '<br>')}</div>
        </div>
        <span class="club-chat-meta">${fmtTime(msg.created_at)}</span>`;
      ui.messages.appendChild(row);
    });
    ui.messages.scrollTop = ui.messages.scrollHeight;
  }

  function resetMessages(label = 'Loading conversation...') {
    if (ui.messages) ui.messages.innerHTML = `<div class="text-muted text-center py-4">${label}</div>`;
  }

  function stopPolling() {
    if (pollTimer) {
      clearInterval(pollTimer);
      pollTimer = null;
    }
  }

  async function loadMessages(chatId, silent = false) {
    if (!chatId) return;
    if (!silent) resetMessages();
    try {
      const data = await fetchJson(AppCfg.routes.chatMessages(chatId));
      renderMessages(data);
    } catch (e) {
      console.warn(e);
      if (!silent) resetMessages('Unable to load messages right now.');
    }
  }

  function open({ name, status = '', initials = '', chatId }) {
    if (!chatId) return;
    activeChatId = chatId;

    if (ui.name) ui.name.textContent = name || 'Chat';
    if (ui.status) ui.status.textContent = status || '';
    if (ui.initials) ui.initials.textContent = initials || (name ? name.slice(0, 2).toUpperCase() : 'CC');

    if (ui.fileInput) ui.fileInput.value = '';
    resetAttachments();
    resetMessages();

    if (ui.modal) {
      ui.modal.show();
      loadMessages(chatId);
      stopPolling();
      pollTimer = setInterval(() => loadMessages(chatId, true), 7000);
    } else {
      window.location.href = `/player/chat?chat_id=${encodeURIComponent(chatId)}`;
    }
    setSendEnabled();
  }

  async function initiate(userId, meta = {}) {
    if (!userId) return;
    try {
      const payload = await fetchJson(AppCfg.routes.chatInitiate(userId));
      if (!payload?.chat_id) throw new Error('Unable to start chat.');
      open({ chatId: payload.chat_id, name: meta.name, status: meta.status, initials: meta.initials });
    } catch (e) {
      console.warn(e);
      alert(e.message || 'Unable to open chat right now.');
    }
  }

  function bind() {
    // form submit
    ui.form?.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      if (!activeChatId || !ui.input) return;

      const message = (ui.input.value || '').trim();
      const hasAttachment = ui.attPreview && ui.attPreview.childElementCount > 0;
      if (!message && !hasAttachment) return;

      const btn = ui.sendBtn;
      const spinner = btn ? btn.querySelector('.spinner-border') : null;
      const toggleSpinner = (on) => spinner && spinner.classList.toggle('d-none', !on);

      if (btn) btn.disabled = true;
      toggleSpinner(true);

      try {
        await fetchJson(AppCfg.routes.chatSend, {
          method: 'POST',
          body: JSON.stringify({ chat_id: activeChatId, message }),
        });
        ui.input.value = '';
        if (ui.fileInput) ui.fileInput.value = '';
        resetAttachments();
        setSendEnabled();
        loadMessages(activeChatId, false);
      } catch (e) {
        console.warn(e);
        alert(e.message || 'Failed to send message.');
      } finally {
        toggleSpinner(false);
        if (btn) btn.disabled = false;
      }
    });

    ui.input?.addEventListener('input', () => setSendEnabled());

    if (ui.fileInput) {
      ui.fileInput.addEventListener('change', () => {
        if (!ui.fileInput.files || ui.fileInput.files.length === 0) {
          resetAttachments();
          return;
        }
        ui.attPreview && (ui.attPreview.innerHTML = '');
        Array.from(ui.fileInput.files).forEach((file) => {
          const reader = new FileReader();
          reader.onload = (e) => appendPreview(e.target.result, file.name);
          reader.readAsDataURL(file);
        });
      });
    }

    ui.attachBtn?.addEventListener('click', () => ui.fileInput?.click());
    ui.cameraBtn?.addEventListener('click', () => ui.fileInput?.click());
    ui.emojiBtn?.addEventListener('click', () => alert('Emoji picker coming soon!'));
    ui.voiceBtn?.addEventListener('click', () => alert('Voice notes coming soon!'));

    // close / cleanup
    if (ui.chatModalEl && ui.modal) {
      ui.chatModalEl.addEventListener('hidden.bs.modal', () => {
        stopPolling();
        activeChatId = null;
        resetMessages('Select a contact to start messaging.');
        if (ui.input) ui.input.value = '';
        if (ui.fileInput) ui.fileInput.value = '';
        resetAttachments();
        setSendEnabled();
      });
    }

    // Roster open
    ui.roster?.addEventListener('click', async (ev) => {
      const item = ev.target.closest('.chat-item');
      if (!item || item.classList.contains('disabled')) return;

      ev.preventDefault();
      const userId = item.dataset.userId;
      const name =
        item.dataset.contactLabel ||
        item.querySelector('.name')?.textContent?.trim() ||
        'Chat';
      const status = item.dataset.contactTagline || item.dataset.contactStatus || '';
      const initials =
        item.dataset.contactInitials ||
        (name ? name.split(' ').map((p) => p[0]).join('').slice(0, 2).toUpperCase() : 'CC');
      let chatId = item.dataset.chatId;

      try {
        if (!chatId) {
          if (!userId) return;
          const payload = await fetchJson(AppCfg.routes.chatInitiate(userId));
          if (!payload?.chat_id) throw new Error('Unable to start chat.');
          chatId = payload.chat_id;
        }
        open({ chatId, name, status, initials });
      } catch (e) {
        console.warn(e);
        alert(e.message || 'Unable to open chat right now.');
      }
    });
  }

  function expose() {
    window.clubChat = {
      open,
      initiate,
      loadMessages,
    };
  }

  return {
    init() {
      bind();
      expose();
    },
  };
})();

/* ---------------------------------
   Invite share helpers (clipboard)
-----------------------------------*/
const Share = (function () {
  function toast(message, type = 'info') {
    const t = document.createElement('div');
    t.className = `alert alert-${
      type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'
    } alert-dismissible fade show position-fixed`;
    t.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    t.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(t);
    setTimeout(() => t.parentNode && t.parentNode.removeChild(t), 3000);
  }

  async function copyInviteLink() {
    const input = document.getElementById('inviteLink');
    if (!input) return;
    const val = input.value || '';
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(val);
      } else {
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
      }
      toast('Invite link copied to clipboard!', 'success');
    } catch {
      toast('Failed to copy link. Please copy manually.', 'error');
    }
  }

  function shareViaWhatsApp() {
    const v = document.getElementById('inviteLink')?.value || '';
    const msg = `Join my club! Click this link: ${v}`;
    window.open(`https://wa.me/?text=${encodeURIComponent(msg)}`, '_blank');
  }

  function shareViaEmail() {
    const v = document.getElementById('inviteLink')?.value || '';
    const subject = 'Join My Club!';
    const body = `Hi! I'd like to invite you to join my club. Click this link to join: ${v}`;
    window.open(`mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`);
  }

  function shareViaSMS() {
    const v = document.getElementById('inviteLink')?.value || '';
    const msg = `Join my club! Click this link: ${v}`;
    window.open(`sms:?body=${encodeURIComponent(msg)}`);
  }

  async function shareViaSocial() {
    const v = document.getElementById('inviteLink')?.value || '';
    const text = `Join my club! Click this link: ${v}`;
    if (navigator.share) {
      try {
        await navigator.share({ title: 'Join My Club', text, url: v });
      } catch (e) {
        console.warn(e);
      }
    } else {
      toast('Copy the link and share it on your social media!', 'info');
    }
  }

  return { copyInviteLink, shareViaWhatsApp, shareViaEmail, shareViaSMS, shareViaSocial, toast };
})();

/* ---------------------------------
   Tournament chat (join & open)
-----------------------------------*/
function initTournamentChat() {
  const list = document.querySelector('.tournament-chat-list');
  if (!list) return;

  async function joinAndOpen(tournamentId, button, rowEl) {
    if (!tournamentId) {
      alert('Unable to open chat. Please refresh and try again.');
      return;
    }
    if (button) {
      button.disabled = true;
      button.classList.add('disabled');
    }
    try {
      const data = await fetchJson(AppCfg.routes.tournamentJoin(tournamentId), {
        method: 'POST',
        body: JSON.stringify({}),
      });
      if (!data?.chat_id) throw new Error('Chat not available for this tournament yet.');

      const name = (rowEl?.dataset.tournamentName || 'Tournament Chat').trim();
      const location = rowEl?.dataset.tournamentLocation || '';
      const status = rowEl?.dataset.tournamentStatus || '';
      const initials = rowEl?.dataset.tournamentInitials || 'TC';

      if (window.clubChat?.open) {
        window.clubChat.open({
          chatId: data.chat_id,
          name,
          status: [status, location].filter(Boolean).join(' • '),
          initials,
        });
      } else {
        const redirect = data.redirect_url || `/player/chat?chat_id=${encodeURIComponent(data.chat_id)}`;
        window.location.href = redirect;
      }
    } catch (e) {
      console.warn('Tournament chat join failed:', e);
      alert(e.message || 'Unable to join tournament chat right now.');
    } finally {
      if (button && !document.hidden) {
        button.disabled = false;
        button.classList.remove('disabled');
      }
    }
  }

  list.addEventListener('click', (ev) => {
    const btn = ev.target.closest('.open-tournament-chat');
    const item = ev.target.closest('.tournament-chat-item');
    if (!btn && !item) return;

    const row = btn ? btn.closest('.tournament-chat-item') : item;
    const isClosed =
      row?.classList.contains('disabled') || (row?.dataset.tournamentStatus || '').toLowerCase() === 'closed';
    if (isClosed) return;

    if (btn) ev.stopPropagation();

    const tid = (btn || row)?.getAttribute('data-tournament-id');
    joinAndOpen(tid, btn, row);
  });
}

/* ---------------------------------
   Tournament directory (iframe modal)
-----------------------------------*/
function initTournamentDirectory() {
  const modalEl = document.getElementById('tournamentDirectoryModal');
  if (!modalEl) return;

  const links = document.querySelectorAll('[data-player-tournament-link]');
  if (!links.length) return;

  const iframe = modalEl.querySelector('iframe');
  const defaultUrl = iframe?.dataset.defaultUrl || links[0].dataset.url || links[0].getAttribute('href');
  let pendingUrl = defaultUrl;
  const normalize = (u) => u || defaultUrl;

  const openModal = (url) => {
    const target = normalize(url);
    if (!target) return;
    const modal = getModalInstance(modalEl);
    if (!modal) {
      window.open(target, '_blank');
      return;
    }
    pendingUrl = target;
    modal.show();
  };

  links.forEach((a) => {
    a.addEventListener('click', (e) => {
      const target = a.dataset.url || a.getAttribute('href');
      if (!target) return;
      if (window.bootstrap && modalEl) {
        e.preventDefault();
        openModal(target);
      }
    });
  });

  modalEl.addEventListener('show.bs.modal', () => {
    const target = normalize(pendingUrl);
    if (iframe && target && iframe.getAttribute('src') !== target) {
      iframe.setAttribute('src', target);
    }
  });
}

/* ---------------------------------
   Create Match Modal (form helpers)
-----------------------------------*/
function initCreateMatchModal() {
  const createMatchModal = document.getElementById('createMatchModal');
  if (!createMatchModal) return;

  const $ = (id) => document.getElementById(id);

  function initializeForm() {
    const today = new Date();
    const date = today.toISOString().split('T')[0];
    const dateInput = $('eventDate');
    if (dateInput) dateInput.value = date;

    const now = new Date();
    now.setHours(now.getHours() + 1);
    const hhmm = now.toTimeString().slice(0, 5);
    const kickoff = $('kickoffTime');
    if (kickoff) kickoff.value = hhmm;

    const arrival = new Date(now);
    arrival.setMinutes(arrival.getMinutes() - 30);
    const arrHHMM = arrival.toTimeString().slice(0, 5);
    const arrivalInput = $('arrivalTime');
    if (arrivalInput) arrivalInput.value = arrHHMM;

    const end = new Date(now);
    end.setHours(end.getHours() + 2);
    const endHHMM = end.toTimeString().slice(0, 5);
    const endInput = $('endTime');
    if (endInput) endInput.value = endHHMM;

    const at = $('atTime');
    if (at) at.value = hhmm;
  }

  function addEventListeners() {
    const toggleGroup = (wrapId) => {
      const wrap = $(wrapId);
      if (!wrap) return;
      wrap.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') {
          wrap.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
          e.target.classList.add('active');
        }
      });
    };
    toggleGroup('homeAwayToggle');
    toggleGroup('rsvpToggle');

    const clickToggle = (containerId) => {
      const el = $(containerId);
      if (!el) return;
      el.addEventListener('click', (e) => {
        const pill = e.target.closest('.cm-pill');
        if (pill) pill.classList.toggle('active');
      });
    };
    clickToggle('dayPills');
    clickToggle('notificationPills');

    const swatches = document.querySelectorAll('.cm-color-swatch');
    swatches.forEach((sw) =>
      sw.addEventListener('click', function () {
        swatches.forEach((s) => s.classList.remove('selected'));
        this.classList.add('selected');
      })
    );

    const repeatToggle = $('repeatToggle');
    const repeatSection = $('repeatSection');
    if (repeatToggle && repeatSection) {
      repeatToggle.addEventListener('change', function () {
        repeatSection.style.display = this.checked ? 'block' : 'none';
      });
    }

    const teamSelect = $('modalTeamSelect');
    if (teamSelect) {
      teamSelect.addEventListener('change', function () {
        const teamId = this.value;
        if (teamId) loadTeamPlayers(teamId);
        else clearPlayerGrid();
      });

      if (teamSelect.value) {
        setTimeout(() => loadTeamPlayers(teamSelect.value), 350);
      }
    }

    const saveDraft = $('createEventBtn');
    saveDraft && saveDraft.addEventListener('click', () => saveEvent(false));

    const publishHeader = $('publishEventHeaderBtn');
    publishHeader && publishHeader.addEventListener('click', () => saveEvent(true));

    const saveHeader = $('saveDraftHeaderBtn');
    saveHeader && saveHeader.addEventListener('click', () => saveEvent(false));

    const publish = $('publishEventBtn');
    publish && publish.addEventListener('click', () => saveEvent(true));

    const preview = $('previewEventBtn');
    preview && preview.addEventListener('click', () => previewEvent());
  }

  function gatherForm(publish = false) {
    const q = (sel) => document.querySelector(sel);
    const val = (id) => $(id)?.value || '';

    return {
      title: val('eventTitle'),
      match_type: val('matchType'),
      opponent: val('opponent'),
      home_away: q('#homeAwayToggle .active')?.getAttribute('data-value') || 'Home',
      date: val('eventDate'),
      kickoff: val('kickoffTime'),
      arrival: val('arrivalTime'),
      end: val('endTime'),
      frequency: val('frequency'),
      every: val('every'),
      at: val('atTime'),
      ends: val('ends'),
      team: val('modalTeamSelect'),
      rsvp: q('#rsvpToggle .active')?.getAttribute('data-value') || 'RSVP',
      venue: val('venue'),
      address: val('address'),
      notifications: Array.from(document.querySelectorAll('#notificationPills .cm-pill.active')).map((p) =>
        p.getAttribute('data-value')
      ),
      days: Array.from(document.querySelectorAll('#dayPills .cm-pill.active')).map((p) => p.getAttribute('data-day')),
      selected_color: document.querySelector('.cm-color-swatch.selected')?.getAttribute('data-color') || '#00bcd4',
      repeat_enabled: !!$('repeatToggle')?.checked,
      selected_players: Array.from(document.querySelectorAll('.cm-player-chip.selected')).map((c) =>
        c.getAttribute('data-player-id')
      ),
      status: publish ? 'published' : 'draft',
    };
  }

  function formatEventDescription(data) {
    let d = `**${data.title}**\n\n`;
    d += `**Match Details:**\n`;
    d += `• Type: ${data.match_type || 'Not specified'}\n`;
    d += `• Opponent: ${data.opponent || 'Not specified'}\n`;
    d += `• Location: ${data.home_away}\n`;
    d += `• Team: ${data.team || 'Not specified'}\n\n`;

    d += `**Schedule:**\n`;
    d += `• Date: ${data.date}\n`;
    d += `• Kickoff: ${data.kickoff}\n`;
    d += `• Arrival: ${data.arrival}\n`;
    d += `• End: ${data.end}\n\n`;

    if (data.venue || data.address) {
      d += `**Venue:**\n`;
      if (data.venue) d += `• Venue: ${data.venue}\n`;
      if (data.address) d += `• Address: ${data.address}\n`;
      d += `\n`;
    }

    if (data.repeat_enabled && data.frequency && data.frequency !== 'One-time') {
      d += `**Recurrence:**\n`;
      d += `• Frequency: ${data.frequency}\n`;
      d += `• Every: ${data.every || '1'}\n`;
      d += `• At: ${data.at}\n`;
      d += `• Ends: ${data.ends || '8'} occurrences\n`;
      if (data.days.length > 0) d += `• Days: ${data.days.join(', ')}\n`;
      d += `\n`;
    }

    d += `**Settings:**\n`;
    d += `• RSVP: ${data.rsvp}\n`;
    if (data.notifications.length > 0) d += `• Notifications: ${data.notifications.join(', ')}\n`;
    if (data.selected_color) d += `• Event Color: ${data.selected_color}\n`;
    if (data.selected_players?.length) d += `• Selected Players: ${data.selected_players.length} players\n`;
    return d;
  }

  async function saveEvent(publish = false) {
    const createBtn = document.getElementById('createEventBtn');
    const originalText = createBtn ? createBtn.innerHTML : null;

    const formData = gatherForm(publish);
    if (!formData.title || !formData.date || !formData.kickoff) {
      alert('Please fill in all required fields (Title, Date, Kickoff)');
      return;
    }
    const description = formatEventDescription(formData);

    if (createBtn) {
      createBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
      createBtn.disabled = true;
    }

    try {
      const data = await fetchJson('/club/events/store', {
        method: 'POST',
        body: JSON.stringify({
          title: formData.title,
          description,
          status: formData.status,
          event_data: formData,
          event_date: formData.date,
          event_time: formData.kickoff,
          location: formData.venue,
        }),
      });

      if (data?.success) {
        if (createBtn) {
          createBtn.innerHTML = '<i class="bi bi-check me-2"></i>Created!';
          createBtn.classList.remove('btn-gradient');
          createBtn.classList.add('btn-success');
        }
        setTimeout(() => {
          const modal = getModalInstance(createMatchModal);
          modal && modal.hide();
          if (publish) window.location.href = '/club/events';
          else window.location.reload();
        }, 1200);
      } else {
        throw new Error(data?.message || 'Unknown error');
      }
    } catch (e) {
      console.error('Error creating event:', e);
      alert('Error creating event. ' + (e.message || 'Please try again.'));
      if (createBtn && originalText != null) {
        createBtn.innerHTML = originalText;
        createBtn.disabled = false;
      }
    }
  }

  function previewEvent() {
    const data = gatherForm(false);
    const description = formatEventDescription(data);
    alert('Event Preview:\n\n' + description);
  }

  async function loadTeamPlayers(teamId) {
    const grid = document.querySelector('.cm-player-grid');
    if (!grid) return;
    grid.innerHTML =
      '<div class="cm-player-empty"><div class="spinner-border text-info" role="status"></div><div class="mt-3">Loading players...</div></div>';
    try {
      const data = await fetchJson(AppCfg.routes.teamPlayers(teamId));
      if (data?.success && Array.isArray(data.players)) {
        displayTeamPlayers(data.players);
      } else {
        grid.innerHTML = '<div class="cm-player-empty">No players found for this team</div>';
      }
    } catch (e) {
      console.error('Error loading team players:', e);
      grid.innerHTML = '<div class="cm-player-empty text-danger">Error loading players</div>';
    }
  }

  function displayTeamPlayers(players) {
    const grid = document.querySelector('.cm-player-grid');
    if (!grid) return;
    if (!Array.isArray(players) || players.length === 0) {
      grid.innerHTML = '<div class="cm-player-empty">No players available for this team</div>';
      return;
    }
    const getInitials = (name) =>
      String(name || '')
        .split(' ')
        .map((w) => w[0] || '')
        .join('')
        .toUpperCase()
        .slice(0, 2);

    grid.innerHTML = players
      .map(
        (p) => `
      <div class="cm-player-chip" data-player-id="${safeHtml(p.id)}">
        <div class="cm-player-avatar">${safeHtml(getInitials(p.name))}</div>
        <div class="cm-player-name">${safeHtml(p.name)}</div>
      </div>`
      )
      .join('');

    grid.querySelectorAll('.cm-player-chip').forEach((chip) =>
      chip.addEventListener('click', function () {
        this.classList.toggle('selected');
      })
    );
  }

  function clearPlayerGrid() {
    const grid = document.querySelector('.cm-player-grid');
    if (grid) grid.innerHTML = '<div class="cm-player-empty">Select a team to view players</div>';
  }

  // Expose test helpers if you want:
  window.testLoadPlayers = function () {
    const t = document.getElementById('modalTeamSelect');
    const id = t ? t.value : null;
    if (id) loadTeamPlayers(id);
  };
  window.testAPI = function () {
    const t = document.getElementById('modalTeamSelect');
    const id = t ? t.value : null;
    if (!id) return;
    fetchJson(AppCfg.routes.teamPlayers(id))
      .then((d) => console.log('Direct API test:', d))
      .catch((e) => console.error('Direct API test error:', e));
  };

  // attach on init
  initializeForm();
  addEventListeners();

  // also re-attach when modal is shown (in case DOM inside modal re-renders)
  createMatchModal.addEventListener('shown.bs.modal', () => {
    setTimeout(() => {
      const teamSelect = document.getElementById('modalTeamSelect');
      addEventListeners();
      if (teamSelect?.value) loadTeamPlayers(teamSelect.value);
    }, 100);
  });
}

/* ---------------------------------
   Boot
-----------------------------------*/
onReady(() => {
  // If you want to preload any modal HTML files:
  // loadModal('createMatch', '/assets/modals/create_match_event_with_repeat.html');

  CalendarModal.init();
  initCalendar();
  initTournamentSearch();
  ClubChat.init();
  initTournamentChat();
  initTournamentDirectory();
  initCreateMatchModal();

  // Expose share helpers globally (optional: the Blade uses these inline)
  window.copyInviteLink = Share.copyInviteLink;
  window.shareViaWhatsApp = Share.shareViaWhatsApp;
  window.shareViaEmail = Share.shareViaEmail;
  window.shareViaSMS = Share.shareViaSMS;
  window.shareViaSocial = Share.shareViaSocial;
});
