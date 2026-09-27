# Women's Sports Calendar — Wix Setup Guide

## Overview

This guide walks you through setting up the redesigned calendar in your Wix site.
All code files are in the `rtb-calendar/` folder — you'll paste them into the Wix editor.

---

## Step 1: Create the CMS Collection

1. Go to **Wix Dashboard → Content Manager → Create Collection**
2. Name it: `WomensSportsCal`
3. Set permissions: **Read = Anyone**, **Write = Admin only**
4. Add these fields:

| Display Name | Field Key | Type |
|---|---|---|
| Title | title | Text (auto-created) |
| Sport Emoji | sportEmoji | Text |
| Sport Name | sportName | Text |
| Event Name | eventName | Text |
| Event Date | eventDate | Date and Time |
| Location | location | Text |
| US Streaming | usStreaming | Text |
| TV Streaming | tvStreaming | Text |
| Ticket Link | ticketLink | URL |
| Additional Info | additionalInfo | Text |
| Sheet Row ID | sheetRowId | Text |
| Is Featured | isFeatured | Boolean |

---

## Step 2: Set Up Google Cloud (for Sheet sync)

1. Go to [console.cloud.google.com](https://console.cloud.google.com)
2. Create a new project (or use existing)
3. Enable the **Google Sheets API**
4. Go to **IAM & Admin → Service Accounts → Create Service Account**
5. Give it a name like "wix-sheets-reader"
6. Download the JSON credentials file
7. Open your Google Sheet → click **Share** → add the service account email (from the JSON file) with **Viewer** access

---

## Step 3: Add Secrets in Wix

1. Go to **Wix Dashboard → Developer Tools → Secrets Manager**
2. Create two secrets:
   - **Name:** `google-sheets-credentials` → **Value:** paste the ENTIRE contents of the JSON credentials file
   - **Name:** `google-sheets-id` → **Value:** the spreadsheet ID from your Google Sheet URL
     (the long string between `/d/` and `/edit` in the URL, e.g., `1lNYW2TedLd8MrIIj0CZ5uuJaaQtLRuUY`)

---

## Step 4: Install npm Package

1. In the Wix Editor, open the **Sidebar → Packages ({})**
2. Click **Install from npm**
3. Search for `jsonwebtoken` and install it

---

## Step 5: Add Backend Files

In the Wix Editor, open the **Code Panel** (bottom of editor) → look for the **Backend** section in the file tree on the left.

Create these files and paste the code from the local files:

| Local file | Wix location |
|---|---|
| `backend/jobs.config` | Backend/jobs.config |
| `backend/googleAuth.js` | Backend/googleAuth.js |
| `backend/syncGoogleSheet.js` | Backend/syncGoogleSheet.js |

---

## Step 6: Build the Page Layout

Open the `/womens-sports-cal` page in the Wix Editor. Build these sections top-to-bottom:

### Hero Section
- Add a **Section** with navy (`#153B50`) background
- Add a **Text** element → type "Women's Sports Calendar" → set font to Pacifico, ~48px, white
- Add a **Text** element below → type "Every game. Every league. Don't miss a moment." → Bitter, 18px, gold (`#D8AD40`)

### Control Bar
- Add a **Section** (slightly lighter navy or dark gradient)
- Inside, add a **Horizontal Layout Box** containing:
  - **Dropdown** → set ID to `sportFilter` (right-click → "View Properties" → change ID)
  - **Text Input** → set ID to `searchInput`, placeholder: "Search teams, events, venues..."
  - Three **Buttons** → set IDs: `btnWeek`, `btnMonth`, `btnAll`
    - Labels: "This Week", "This Month", "All Upcoming"
    - Style: pill-shaped (high border-radius), white border, transparent bg

### Today's Spotlight
- Add a **Section** → set ID to `todaySection`
- Background: gold at low opacity or a gold-tinted box
- Add a **Text** → set ID to `todayHeading`, text: "On Today 🎉", Pacifico, gold
- Add a **Repeater** → set ID to `todayRepeater`
  - Inside each item, add:
    - Text → ID: `todayEmoji`
    - Text → ID: `todayEvent`
    - Text → ID: `todayTime`
    - Text → ID: `todayStreaming`

### Main Event Feed
- Add a **Section** with icy white (`#F6FCFF`) background
- Add a **Text** → ID: `resultsCount`, Bitter 14px, navy
- Add a **Repeater** → set ID to `eventRepeater`
  - Set layout: Grid, 3 columns, 16px gap
  - **Inside each repeater item**, create the card:

    | Element type | ID | Content/Style |
    |---|---|---|
    | Box (container) | `eventCard` | Navy bg, 8px radius, shadow, 20px padding, 4px left border |
    | Text | `cardDateHeader` | Date group header, Pacifico 16px, navy (hidden by default) |
    | Text | `cardSportEmoji` | Large emoji, 32px |
    | Text | `cardSportLabel` | Sport name, Bitter 11px, gold |
    | Text | `cardEventName` | Event title, Pacifico 20px, white |
    | Text | `cardDate` | Date string, Bitter 14px, white |
    | Text | `cardTime` | Time string, Bitter 14px, gold |
    | Text | `cardLocation` | Venue, Bitter 13px, white at 80% |
    | Text | `cardStreaming` | Streaming info, Bitter 13px, white at 80% |
    | Button | `cardTicketBtn` | "Get Tickets", red bg, white text, rounded |
    | Text | `cardAdditional` | Extra info, Bitter 12px, white at 60% |

### Empty State
- Add a **Container** → ID: `emptyState` (hidden by default)
- Inside: fun illustration + text "No upcoming games match your search. Try a different sport or date range!"

### Load More
- Add a **Button** → ID: `loadMoreBtn` (hidden by default)
- Label: "Load More Games"
- Style: outlined, gold border, white text

### Footer CTA
- Add a **Section**, navy bg
- Text: "Come Watch With Us" — Pacifico, white
- Button: "Find Us" → red bg, links to your location page

---

## Step 7: Add Page Code

1. Click on the `/womens-sports-cal` page in the editor
2. Open the **Code Panel** at the bottom
3. Paste the entire contents of `page-code.js`
4. Save

---

## Step 8: Verify Column Mapping

**IMPORTANT:** The sync script assumes specific column positions in your Google Sheet.
Open `backend/syncGoogleSheet.js` and check the `COL` object near the top:

```
SPORT:        1    (column B)
EVENT_NAME:   9    (column J)
DATE:         10   (column K)
TIME:         11   (column L)
LOCATION:     12   (column M)
STREAMING:    13   (column N)
TICKET_LINK:  14   (column O)
ADDITIONAL:   15   (column P)
TV_STREAMING: 20   (column U)
```

If your sheet columns don't match these positions, update the numbers.
Count from 0: column A = 0, B = 1, C = 2, ... J = 9, K = 10, etc.

---

## Step 9: Test the Sync

Before relying on the daily scheduled job, test manually:

1. Create a temporary file: `Backend/testSync.jsw` (note: `.jsw` = web module, callable from frontend)
2. Paste:
   ```javascript
   import { syncCalendarData } from 'backend/syncGoogleSheet';
   export async function runTestSync() {
     await syncCalendarData();
     return 'Sync complete!';
   }
   ```
3. On your page, temporarily add a button and in page code add:
   ```javascript
   import { runTestSync } from 'backend/testSync';
   $w('#testSyncBtn').onClick(async () => {
     const result = await runTestSync();
     console.log(result);
   });
   ```
4. Preview the site, click the button, check the **Developer Console** (Wix Preview → toggle console) for output.
5. Check the CMS collection for new items.
6. **Delete `testSync.jsw` and remove the test button when done.**

---

## Step 10: Publish & Monitor

1. **Publish** the site
2. The scheduled job runs daily at 6:00 AM UTC (~1-2 AM Eastern)
3. Check **Wix Dashboard → Developer Tools → Logs** the next morning for sync output
4. Verify: new games from the sheet appear, past games are removed, no duplicates

---

## Responsive Breakpoints

In the Wix Editor, switch between Desktop / Tablet / Mobile views and adjust:

| View | Event cards | Controls |
|---|---|---|
| Desktop | 3 columns | Single row |
| Tablet | 2 columns | Wrap to 2 rows |
| Mobile | 1 column | Stack vertically |

For each breakpoint, select the repeater and adjust its column count.
Make sure buttons are at least 44px tall on mobile for touch targets.

---

## Troubleshooting

**Sync not running?**
- Check that `jobs.config` is in the `Backend/` folder (not a subfolder)
- Verify secrets are named exactly: `google-sheets-credentials` and `google-sheets-id`
- Check Wix Logs for error messages

**Wrong data showing?**
- Verify column indices in `COL` match your actual sheet
- Check that dates parse correctly (try different date formats in the sheet)

**Emojis not mapping to sport names?**
- Add new emoji→name entries to `EMOJI_TO_SPORT` in `syncGoogleSheet.js`
- The frontend will show "Other" for unmapped emojis

**Filters not working?**
- Make sure all element IDs match exactly (case-sensitive)
- Check the Developer Console in Wix Preview for JavaScript errors
