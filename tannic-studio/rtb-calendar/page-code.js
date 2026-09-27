// ─────────────────────────────────────────────────────────────
// Page Code: Women's Sports Calendar (/womens-sports-cal)
// ─────────────────────────────────────────────────────────────
// Paste this into the page code panel in the Wix Editor
// (click the page, then open the code panel at the bottom).
//
// ELEMENT IDs — you must name your Wix elements to match:
//   #sportFilter      — Dropdown (sport filter)
//   #searchInput      — Text Input (search box)
//   #btnWeek          — Button ("This Week")
//   #btnMonth         — Button ("This Month")
//   #btnAll           — Button ("All Upcoming")
//   #todaySection     — Container/Section (today's spotlight)
//   #todayHeading     — Text ("On Today 🎉")
//   #todayRepeater    — Repeater (today's games)
//   #eventRepeater    — Repeater (main feed)
//   #resultsCount     — Text ("Showing X upcoming events")
//   #emptyState       — Container (no-results message)
//   #loadMoreBtn      — Button ("Load More")
//
// Inside #eventRepeater item template:
//   #eventCard        — Container (the card box)
//   #cardDateHeader   — Text (date group label, e.g. "Saturday, February 22")
//   #cardSportEmoji   — Text (large emoji)
//   #cardSportLabel   — Text (sport name)
//   #cardEventName    — Text (event title)
//   #cardDate         — Text (formatted date)
//   #cardTime         — Text (formatted time)
//   #cardLocation     — Text (venue)
//   #cardStreaming    — Text (streaming platform)
//   #cardTicketBtn    — Button ("Get Tickets")
//   #cardAdditional   — Text (extra info)
//
// Inside #todayRepeater item template:
//   #todayEmoji       — Text
//   #todayEvent       — Text
//   #todayTime        — Text
//   #todayStreaming   — Text
// ─────────────────────────────────────────────────────────────

import wixData from 'wix-data';

// ── State ──
let allEvents = [];
let filteredEvents = [];
let currentSport = 'All';
let currentView = 'week';
let currentSearch = '';
let displayLimit = 30;

// ── Brand colors (for dynamic style changes) ──
const COLORS = {
  red:   '#C04C49',
  navy:  '#153B50',
  gold:  '#D8AD40',
  white: '#F6FCFF'
};

// ── Page initialization ──
$w.onReady(async function () {
  // Set up repeater renderers
  $w('#eventRepeater').onItemReady(renderEventCard);
  $w('#todayRepeater').onItemReady(renderTodayCard);

  // Hide sections until data loads
  $w('#todaySection').hide();
  $w('#emptyState').hide();
  $w('#loadMoreBtn').hide();

  // Load data
  allEvents = await loadUpcomingEvents();

  // Populate sport filter dropdown
  populateSportFilter(allEvents);

  // Set default view and render
  setActiveView('week');
  applyFilters();

  // ── Event handlers ──

  $w('#sportFilter').onChange(() => {
    currentSport = $w('#sportFilter').value || 'All';
    displayLimit = 30;
    applyFilters();
  });

  // Debounced search — wait 300ms after user stops typing
  let searchTimer;
  $w('#searchInput').onInput(() => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      currentSearch = $w('#searchInput').value.trim().toLowerCase();
      displayLimit = 30;
      applyFilters();
    }, 300);
  });

  $w('#btnWeek').onClick(() => {
    setActiveView('week');
    displayLimit = 30;
    applyFilters();
  });

  $w('#btnMonth').onClick(() => {
    setActiveView('month');
    displayLimit = 30;
    applyFilters();
  });

  $w('#btnAll').onClick(() => {
    setActiveView('all');
    displayLimit = 30;
    applyFilters();
  });

  $w('#loadMoreBtn').onClick(() => {
    displayLimit += 30;
    renderFeed(filteredEvents);
  });
});

// ── Data loading ──

async function loadUpcomingEvents() {
  const now = new Date();
  let results = [];
  let skip = 0;
  const PAGE_SIZE = 50;

  let hasMore = true;
  while (hasMore) {
    const query = await wixData.query('WomensSportsCal')
      .gt('eventDate', now)
      .ascending('eventDate')
      .skip(skip)
      .limit(PAGE_SIZE)
      .find();

    results = results.concat(query.items);
    hasMore = query.hasNext();
    skip += PAGE_SIZE;
  }

  return results;
}

// ── Sport filter population ──

function populateSportFilter(events) {
  const sportSet = new Set();
  events.forEach(e => {
    if (e.sportName) sportSet.add(e.sportName);
  });

  const sports = [...sportSet].sort();
  const options = [
    { label: '🏅 All Sports', value: 'All' },
    ...sports.map(s => {
      const emoji = events.find(e => e.sportName === s)?.sportEmoji || '🏅';
      return { label: `${emoji} ${s}`, value: s };
    })
  ];

  $w('#sportFilter').options = options;
  $w('#sportFilter').value = 'All';
}

// ── View toggle ──

function setActiveView(view) {
  currentView = view;

  // Reset all buttons to inactive style
  const buttons = ['#btnWeek', '#btnMonth', '#btnAll'];
  buttons.forEach(id => {
    $w(id).style.backgroundColor = 'transparent';
    $w(id).style.color = COLORS.white;
    $w(id).style.borderColor = COLORS.white;
  });

  // Set active button
  const activeId = view === 'week' ? '#btnWeek' : view === 'month' ? '#btnMonth' : '#btnAll';
  $w(activeId).style.backgroundColor = COLORS.gold;
  $w(activeId).style.color = COLORS.navy;
  $w(activeId).style.borderColor = COLORS.gold;
}

// ── Central filter logic ──

function applyFilters() {
  let events = [...allEvents];

  // 1. Sport filter
  if (currentSport !== 'All') {
    events = events.filter(e => e.sportName === currentSport);
  }

  // 2. Date view filter
  const now = new Date();
  if (currentView === 'week') {
    const cutoff = new Date(now);
    cutoff.setDate(cutoff.getDate() + 7);
    events = events.filter(e => new Date(e.eventDate) <= cutoff);
  } else if (currentView === 'month') {
    const cutoff = new Date(now);
    cutoff.setDate(cutoff.getDate() + 30);
    events = events.filter(e => new Date(e.eventDate) <= cutoff);
  }
  // "all" → no additional date filtering

  // 3. Search filter
  if (currentSearch.length > 0) {
    events = events.filter(e =>
      (e.eventName || '').toLowerCase().includes(currentSearch) ||
      (e.location || '').toLowerCase().includes(currentSearch) ||
      (e.sportName || '').toLowerCase().includes(currentSearch) ||
      (e.additionalInfo || '').toLowerCase().includes(currentSearch)
    );
  }

  filteredEvents = events;

  // Render everything
  renderFeed(filteredEvents);
  renderTodaySpotlight(filteredEvents);
  updateResultsCount(filteredEvents.length);
  toggleEmptyState(filteredEvents.length === 0);
}

// ── Feed rendering ──

function renderFeed(events) {
  const visible = events.slice(0, displayLimit);

  // Add date-header tracking
  let lastDateStr = '';
  const itemsWithHeaders = visible.map(event => {
    const d = new Date(event.eventDate);
    const dateStr = d.toLocaleDateString('en-US', {
      weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
    });
    const showHeader = dateStr !== lastDateStr;
    lastDateStr = dateStr;

    return {
      ...event,
      _id: event._id, // ensure _id carries through
      _showDateHeader: showHeader,
      _dateHeaderText: dateStr
    };
  });

  $w('#eventRepeater').data = itemsWithHeaders;

  // Show/hide Load More
  if (events.length > displayLimit) {
    $w('#loadMoreBtn').show();
  } else {
    $w('#loadMoreBtn').hide();
  }
}

// ── Card rendering (called per repeater item) ──

function renderEventCard($item, itemData) {
  // Date group header
  if (itemData._showDateHeader) {
    $item('#cardDateHeader').text = itemData._dateHeaderText;
    $item('#cardDateHeader').show();
  } else {
    $item('#cardDateHeader').hide();
  }

  // Sport
  $item('#cardSportEmoji').text = itemData.sportEmoji || '🏅';
  $item('#cardSportLabel').text = itemData.sportName || '';

  // Event name
  $item('#cardEventName').text = itemData.eventName || '';

  // Date + Time
  const d = new Date(itemData.eventDate);
  $item('#cardDate').text = d.toLocaleDateString('en-US', {
    weekday: 'short', month: 'short', day: 'numeric'
  });
  $item('#cardTime').text = d.toLocaleTimeString('en-US', {
    hour: 'numeric', minute: '2-digit'
  }) + ' ET';

  // Location
  $item('#cardLocation').text = itemData.location ? `📍 ${itemData.location}` : '';

  // Streaming info
  const stream = itemData.usStreaming || itemData.tvStreaming || '';
  if (stream) {
    $item('#cardStreaming').text = `📺 ${stream}`;
    $item('#cardStreaming').show();
  } else {
    $item('#cardStreaming').hide();
  }

  // Ticket button
  if (itemData.ticketLink) {
    $item('#cardTicketBtn').link = itemData.ticketLink;
    $item('#cardTicketBtn').target = '_blank';
    $item('#cardTicketBtn').label = 'Get Tickets 🎟';
    $item('#cardTicketBtn').show();
  } else {
    $item('#cardTicketBtn').hide();
  }

  // Additional info
  if (itemData.additionalInfo) {
    $item('#cardAdditional').text = itemData.additionalInfo;
    $item('#cardAdditional').show();
  } else {
    $item('#cardAdditional').hide();
  }

  // Featured highlight (gold left border)
  if (itemData.isFeatured) {
    $item('#eventCard').style.borderColor = COLORS.gold;
  }
}

// ── Today's spotlight ──

function renderTodaySpotlight(events) {
  const todayStr = new Date().toDateString();
  const todayEvents = events.filter(e =>
    new Date(e.eventDate).toDateString() === todayStr
  );

  if (todayEvents.length > 0) {
    $w('#todaySection').show();
    $w('#todayRepeater').data = todayEvents;
  } else {
    $w('#todaySection').hide();
  }
}

function renderTodayCard($item, itemData) {
  $item('#todayEmoji').text = itemData.sportEmoji || '🏅';
  $item('#todayEvent').text = itemData.eventName || '';

  const d = new Date(itemData.eventDate);
  $item('#todayTime').text = d.toLocaleTimeString('en-US', {
    hour: 'numeric', minute: '2-digit'
  }) + ' ET';

  const stream = itemData.usStreaming || itemData.tvStreaming || '';
  $item('#todayStreaming').text = stream ? `📺 ${stream}` : '';
}

// ── UI helpers ──

function updateResultsCount(count) {
  if (count === 0) {
    $w('#resultsCount').text = '';
  } else {
    $w('#resultsCount').text = `Showing ${Math.min(count, displayLimit)} of ${count} upcoming event${count !== 1 ? 's' : ''}`;
  }
}

function toggleEmptyState(isEmpty) {
  if (isEmpty) {
    $w('#emptyState').show();
    $w('#eventRepeater').hide();
    $w('#loadMoreBtn').hide();
  } else {
    $w('#emptyState').hide();
    $w('#eventRepeater').show();
  }
}
