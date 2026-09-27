// ═══════════════════════════════════════════════════════════════════
// RAISE THE BAR CBUS — Women's Sports Calendar
// Native Wix Velo Page Code (no HTML embed)
// ═══════════════════════════════════════════════════════════════════
//
// PASTE THIS into the page code panel for your calendar page:
//   Wix Editor → click the page → open code panel at bottom
//
// ─── COLLECTION CONFIG ──────────────────────────────────────────
// ⚠️  UPDATE THIS to your actual collection ID:
//     Dashboard → CMS → ⋮ menu on your collection → "Collection ID"
//     Common auto-generated IDs for "Women's Sports Calendar":
//       "WomensSportsCalendar"  or  "Womens-Sports-Calendar"

const COLLECTION_ID = 'Import1';

// ─── FIELD KEY MAPPING ──────────────────────────────────────────
// These match your CMS fields (from your screenshot):
const F = {
  TITLE:      'title',
  SPORT:      'sport',              // emoji like 🏀
  EVENT_NAME: 'eventName',          // "Ohio State vs Nebraska"
  DATE:       'date1',              // Wix renames "date-1" key → try 'date1' or 'date-1'
  TIME:       'time',               // "6:00 PM"
  LOCATION:   'location',
  STREAMING:  'usStreamingPlatform', // might be 'usStreaming' — check your field key
  TICKET:     'ticketLink',
  ADDITIONAL: 'additionalInfo',
  TV:         'tvStreaming',
};

// ─── EMOJI → SPORT NAME MAP ────────────────────────────────────
const EMOJI_TO_SPORT = {
  '🏀': 'Basketball',
  '⚽': 'Soccer',
  '🏒': 'Hockey',
  '🥎': 'Softball',
  '🏐': 'Volleyball',
  '⚾': 'Baseball',
  '🎾': 'Tennis',
  '🏈': 'Football',
  '🤸': 'Gymnastics',
  '🏊': 'Swimming',
  '🥍': 'Lacrosse',
  '🏑': 'Field Hockey',
};

// ═══════════════════════════════════════════════════════════════════
// WIX ELEMENT IDs — Name these EXACTLY in the Wix Editor
// ═══════════════════════════════════════════════════════════════════
//
// CONTROL BAR:
//   #sportDropdown    → Dropdown element
//   #searchInput      → Text Input
//   #btnWeek          → Button ("This Week")
//   #btnMonth         → Button ("This Month")
//   #btnAll           → Button ("All Upcoming")
//
// TODAY SPOTLIGHT:
//   #todaySection     → Container (Box)
//   #todayRepeater    → Repeater
//     Inside each item:
//       #todayEmoji     → Text
//       #todayName      → Text
//       #todayTime      → Text
//       #todayStream    → Text
//
// MAIN FEED:
//   #resultsCount     → Text
//   #eventRepeater    → Repeater
//     Inside each item:
//       #cardBox          → Container (the whole card)
//       #cardDateHeader   → Text (date group label)
//       #cardSportEmoji   → Text
//       #cardSportLabel   → Text
//       #cardEventName    → Text
//       #cardDate         → Text
//       #cardTime         → Text
//       #cardLocation     → Text
//       #cardStreaming    → Text
//       #cardTicketBtn    → Button
//       #cardAdditional   → Text
//       #cardFeaturedBar  → Box (thin gold bar, 4px wide)
//
// OTHER:
//   #emptyState       → Container (Box)
//   #loadMoreBtn      → Button
//
// ═══════════════════════════════════════════════════════════════════

import wixData from 'wix-data';

// ── State ──
let allEvents = [];
let filteredEvents = [];
let currentSport = 'All';
let currentView = 'week';
let currentSearch = '';
let displayLimit = 30;

// ── Page Init ──
$w.onReady(async function () {

  // Wire up repeater renderers FIRST (before setting data)
  $w('#eventRepeater').onItemReady(renderEventCard);
  $w('#todayRepeater').onItemReady(renderTodayCard);

  // Hide conditional sections
  $w('#todaySection').collapse();
  $w('#emptyState').collapse();
  $w('#loadMoreBtn').collapse();

  // Load data from CMS
  allEvents = await loadEvents();

  // Build sport filter dropdown
  buildSportDropdown(allEvents);

  // Set default view
  highlightViewBtn('week');

  // Initial render
  applyFilters();

  // ── Bind events ──

  $w('#sportDropdown').onChange(() => {
    currentSport = $w('#sportDropdown').value || 'All';
    displayLimit = 30;
    applyFilters();
  });

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
    currentView = 'week';
    displayLimit = 30;
    highlightViewBtn('week');
    applyFilters();
  });

  $w('#btnMonth').onClick(() => {
    currentView = 'month';
    displayLimit = 30;
    highlightViewBtn('month');
    applyFilters();
  });

  $w('#btnAll').onClick(() => {
    currentView = 'all';
    displayLimit = 30;
    highlightViewBtn('all');
    applyFilters();
  });

  $w('#loadMoreBtn').onClick(() => {
    displayLimit += 30;
    renderFeed(filteredEvents);
  });
});

// ═══════════════════════════════════════════════════════════════════
// DATA LOADING
// ═══════════════════════════════════════════════════════════════════

async function loadEvents() {
  const now = new Date();
  let results = [];
  let hasMore = true;
  let skip = 0;

  while (hasMore) {
    const query = await wixData.query(COLLECTION_ID)
      .ascending(F.DATE)
      .skip(skip)
      .limit(50)
      .find();

    results = results.concat(query.items);
    hasMore = query.hasNext();
    skip += 50;
  }

  // Normalize and filter out past events
  return results
    .map(normalizeEvent)
    .filter(ev => ev._dateObj >= now)
    .sort((a, b) => a._dateObj - b._dateObj);
}

function normalizeEvent(item) {
  // Build a proper Date object from the CMS fields
  // Wix CMS "Date and Time" fields return Date objects
  // Wix CMS "Text" date fields return strings
  let dateObj;

  const rawDate = item[F.DATE];
  const rawTime = item[F.TIME] || '';

  if (rawDate instanceof Date) {
    dateObj = new Date(rawDate);
    // If time is stored separately as text, override hours
    if (typeof rawTime === 'string' && rawTime.trim()) {
      const timeParts = parseTimeString(rawTime.trim());
      if (timeParts) {
        dateObj.setHours(timeParts.hours, timeParts.minutes, 0, 0);
      }
    }
  } else if (typeof rawDate === 'string') {
    const combined = rawTime ? `${rawDate} ${rawTime}` : rawDate;
    dateObj = new Date(combined);
    if (isNaN(dateObj.getTime())) {
      dateObj = new Date(rawDate);
    }
  } else {
    dateObj = new Date(0); // fallback
  }

  const sportEmoji = (item[F.SPORT] || '').trim();
  const sportName = EMOJI_TO_SPORT[sportEmoji] || 'Other';

  return {
    _id: item._id,
    _dateObj: dateObj,
    sportEmoji,
    sportName,
    eventName: item[F.EVENT_NAME] || item[F.TITLE] || '',
    location: item[F.LOCATION] || '',
    streaming: item[F.STREAMING] || '',
    tv: item[F.TV] || '',
    ticketLink: item[F.TICKET] || '',
    additionalInfo: item[F.ADDITIONAL] || '',
    isFeatured: item.isFeatured || false,
  };
}

function parseTimeString(str) {
  // Parse "6:00 PM", "18:00", "6:00pm", etc.
  const match = str.match(/(\d{1,2}):(\d{2})\s*(AM|PM|am|pm)?/);
  if (!match) return null;

  let hours = parseInt(match[1], 10);
  const minutes = parseInt(match[2], 10);
  const meridian = (match[3] || '').toUpperCase();

  if (meridian === 'PM' && hours < 12) hours += 12;
  if (meridian === 'AM' && hours === 12) hours = 0;

  return { hours, minutes };
}

// ═══════════════════════════════════════════════════════════════════
// SPORT DROPDOWN
// ═══════════════════════════════════════════════════════════════════

function buildSportDropdown(events) {
  const sportSet = new Set();
  events.forEach(e => { if (e.sportName) sportSet.add(e.sportName); });

  const sports = [...sportSet].sort();
  const options = [
    { label: '🏅 All Sports', value: 'All' },
    ...sports.map(s => {
      const emoji = events.find(e => e.sportName === s)?.sportEmoji || '🏅';
      return { label: `${emoji}  ${s}`, value: s };
    })
  ];

  $w('#sportDropdown').options = options;
  $w('#sportDropdown').value = 'All';
}

// ═══════════════════════════════════════════════════════════════════
// VIEW TOGGLE
// ═══════════════════════════════════════════════════════════════════

function highlightViewBtn(active) {
  const map = { week: '#btnWeek', month: '#btnMonth', all: '#btnAll' };

  // Reset all to "inactive" look
  Object.values(map).forEach(id => {
    $w(id).style.backgroundColor = 'transparent';
    $w(id).style.color = '#F6FCFF';
    $w(id).style.borderColor = '#F6FCFF';
  });

  // Set active
  $w(map[active]).style.backgroundColor = '#D8AD40';
  $w(map[active]).style.color = '#153B50';
  $w(map[active]).style.borderColor = '#D8AD40';
}

// ═══════════════════════════════════════════════════════════════════
// CENTRAL FILTER LOGIC
// ═══════════════════════════════════════════════════════════════════

function applyFilters() {
  let events = [...allEvents];
  const now = new Date();

  // 1. Sport
  if (currentSport !== 'All') {
    events = events.filter(e => e.sportName === currentSport);
  }

  // 2. Date range
  if (currentView === 'week') {
    const cutoff = new Date(now);
    cutoff.setDate(cutoff.getDate() + 7);
    events = events.filter(e => e._dateObj <= cutoff);
  } else if (currentView === 'month') {
    const cutoff = new Date(now);
    cutoff.setDate(cutoff.getDate() + 30);
    events = events.filter(e => e._dateObj <= cutoff);
  }

  // 3. Search
  if (currentSearch) {
    events = events.filter(e =>
      e.eventName.toLowerCase().includes(currentSearch) ||
      e.location.toLowerCase().includes(currentSearch) ||
      e.sportName.toLowerCase().includes(currentSearch) ||
      e.additionalInfo.toLowerCase().includes(currentSearch)
    );
  }

  filteredEvents = events;

  renderFeed(filteredEvents);
  renderToday(filteredEvents);
  updateCount(filteredEvents.length);
  toggleEmpty(filteredEvents.length === 0);
}

// ═══════════════════════════════════════════════════════════════════
// RENDER MAIN FEED
// ═══════════════════════════════════════════════════════════════════

function renderFeed(events) {
  const visible = events.slice(0, displayLimit);

  // Track date grouping
  let lastDateStr = '';
  const items = visible.map(ev => {
    const dateStr = ev._dateObj.toLocaleDateString('en-US', {
      weekday: 'long', month: 'long', day: 'numeric'
    });
    const showHeader = dateStr !== lastDateStr;
    lastDateStr = dateStr;

    return {
      ...ev,
      _showDateHeader: showHeader,
      _dateHeaderText: dateStr,
    };
  });

  $w('#eventRepeater').data = items;

  // Load more
  if (events.length > displayLimit) {
    $w('#loadMoreBtn').expand();
  } else {
    $w('#loadMoreBtn').collapse();
  }
}

// ═══════════════════════════════════════════════════════════════════
// CARD RENDERER (onItemReady)
// ═══════════════════════════════════════════════════════════════════

function renderEventCard($item, itemData) {
  // Date group header
  if (itemData._showDateHeader) {
    $item('#cardDateHeader').text = itemData._dateHeaderText;
    $item('#cardDateHeader').expand();
  } else {
    $item('#cardDateHeader').collapse();
  }

  // Sport
  $item('#cardSportEmoji').text = itemData.sportEmoji || '🏅';
  $item('#cardSportLabel').text = itemData.sportName || '';

  // Event name
  $item('#cardEventName').text = itemData.eventName;

  // Date
  $item('#cardDate').text = itemData._dateObj.toLocaleDateString('en-US', {
    weekday: 'short', month: 'short', day: 'numeric'
  });

  // Time
  $item('#cardTime').text = itemData._dateObj.toLocaleTimeString('en-US', {
    hour: 'numeric', minute: '2-digit'
  }) + ' ET';

  // Location
  if (itemData.location) {
    $item('#cardLocation').text = `📍 ${itemData.location}`;
    $item('#cardLocation').expand();
  } else {
    $item('#cardLocation').collapse();
  }

  // Streaming
  const stream = itemData.streaming || itemData.tv || '';
  if (stream) {
    $item('#cardStreaming').text = `📺 ${stream}`;
    $item('#cardStreaming').expand();
  } else {
    $item('#cardStreaming').collapse();
  }

  // Ticket button
  if (itemData.ticketLink) {
    $item('#cardTicketBtn').label = 'Get Tickets 🎟';
    $item('#cardTicketBtn').link = itemData.ticketLink;
    $item('#cardTicketBtn').target = '_blank';
    $item('#cardTicketBtn').expand();
  } else {
    $item('#cardTicketBtn').collapse();
  }

  // Additional info
  if (itemData.additionalInfo) {
    $item('#cardAdditional').text = itemData.additionalInfo;
    $item('#cardAdditional').expand();
  } else {
    $item('#cardAdditional').collapse();
  }

  // Featured gold bar
  if (itemData.isFeatured) {
    try { $item('#cardFeaturedBar').expand(); } catch(e) {}
  } else {
    try { $item('#cardFeaturedBar').collapse(); } catch(e) {}
  }
}

// ═══════════════════════════════════════════════════════════════════
// TODAY'S SPOTLIGHT
// ═══════════════════════════════════════════════════════════════════

function renderToday(events) {
  const todayStr = new Date().toDateString();
  const todayEvents = events.filter(e => e._dateObj.toDateString() === todayStr);

  if (todayEvents.length > 0) {
    $w('#todaySection').expand();
    $w('#todayRepeater').data = todayEvents;
  } else {
    $w('#todaySection').collapse();
  }
}

function renderTodayCard($item, itemData) {
  $item('#todayEmoji').text = itemData.sportEmoji || '🏅';
  $item('#todayName').text = itemData.eventName || '';
  $item('#todayTime').text = itemData._dateObj.toLocaleTimeString('en-US', {
    hour: 'numeric', minute: '2-digit'
  }) + ' ET';

  const stream = itemData.streaming || itemData.tv || '';
  $item('#todayStream').text = stream || '';
}

// ═══════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════

function updateCount(count) {
  if (count === 0) {
    $w('#resultsCount').text = '';
  } else {
    const shown = Math.min(count, displayLimit);
    $w('#resultsCount').text = `Showing ${shown} of ${count} upcoming game${count !== 1 ? 's' : ''}`;
  }
}

function toggleEmpty(isEmpty) {
  if (isEmpty) {
    $w('#emptyState').expand();
    $w('#eventRepeater').collapse();
    $w('#loadMoreBtn').collapse();
  } else {
    $w('#emptyState').collapse();
    $w('#eventRepeater').expand();
  }
}
