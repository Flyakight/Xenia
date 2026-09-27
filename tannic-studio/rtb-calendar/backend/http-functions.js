// ═══════════════════════════════════════════════════════════════════
// Backend/http-functions.js
// ═══════════════════════════════════════════════════════════════════
// This creates a public API endpoint at:
//   https://www.raisethebarcbus.com/_functions/calendarEvents
//
// The HTML embed fetches from this URL to get live CMS data.
//
// PASTE THIS into: Wix Editor → Code Panel → Backend → http-functions.js
// (Wix auto-creates this file; if it already exists, add the function to it)
// ═══════════════════════════════════════════════════════════════════

import { ok, serverError } from 'wix-http-functions';
import wixData from 'wix-data';

const COLLECTION_ID = 'Import1';

// Emoji → readable sport name
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
  '🤾': 'Handball',
  '🏃': 'Track & Field',
};

// GET /_functions/calendarEvents
export function get_calendarEvents(request) {
  return fetchAllEvents()
    .then(events => {
      return ok({
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*'
        },
        body: JSON.stringify({ events })
      });
    })
    .catch(err => {
      console.error('Calendar API error:', err);
      return serverError({
        body: JSON.stringify({ error: err.message })
      });
    });
}

async function fetchAllEvents() {
  const now = new Date();
  let results = [];
  let hasMore = true;
  let skip = 0;

  while (hasMore) {
    const query = await wixData.query(COLLECTION_ID)
      .ascending('date1')
      .skip(skip)
      .limit(50)
      .find({ suppressAuth: true });

    results = results.concat(query.items);
    hasMore = query.hasNext();
    skip += 50;
  }

  // Normalize, filter past events, and return clean JSON
  return results
    .map(item => {
      let dateObj;
      const rawDate = item.date1;
      const rawTime = item.time || '';

      if (rawDate instanceof Date) {
        dateObj = new Date(rawDate);
        if (typeof rawTime === 'string' && rawTime.trim()) {
          const tp = parseTime(rawTime.trim());
          if (tp) dateObj.setHours(tp.h, tp.m, 0, 0);
        }
      } else if (typeof rawDate === 'string') {
        dateObj = new Date(rawTime ? `${rawDate} ${rawTime}` : rawDate);
        if (isNaN(dateObj.getTime())) dateObj = new Date(rawDate);
      } else {
        return null;
      }

      if (!dateObj || isNaN(dateObj.getTime())) return null;

      const sportEmoji = (item.sport || '').trim();

      return {
        id: item._id,
        sportEmoji,
        sportName: EMOJI_TO_SPORT[sportEmoji] || 'Other',
        eventName: item.eventName || item.title || '',
        eventDate: dateObj.toISOString(),
        location: item.location || '',
        streaming: item.usStreamingPlatform || '',
        tv: item.tvStreaming || '',
        ticketLink: item.ticketLink || '',
        additionalInfo: item.additionalInfo || '',
      };
    })
    .filter(ev => ev && new Date(ev.eventDate) >= now)
    .sort((a, b) => new Date(a.eventDate) - new Date(b.eventDate));
}

function parseTime(str) {
  const m = str.match(/(\d{1,2}):(\d{2})\s*(AM|PM|am|pm)?/);
  if (!m) return null;
  let h = parseInt(m[1], 10);
  const min = parseInt(m[2], 10);
  const mer = (m[3] || '').toUpperCase();
  if (mer === 'PM' && h < 12) h += 12;
  if (mer === 'AM' && h === 12) h = 0;
  return { h, m: min };
}
