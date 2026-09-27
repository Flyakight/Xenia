// Backend/syncGoogleSheet.js
// Daily sync: pulls women's sports calendar data from Google Sheets,
// upserts upcoming events into Wix CMS, and removes past events.
//
// Called automatically by the scheduled job defined in jobs.config.
// Can also be called manually for testing (expose via a temp .jsw module).

import wixData from 'wix-data';
import { fetch } from 'wix-fetch';
import { getAccessToken } from 'backend/googleAuth';
import { getSecret } from 'wix-secrets-backend';

// ---------- Configuration ----------

const COLLECTION = 'WomensSportsCal';

// Column indices (0-based) — adjust if your sheet columns shift
// These map to the LATER columns in your sheet (J–U range)
const COL = {
  SPORT:        1,   // B — Sport emoji (🏀, ⚽, etc.)
  EVENT_NAME:   9,   // J — Event Name
  DATE:         10,  // K — Date
  TIME:         11,  // L — Time
  LOCATION:     12,  // M — Location
  STREAMING:    13,  // N — US Streaming Platform
  TICKET_LINK:  14,  // O — Ticket Link
  ADDITIONAL:   15,  // P — Additional Info
  TV_STREAMING: 20   // U — TV/Streaming
};

// Emoji → readable sport name mapping
const EMOJI_TO_SPORT = {
  '🏀': 'Basketball',
  '⚽': 'Soccer',
  '🏒': 'Hockey',
  '🥎': 'Softball',
  '🏐': 'Volleyball',
  '⚾': 'Baseball',
  '🎾': 'Tennis',
  '🏈': 'Football',
  '🏑': 'Field Hockey',
  '🤸': 'Gymnastics',
  '🏊': 'Swimming',
  '🎿': 'Skiing',
  '🏃': 'Track & Field',
  '🤾': 'Handball',
  '🏋': 'Weightlifting',
  '🥊': 'Boxing',
  '🤼': 'Wrestling',
  '⛳': 'Golf',
  '🏸': 'Badminton',
  '🥍': 'Lacrosse'
};

// ---------- Main sync function ----------

export async function syncCalendarData() {
  console.log('[Sync] Starting women\'s sports calendar sync...');

  try {
    // 1. Get credentials and fetch sheet data
    const sheetId = await getSecret('google-sheets-id');
    const token = await getAccessToken();
    const rows = await fetchSheetData(sheetId, token);

    if (!rows || rows.length < 2) {
      console.log('[Sync] No data rows found in sheet.');
      return;
    }

    // 2. Parse rows into CMS items (skip header row)
    const items = [];
    const now = new Date();
    now.setHours(0, 0, 0, 0);

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      try {
        const item = parseRow(row, i);
        if (item && item.eventDate >= now) {
          items.push(item);
        }
      } catch (err) {
        console.log(`[Sync] Skipping row ${i + 1}: ${err.message}`);
      }
    }

    console.log(`[Sync] Parsed ${items.length} upcoming events from sheet.`);

    // 3. Bulk upsert upcoming events
    if (items.length > 0) {
      const BATCH_SIZE = 1000;
      for (let i = 0; i < items.length; i += BATCH_SIZE) {
        const batch = items.slice(i, i + BATCH_SIZE);
        await wixData.bulkSave(COLLECTION, batch, { suppressAuth: true });
        console.log(`[Sync] Upserted batch: ${batch.length} items`);
      }
    }

    // 4. Remove past events from CMS
    await removePastEvents(now);

    console.log('[Sync] Calendar sync complete.');
  } catch (err) {
    console.error(`[Sync] Fatal error: ${err.message}`);
    throw err;
  }
}

// ---------- Helpers ----------

async function fetchSheetData(sheetId, token) {
  // Fetch all data from Sheet1 — adjust sheet name if different
  const range = encodeURIComponent('Sheet1');
  const url = `https://sheets.googleapis.com/v4/spreadsheets/${sheetId}/values/${range}`;

  const response = await fetch(url, {
    headers: { 'Authorization': `Bearer ${token}` }
  });

  if (!response.ok) {
    const text = await response.text();
    throw new Error(`Sheets API error ${response.status}: ${text}`);
  }

  const data = await response.json();
  return data.values || [];
}

function parseRow(row, rowIndex) {
  const eventName = (row[COL.EVENT_NAME] || '').trim();
  const dateStr = (row[COL.DATE] || '').trim();
  const sportEmoji = (row[COL.SPORT] || '').trim();

  // Skip rows missing essential data
  if (!eventName || !dateStr) {
    return null;
  }

  // Parse date + time into a Date object
  const timeStr = (row[COL.TIME] || '12:00 PM').trim();
  const eventDate = parseDateTime(dateStr, timeStr);

  if (!eventDate || isNaN(eventDate.getTime())) {
    throw new Error(`Invalid date: "${dateStr} ${timeStr}"`);
  }

  // Generate a deterministic ID for upsert matching
  const sheetRowId = generateId(sportEmoji, eventName, eventDate);

  return {
    _id: sheetRowId,
    title: eventName,
    sportEmoji: sportEmoji,
    sportName: EMOJI_TO_SPORT[sportEmoji] || extractSportName(sportEmoji),
    eventName: eventName,
    eventDate: eventDate,
    location: (row[COL.LOCATION] || '').trim(),
    usStreaming: (row[COL.STREAMING] || '').trim(),
    tvStreaming: (row[COL.TV_STREAMING] || '').trim(),
    ticketLink: (row[COL.TICKET_LINK] || '').trim(),
    additionalInfo: (row[COL.ADDITIONAL] || '').trim(),
    sheetRowId: sheetRowId,
    isFeatured: false
  };
}

function parseDateTime(dateStr, timeStr) {
  // Handle common date formats from Google Sheets:
  //   "2/1/2025", "02/01/2025", "2025-02-01", "Feb 1, 2025"
  // Combine with time: "6:00 PM", "18:00", "6:00pm"

  // Normalize the combined string and let Date.parse handle it
  const combined = `${dateStr} ${timeStr}`;
  const parsed = new Date(combined);

  if (!isNaN(parsed.getTime())) {
    return parsed;
  }

  // Fallback: try date-only if time caused the issue
  const dateOnly = new Date(dateStr);
  if (!isNaN(dateOnly.getTime())) {
    // Try to parse time separately
    const timeParts = timeStr.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
    if (timeParts) {
      let hours = parseInt(timeParts[1], 10);
      const minutes = parseInt(timeParts[2], 10);
      const meridian = (timeParts[3] || '').toUpperCase();

      if (meridian === 'PM' && hours < 12) hours += 12;
      if (meridian === 'AM' && hours === 12) hours = 0;

      dateOnly.setHours(hours, minutes, 0, 0);
    }
    return dateOnly;
  }

  return null;
}

function generateId(sport, name, date) {
  // Create a stable, URL-safe ID from the key fields
  const raw = `${sport}-${name}-${date.toISOString()}`;
  // Simple hash: convert to a deterministic alphanumeric string
  let hash = 0;
  for (let i = 0; i < raw.length; i++) {
    const char = raw.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convert to 32-bit integer
  }
  return `evt-${Math.abs(hash).toString(36)}`;
}

function extractSportName(emoji) {
  // Fallback: if the emoji isn't in our map, return "Other"
  // You can expand EMOJI_TO_SPORT as new sports appear in the sheet
  return emoji ? 'Other' : 'Unknown';
}

async function removePastEvents(cutoffDate) {
  let removed = 0;
  let hasMore = true;

  while (hasMore) {
    const results = await wixData.query(COLLECTION)
      .lt('eventDate', cutoffDate)
      .limit(100)
      .find({ suppressAuth: true });

    if (results.items.length === 0) {
      hasMore = false;
      break;
    }

    const ids = results.items.map(item => item._id);
    await wixData.bulkRemove(COLLECTION, ids, { suppressAuth: true });
    removed += ids.length;

    // Safety: if we got fewer than 100, we're done
    if (results.items.length < 100) {
      hasMore = false;
    }
  }

  if (removed > 0) {
    console.log(`[Sync] Removed ${removed} past events.`);
  }
}
