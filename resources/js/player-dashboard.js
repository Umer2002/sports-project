/*
 * Player Dashboard JS – extracted from Blade
 * -------------------------------------------------
 * How it works:
 * 1) In your Blade, embed a single JSON blob:
 *    <script id="dashboard-data" type="application/json">{...}</script>
 * 2) Include this file with Vite/Mix.
 * 3) This file reads that JSON and wires up: modal, calendar, sliders, actions.
 */

(function () {
  'use strict';

  // --- Helpers --------------------------------------------------------------
  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
  const byId = (id) => document.getElementById(id);

  const DATA = (function readInlineJSON() {
    const el = byId('dashboard-data') || byId('player-bootstrap');
    if (!el) return {};
    try { return JSON.parse(el.textContent || '{}'); } catch { return {}; }
  })();

  function safeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value == null ? '' : String(value);
    return div.innerHTML;
  }
  function fmtWhen(dateStr, timeStr) {
    if (!dateStr) return '—';
    try {
      const d = new Date(dateStr + (timeStr ? 'T' + timeStr : ''));
      const D = new Intl.DateTimeFormat(undefined, { weekday: 'short', month: 'short', day: 'numeric' }).format(d);
      return timeStr ? `${D} • ${timeStr}` : D;
    } catch { return [dateStr, timeStr].filter(Boolean).join(' '); }
  }

  // --- Modal open function (kept global if HTML calls it) ------------------
  window.openModalFunction = function (imageSrc, title, description) {
    const img = byId('modal-img');
    if (img) img.src = imageSrc;
    const tag = byId('modal-tag');
    if (tag) tag.textContent = title;
    const desc = byId('desc-para');
    if (desc) desc.textContent = description;
    const modal = byId('myModal-a');
    if (modal) modal.style.display = 'block';
  };

  function initGenericModalClose() {
    const modal = byId('myModal-a');
    const closeBtn = document.querySelector('.close-a');
    const closeBtnFooter = document.querySelector('.btn-clos');

    if (closeBtn) closeBtn.onclick = () => { if (modal) modal.style.display = 'none'; };
    if (closeBtnFooter) closeBtnFooter.onclick = () => { if (modal) modal.style.display = 'none'; };

    window.addEventListener('click', (event) => {
      if (event.target === modal) modal.style.display = 'none';
    });
  }

  // --- Horizontal scroll slider (prev/next) --------------------------------
  function initHorizontalScrollSliders() {
    $$('[data-video-slider]').forEach((container) => {
      const track = container.querySelector('.video-track');
      const prev = container.querySelector('.video-prev');
      const next = container.querySelector('.video-next');
      if (!track || !prev || !next) return;

      container.classList.add('video-slider-ready');

      const scrollStep = () => Math.max(Math.round(track.clientWidth * 0.85), 220);
      const updateControls = () => {
        const maxScrollLeft = Math.max(track.scrollWidth - track.clientWidth - 4, 0);
        const hasOverflow = track.scrollWidth > track.clientWidth + 4;
        prev.hidden = next.hidden = !hasOverflow;
        prev.disabled = track.scrollLeft <= 0;
        next.disabled = track.scrollLeft >= maxScrollLeft;
      };

      prev.addEventListener('click', () => { track.scrollBy({ left: -scrollStep(), behavior: 'smooth' }); });
      next.addEventListener('click', () => { track.scrollBy({ left: scrollStep(), behavior: 'smooth' }); });

      let rafId;
      track.addEventListener('scroll', () => {
        cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(updateControls);
      }, { passive: true });

      window.addEventListener('resize', updateControls);
      updateControls();
      setTimeout(updateControls, 400);
    });
  }

  // --- Calendar data + API --------------------------------------------------
  window.calendarEvents = window.calendarEvents || {};
  function addCal(dateKey, entry) {
    (window.calendarEvents[dateKey] = window.calendarEvents[dateKey] || []).push(entry);
  }

  // Merge inline DATA.* into calendarEvents
  function hydrateCalendarFromData() {
    // pickup games
    if (Array.isArray(DATA.pickupGames)) {
      DATA.pickupGames.forEach((g) => addCal(String(g.date), {
        id: g.id,
        date: String(g.date),
        time: g.time || '',
        text: g.text || `Pickup: ${g.sport || ''}`.trim(),
        type: 'pickup',
        color: g.color || 'blue',
        location: g.location || '',
        description: g.description || 'Join or leave this pickup game.',
        url: g.url || ''
      }));
    }

    // events
    if (Array.isArray(DATA.events)) {
      DATA.events.forEach((e) => addCal(String(e.date), {
        id: e.id,
        resource_type: e.resource_type || 'event',
        resource_id: e.resource_id ?? e.id,
        date: String(e.date),
        time: e.time || '',
        text: e.text || 'Event',
        type: e.type || 'event',
        color: e.color || 'green',
        location: e.location || '',
        description: e.description || '',
        url: e.url || '#'
      }));
    }

    // tournaments
    if (Array.isArray(DATA.tournaments)) {
      DATA.tournaments.forEach((t) => addCal(String(t.date), {
        id: t.id,
        resource_type: 'tournament',
        resource_id: t.id,
        date: String(t.date),
        time: t.time || '',
        text: t.text || `Tournament: ${t.name || ''}`.trim(),
        type: 'tournament',
        color: 'orange',
        location: t.location || '',
        venue_name: t.venue_name || '',
        lat: t.lat ?? null,
        lng: t.lng ?? null,
        description: t.description || '',
        url: t.url || ''
      }));
    }

    // merge into global events hash if present
    if (typeof window.events !== 'undefined' && window.calendarEvents) {
      for (const [k, v] of Object.entries(window.calendarEvents)) {
        window.events[k] = (window.events[k] || []).concat(v);
      }
      if (typeof window.renderCalendar === 'function' && typeof window.currentDate !== 'undefined') {
        window.renderCalendar(window.currentDate);
      }
    }
  }

  // --- Config from DATA -----------------------------------------------------
  const nestedCfg = DATA.playerCalendarConfig || {};
  const metaCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
  const playerCalendarConfig = {
    routes: nestedCfg.routes || DATA.routes || {},
    csrf: DATA.csrf || nestedCfg.csrf || metaCsrf(),
    roles: Array.isArray(DATA.roles) ? DATA.roles : (Array.isArray(nestedCfg.roles) ? nestedCfg.roles : [])
  };
  window.PlayerCalendarConfig = playerCalendarConfig;

  // --- Lightweight notify ---------------------------------------------------
  function playerNotify(message, type = 'info') {
    const alert = document.createElement('div');
    const cls = type === 'success' ? 'success' : type === 'error' ? 'danger' : 'secondary';
    alert.className = `alert alert-${cls} alert-dismissible fade show calendar-alert`;
    alert.style.cssText = 'position:fixed;top:20px;right:20px;z-index:2000;min-width:220px;';
    alert.innerHTML = `${safeHtml(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(alert);
    setTimeout(() => { alert.classList.remove('show'); setTimeout(() => alert.remove(), 300); }, 2800);
  }

  // --- Calendar modal module -----------------------------------------------
  const PlayerCalendarModal = (function () {
    let modalEl = null, modal = null, current = null, seatTimer = null;
    const refs = {}; const roles = playerCalendarConfig.roles || [];

    function cacheRefs() {
      modalEl = byId('staticBackdrop-one');
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

    function ensureModal() {
      if (!modalEl) cacheRefs();
      if (!modalEl) return false;
      if (!modal) {
        // Bootstrap 5 required globally as `bootstrap`
        modal = window.bootstrap && window.bootstrap.Modal ? window.bootstrap.Modal.getOrCreateInstance(modalEl) : null;
        bindActions();
        modalEl.addEventListener('hidden.bs.modal', () => { current = null; reset(); });
      }
      return true;
    }

    function setText(el, value) { if (el) el.textContent = value != null && value !== '' ? value : '—'; }

    function reset() {
      current = null; if (seatTimer) { clearTimeout(seatTimer); seatTimer = null; }
      setText(refs.title, 'Event'); setText(refs.when, '—'); setText(refs.venue, '—'); setText(refs.venueName, '—');
      setCoachNote(null); setWeather(null); setMapPlaceholder('Map will appear here once details load.');
      setHotelsPlaceholder('No nearby hotels have been added yet.');
      renderSummary(refs.clubs, null); renderSummary(refs.coaches, null); renderSummary(refs.players, null);
      renderPreference({}); configureButtons({ routes: {} }); setInteractivity(false);
    }

    function prepareSkeleton(fallback) {
      const title = ((fallback?.title || fallback?.text) || 'Event').replace(/^Tournament:\s*/i, '').trim();
      setText(refs.title, title || 'Event');
      setText(refs.when, fmtWhen(fallback?.date, fallback?.time));
      const venueName = fallback?.venue_name || '';
      const venueLine = [venueName, fallback?.location].filter(Boolean).join(', ');
      setText(refs.venue, venueLine || '—');
      setText(refs.venueName, venueName || venueLine || '—');
      setCoachNote(null); setWeather(null); setMapPlaceholder('Loading map...'); setHotelsPlaceholder('Loading hotels...');
      renderSummary(refs.clubs, null); renderSummary(refs.coaches, null); renderSummary(refs.players, null);
      renderPreference({}); configureButtons({ routes: {} }); setInteractivity(false);
    }

    function setMapPlaceholder(message) { if (refs.map) refs.map.innerHTML = `<div class="text-muted small">${safeHtml(message)}</div>`; }
    function setHotelsPlaceholder(message) { if (refs.hotels) refs.hotels.innerHTML = `<div class="text-muted small">${safeHtml(message)}</div>`; }

    function setLoading(state) {
      if (!modalEl) return;
      modalEl.classList.toggle('calendar-modal-loading', Boolean(state));
    }

    async function load(type, id, fallback) {
      prepareSkeleton(fallback);
      setLoading(true);
      try {
        const data = await playerCalendarApi.load(type, id);
        current = { ...data, type: data.type || type, id: data.id || id };
        render(current);
        modal && modal.show && modal.show();
      } catch (e) {
        playerNotify(e.message || 'Unable to load event details.', 'error');
        if (fallback) {
          modal && modal.show && modal.show();
        }
      } finally {
        setLoading(false);
      }
    }

    function render(data) {
      setText(refs.title, data.title || 'Event'); setText(refs.when, data.when || '—');
      setVenue(data.venue); setMap(data.map); renderHotels(data.hotels);
      renderSummary(refs.clubs, data.clubs); renderSummary(refs.coaches, data.coaches); renderSummary(refs.players, data.players);
      setCoachNote(data.description); setWeather(window.DASHBOARD_WEATHER || {});
      const prefsEnabled = data.preferences_enabled !== false; renderPreference(data.preference || {}); setInteractivity(prefsEnabled);
      current.preferences_enabled = prefsEnabled; configureButtons(data);
    }

    function setInteractivity(enabled) {
      const disabled = !enabled;
      (refs.attendButtons || []).forEach((btn) => (btn.disabled = disabled));
      (refs.carpoolButtons || []).forEach((btn) => (btn.disabled = disabled));
      if (refs.seatsInput) refs.seatsInput.disabled = disabled;
      if (refs.uploadBtn) refs.uploadBtn.disabled = disabled;
      if (refs.uploadInput) refs.uploadInput.disabled = disabled;
      if (refs.saveBtn) refs.saveBtn.disabled = disabled;
    }

    function setVenue(venue = {}) { setText(refs.venueName, venue.name || venue.line || '—'); setText(refs.venue, venue.line || venue.name || '—'); }

    function setMap(map = {}) {
      if (!refs.map) return;
      if (map.embed) refs.map.innerHTML = `<iframe src="${map.embed}" width="100%" height="260" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
      else setMapPlaceholder('Map unavailable for this event.');
      if (refs.mapBtn) { const href = map.view || '#'; refs.mapBtn.href = href || '#'; refs.mapBtn.classList.toggle('disabled', !map.view); }
    }

    function renderHotels(list) {
      if (!refs.hotels) return; refs.hotels.innerHTML = '';
      if (!Array.isArray(list) || list.length === 0) { setHotelsPlaceholder('No nearby hotels have been added yet.'); return; }
      list.forEach((hotel) => {
        const card = document.createElement('div');
        card.className = 'tm-hotel-card';
        card.innerHTML = `<div class="tm-hotel-name">${safeHtml(hotel.name || 'Hotel')}</div><div class="tm-hotel-address text-muted">${safeHtml(hotel.address || '')}</div>`;
        if (hotel.maps_url) {
          const link = document.createElement('a');
          link.href = hotel.maps_url; link.target = '_blank'; link.rel = 'noopener'; link.className = 'tm-hotel-link'; link.textContent = 'View on map';
          card.appendChild(link);
        }
        refs.hotels.appendChild(card);
      });
    }

    function renderSummary(container, summary) {
      if (!container) return; container.innerHTML = '';
      if (!summary || !Array.isArray(summary.list) || summary.list.length === 0) { container.innerHTML = '<span class="text-muted small">—</span>'; return; }
      summary.list.forEach((item) => { const chip = document.createElement('span'); chip.className = 'calendar-chip'; chip.textContent = item; container.appendChild(chip); });
      if (summary.overflow > 0) { const overflow = document.createElement('span'); overflow.className = 'calendar-chip overflow'; overflow.textContent = `+${summary.overflow}`; container.appendChild(overflow); }
    }

    function renderPreference(pref) {
      current = current || {}; current.preference = pref || {};
      setAttendState(pref?.attending_status || null); setCarpoolState(pref?.carpool_status || null); setSeats(pref?.seats_available);
      renderAttachments(pref?.attachments || []);
    }

    function setAttendState(status) { (refs.attendButtons || []).forEach((btn) => { if (!btn.dataset.attendance) return; btn.classList.toggle('active', btn.dataset.attendance === status); }); }
    function setCarpoolState(status) { (refs.carpoolButtons || []).forEach((btn) => { if (!btn.dataset.carpool) return; btn.classList.toggle('active', btn.dataset.carpool === status); }); }
    function setSeats(value) { if (refs.seatsInput) refs.seatsInput.value = value == null ? '' : Number(value); }

    function renderAttachments(list) {
      if (!refs.images) return; refs.images.innerHTML = '';
      if (!Array.isArray(list) || list.length === 0) { refs.images.classList.add('empty'); refs.images.innerHTML = '<div class="text-muted small">No images uploaded yet.</div>'; return; }
      refs.images.classList.remove('empty');
      list.forEach((item) => {
        if (!item?.url) return;
        const link = document.createElement('a');
        link.href = item.url; link.target = '_blank'; link.rel = 'noopener'; link.className = 'tm-image-thumb'; link.title = item.name || 'Event image';
        link.innerHTML = `<img src="${safeHtml(item.url)}" alt="${safeHtml(item.name || 'Event image')}">`;
        refs.images.appendChild(link);
      });
    }

    function setCoachNote(note) { setText(refs.coachNote, note); }

    function setWeather(weather) {
      if (refs.weather) {
        const parts = []; if (weather.city) parts.push(weather.city); if (weather.temp_c != null) parts.push(`${weather.temp_c}° C`); if (weather.condition) parts.push(weather.condition);
        setText(refs.weather, parts.join(' • ') || '—');
      }
      if (refs.weatherIcon) { if (weather.icon_url) { refs.weatherIcon.src = weather.icon_url; refs.weatherIcon.style.display = ''; } else { refs.weatherIcon.style.display = 'none'; } }
    }

    function configureButtons(data) {
      if (refs.addCalBtn) { const icsUrl = data.routes?.ics || (data.type && data.id ? playerCalendarApi.ics(data.type, data.id) : ''); refs.addCalBtn.dataset.icsUrl = icsUrl; refs.addCalBtn.disabled = !icsUrl; }
      if (refs.chatBtn) { refs.chatBtn.disabled = !data.team; if (data.team?.name) refs.chatBtn.title = `Open chat for ${data.team.name}`; else refs.chatBtn.removeAttribute('title'); }
    }

    async function savePreference(patch = {}, opts = {}) {
      if (!current) return false;
      if (current.preferences_enabled === false) { if (!opts.quiet) playerNotify('Event preferences will be available once the latest migrations run.', 'info'); return false; }
      try {
        const res = await playerCalendarApi.save(current.type, current.id, patch);
        if (res?.preference) renderPreference(res.preference);
        if (opts.successMessage) playerNotify(opts.successMessage, 'success');
        return true;
      } catch (e) { if (!opts.quiet) playerNotify(e.message || 'Unable to save.', 'error'); return false; }
    }

    async function handleAttend(status) { const previous = current?.preference?.attending_status || null; setAttendState(status); const ok = await savePreference({ attending_status: status }, { successMessage: 'Attendance updated.' }); if (!ok) setAttendState(previous); }
    async function handleCarpool(status) { const previous = current?.preference?.carpool_status || null; setCarpoolState(status); const ok = await savePreference({ carpool_status: status }, { successMessage: 'Carpool preference saved.' }); if (!ok) setCarpoolState(previous); }

    function handleSeatsInput() {
      if (!refs.seatsInput) return; const raw = refs.seatsInput.value.trim(); const value = raw === '' ? null : Number.parseInt(raw, 10);
      if (raw !== '' && (Number.isNaN(value) || value < 0)) return;
      clearTimeout(seatTimer); seatTimer = setTimeout(() => { savePreference({ seats_available: value }, { successMessage: 'Seats updated.' }); }, 500);
    }

    async function uploadImages(files) {
      if (!current || !files.length) return; if (current.preferences_enabled === false) { playerNotify('Image uploads will be available once the latest migrations run.', 'info'); return; }
      for (const file of files) {
        try {
          const res = await playerCalendarApi.upload(current.type, current.id, file);
          if (res?.attachments) { current.preference = current.preference || {}; current.preference.attachments = res.attachments; renderAttachments(res.attachments); playerNotify('Image uploaded successfully.', 'success'); }
        } catch (e) { playerNotify(e.message || 'Image upload failed.', 'error'); }
      }
      if (refs.uploadInput) refs.uploadInput.value = '';
    }

    function addToCalendar() { if (!current || !refs.addCalBtn) return; const url = refs.addCalBtn.dataset.icsUrl; if (!url) { playerNotify('Calendar file unavailable.', 'error'); return; } window.open(url, '_blank', 'noopener'); savePreference({ calendar_added: true }, { quiet: true }); playerNotify('Calendar download started.', 'success'); }

    async function shareEvent() {
      if (!current) return;
      const textParts = [current.title || 'Event', current.when || '', current.venue?.line || current.venue?.name || ''].filter(Boolean);
      const payload = { title: current.title || 'Event', text: textParts.join('\n'), url: window.location.href };
      try {
        if (navigator.share) await navigator.share(payload);
        else if (navigator.clipboard?.writeText) { await navigator.clipboard.writeText(payload.text + '\n' + payload.url); playerNotify('Event details copied to clipboard.', 'success'); }
        else playerNotify('Sharing is not supported on this device.', 'error');
      } catch { playerNotify('Unable to share this event.', 'error'); }
    }

    function openTeamChat() {
      if (!current?.team) { playerNotify('Team chat is unavailable for this event.', 'error'); return; }
      const role = roles.includes('club') ? 'club' : 'player';
      const url = playerCalendarApi.teamChat(current.team.id, role); if (!url) { playerNotify('Unable to open team chat.', 'error'); return; }
      window.open(url, '_blank', 'noopener');
    }

    function bindActions() {
      if (!modalEl) return;
      (refs.attendButtons || []).forEach((btn) => {
        if (btn.classList.contains('btn-yes')) btn.dataset.attendance = 'yes';
        else if (btn.classList.contains('btn-maybe')) btn.dataset.attendance = 'maybe';
        else btn.dataset.attendance = 'no';
        btn.addEventListener('click', () => handleAttend(btn.dataset.attendance));
      });

      (refs.carpoolButtons || []).forEach((btn) => {
        const label = (btn.textContent || '').toLowerCase();
        btn.dataset.carpool = label.includes('drive') ? 'driver' : 'rider';
        btn.addEventListener('click', () => handleCarpool(btn.dataset.carpool));
      });

      if (refs.seatsInput) refs.seatsInput.addEventListener('input', handleSeatsInput);

      if (refs.uploadBtn && refs.uploadInput) {
        refs.uploadInput.setAttribute('multiple', 'multiple');
        refs.uploadBtn.addEventListener('click', () => refs.uploadInput.click());
        refs.uploadInput.addEventListener('change', () => { const files = Array.from(refs.uploadInput.files || []); uploadImages(files); });
      }

      if (refs.addCalBtn) refs.addCalBtn.addEventListener('click', addToCalendar);
      if (refs.shareBtn) refs.shareBtn.addEventListener('click', shareEvent);
      if (refs.chatBtn) refs.chatBtn.addEventListener('click', openTeamChat);
      if (refs.saveBtn) refs.saveBtn.addEventListener('click', () => savePreference({}, { successMessage: 'Preferences saved.' }));
    }

    return {
      open(type, id, fallback) { if (!ensureModal()) { playerNotify('Event modal unavailable.', 'error'); return; } load(type, id, fallback); },
      openFallback(fallback) { if (!ensureModal()) { playerNotify('Event modal unavailable.', 'error'); return; } reset(); prepareSkeleton(fallback); setInteractivity(false); modal && modal.show && modal.show(); },
    };
  })();

  // --- API wrapper (uses PlayerCalendarConfig) ------------------------------
  const playerCalendarApi = (function () {
    const cfg = window.PlayerCalendarConfig || { routes: {}, csrf: '', roles: [] };
    const fill = (tpl, type, id) => (tpl || '').replace('__TYPE__', encodeURIComponent(String(type))).replace('__ID__', encodeURIComponent(String(id)));
    const fillId = (tpl, id) => (tpl || '').replace('__ID__', encodeURIComponent(String(id)));

    async function request(url, { method = 'GET', body = null } = {}) {
      if (!url) {
        throw new Error('Calendar endpoint unavailable.');
      }
      const headers = new Headers(); headers.set('X-Requested-With', 'XMLHttpRequest');
      if (!(body instanceof FormData)) headers.set('Accept', 'application/json');
      if (cfg.csrf) headers.set('X-CSRF-TOKEN', cfg.csrf);
      let payload = body;
      if (body && !(body instanceof FormData) && typeof body !== 'string') { headers.set('Content-Type', 'application/json'); payload = JSON.stringify(body); }
      const res = await fetch(url, { method, headers, body: payload, credentials: 'same-origin' });
      const ct = res.headers.get('content-type') || ''; const data = ct.includes('application/json') ? await res.json().catch(() => null) : null;
      if (!res.ok) throw new Error((data && (data.message || data.error)) || 'Request failed.');
      return data;
    }

    return {
      load(type, id) {
        const tpl = cfg.routes?.item;
        if (!tpl) return Promise.reject(new Error('Calendar item endpoint unavailable.'));
        return request(fill(tpl, type, id));
      },
      save(type, id, payload) {
        const tpl = cfg.routes?.preference;
        if (!tpl) return Promise.reject(new Error('Calendar preference endpoint unavailable.'));
        return request(fill(tpl, type, id), { method: 'POST', body: payload });
      },
      upload(type, id, file) {
        const tpl = cfg.routes?.upload;
        if (!tpl) return Promise.reject(new Error('Calendar upload endpoint unavailable.'));
        const formData = new FormData(); formData.append('image', file);
        return request(fill(tpl, type, id), { method: 'POST', body: formData });
      },
      ics(type, id) {
        const tpl = cfg.routes?.ics;
        if (!tpl) return '';
        return fill(tpl, type, id);
      },
      teamChat(teamId, scope) {
        const tpl = scope === 'club' ? cfg.routes?.teamClub : cfg.routes?.teamPlayer;
        if (!tpl) return '';
        return fillId(tpl, teamId);
      },
      roles: cfg.roles || [],
    };
  })();

  // --- Expose calendar event opener ----------------------------------------
  window.openCalendarEvent = function (d) {
    const type = d.resource_type || d.type || 'event';
    const id = d.resource_id || d.id; const supported = ['event', 'tournament', 'match'];
    if (!type || !id || !supported.includes(type)) { PlayerCalendarModal.openFallback(d); return; }
    PlayerCalendarModal.open(type, id, d);
  };

  // --- Player video slider (cover, dots, autoplay) --------------------------
  function initPlayerVideoSliders() {
    $$('[data-video-slider]').forEach((slider) => {
      let slides = [];
      try { const raw = slider.dataset.videos || '[]'; slides = JSON.parse(raw); } catch { slides = []; }
      if (!Array.isArray(slides) || !slides.length) return;

      const stage = slider.querySelector('[data-slider-stage]');
      const cover = slider.querySelector('[data-slider-cover]');
      const coverImg = slider.querySelector('[data-slider-cover-img]');
      const coverVideo = slider.querySelector('[data-slider-video]');
      const titleEl = slider.querySelector('[data-slider-title]');
      const userEl = slider.querySelector('[data-slider-user]');
      const timeEl = slider.querySelector('[data-slider-time]');
      const timeSeparator = slider.querySelector('[data-slider-separator]');
      const descriptionEl = slider.querySelector('[data-slider-description]');
      const indicatorEl = slider.querySelector('[data-slider-indicator]');
      const dotsWrap = slider.querySelector('[data-slider-dots]');
      const prevBtn = slider.querySelector('[data-slider-prev]');
      const nextBtn = slider.querySelector('[data-slider-next]');
      const redirectBase = slider.dataset.redirectBase || '/player/videos/explore';

      let index = 0; let timerId = null; const AUTOPLAY_INTERVAL = 8000;
      const fallbackImg = (DATA.assets && DATA.assets.videoThumb) || DATA.imagePlaceholder || '';

      const buildUrl = (base, id) => {
        const cleanBase = (base || '/player/videos/explore').replace(/\/$/, '');
        return id ? `${cleanBase}/${id}` : cleanBase;
      };

      const setCoverImage = (url, preview, previewType) => {
        const value = url ? `url(${url})` : 'none';
        if (cover) { const bgValue = preview && previewType === 'file' ? 'none' : value; cover.style.setProperty('--player-video-cover', bgValue); cover.style.backgroundImage = bgValue; }
        if (coverImg) { coverImg.src = url || fallbackImg; coverImg.style.display = preview ? 'none' : 'block'; }
        if (coverVideo) {
          if (preview && previewType === 'file') { if (coverVideo.src !== preview) { coverVideo.src = preview; coverVideo.load(); } coverVideo.poster = ''; coverVideo.style.display = 'block'; }
          else { if (coverVideo.src) { coverVideo.pause(); coverVideo.removeAttribute('src'); coverVideo.load(); } coverVideo.poster = url || fallbackImg; coverVideo.style.display = 'none'; }
        }
      };

      const render = () => {
        const current = slides[index] || {};
        setCoverImage(current.thumbnail || '', current.preview || '', current.preview_type || 'file');
        if (titleEl) titleEl.textContent = current.title || 'Player video';
        if (userEl) userEl.textContent = current.user || 'Play2Earn';
        if (timeEl) timeEl.textContent = current.time || '';
        if (timeSeparator) timeSeparator.style.visibility = current.time ? 'visible' : 'hidden';
        if (descriptionEl) descriptionEl.textContent = current.description || '';
        if (indicatorEl) indicatorEl.textContent = `${index + 1} / ${slides.length}`;
        if (dotsWrap) dotsWrap.querySelectorAll('.player-video-dot').forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === index));
      };

      const goTo = (targetIndex) => { if (!slides.length) return; index = (targetIndex + slides.length) % slides.length; render(); };
      const go = (delta) => goTo(index + delta);
      const stopAuto = () => { if (timerId) { clearInterval(timerId); timerId = null; } };
      const startAuto = () => { if (timerId || slides.length <= 1) return; timerId = setInterval(() => go(1), AUTOPLAY_INTERVAL); };

      if (dotsWrap) {
        dotsWrap.innerHTML = '';
        slides.forEach((_, dotIndex) => {
          const dot = document.createElement('button');
          dot.type = 'button'; dot.className = 'player-video-dot' + (dotIndex === index ? ' active' : '');
          dot.setAttribute('aria-label', `Show video ${dotIndex + 1}`);
          dot.addEventListener('click', (event) => { event.stopPropagation(); stopAuto(); goTo(dotIndex); startAuto(); });
          dotsWrap.appendChild(dot);
        });
      }

      if (prevBtn) prevBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); stopAuto(); go(-1); startAuto(); });
      if (nextBtn) nextBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); stopAuto(); go(1); startAuto(); });

      const openCurrent = () => { const current = slides[index] || {}; const target = current.url || buildUrl(redirectBase, current.id); if (target) window.location.href = target; };
      if (stage) {
        stage.addEventListener('click', openCurrent);
        stage.addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openCurrent(); } });
        stage.addEventListener('mouseenter', stopAuto);
        stage.addEventListener('mouseleave', startAuto);
      }

      slider.addEventListener('mouseenter', stopAuto);
      slider.addEventListener('mouseleave', startAuto);

      render(); startAuto();
    });
  }

  // --- Join/Leave pickup game ----------------------------------------------
  function csrf() { return playerCalendarConfig.csrf || (document.querySelector('meta[name="csrf-token"]')?.content || ''); }
  window.joinGame = function (btnOrId) {
    const id = typeof btnOrId === 'object' && btnOrId?.dataset ? btnOrId.dataset.id : btnOrId;
    fetch(`/pickup-games/${id}/join`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf() } }).then(() => location.reload());
  };
  window.leaveGame = function (btnOrId) {
    const id = typeof btnOrId === 'object' && btnOrId?.dataset ? btnOrId.dataset.id : btnOrId;
    fetch(`/pickup-games/${id}/leave`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf() } }).then(() => location.reload());
  };

  // --- Init -----------------------------------------------------------------
  function init() {
    initGenericModalClose();
    initHorizontalScrollSliders();
    hydrateCalendarFromData();
    initPlayerVideoSliders();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
