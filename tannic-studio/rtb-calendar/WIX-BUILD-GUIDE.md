# Native Wix Calendar — Build Guide

This guide walks you through building the calendar layout using **native Wix elements** (not HTML embed), powered by the Velo page code in `velo-page-code.js`.

---

## ⚠️ FIRST: Find Your Collection ID

Before anything else, you need your **exact CMS Collection ID**:

1. Go to **Wix Dashboard → CMS (Content Manager)**
2. Hover over "Women's Sports Calendar"
3. Click the **⋮ three dots** → **Settings** (or **Manage Collection**)
4. Look for **Collection ID** — it's a camelCase string like `womensSportsCalendar`
5. Open `velo-page-code.js` and update line 13:
   ```js
   const COLLECTION_ID = 'your-actual-id-here';
   ```

### Also verify your field keys:

In the CMS, click the **⚙️ gear** on each column header to see the **Field Key**. The code assumes these keys:

| Your Column | Expected Key | If different, update in code |
|---|---|---|
| Title | `title` | — |
| Sport | `sport` | `F.SPORT` |
| Event Name | `eventName` | `F.EVENT_NAME` |
| Date | `date1` or `date-1` | `F.DATE` ← most likely to differ! |
| Time | `time` | `F.TIME` |
| Location | `location` | `F.LOCATION` |
| US Streaming Platform | `usStreamingPlatform` | `F.STREAMING` |
| Ticket Link | `ticketLink` | `F.TICKET` |
| Additional Info | `additionalInfo` | `F.ADDITIONAL` |
| TV/Streaming | `tvStreaming` | `F.TV` |

**The date field key is the trickiest** — Wix renames "date-1" to either `date1`, `date_1`, or `date-1`. Check this carefully!

---

## Step 1: Enable Velo (Dev Mode)

1. Open your site in the **Wix Editor**
2. Click **Dev Mode** (or **{ } Code**) at the top bar to enable Velo
3. You should see a code panel at the bottom of the editor

---

## Step 2: Build the Page Layout

Navigate to your calendar page. Build these sections **top to bottom**:

### 2A: Control Bar Section

Add a **Section** or **Strip** at the top:
- Background: `#153B50` (navy)
- Padding: 20px top/bottom

Inside, add a **Layouter** or **horizontal Box** containing:

1. **Dropdown** → ID: `sportDropdown`
   - Style: Dark background, white text, gold accent
   - Will be populated by code — don't add options manually

2. **Text Input** → ID: `searchInput`
   - Placeholder: "Search teams, venues, sports..."
   - Style: Rounded corners, light background

3. **Three Buttons** (side by side):
   - Button → ID: `btnWeek`, Label: "This Week"
   - Button → ID: `btnMonth`, Label: "This Month"
   - Button → ID: `btnAll`, Label: "All Upcoming"
   - Style all three: pill-shaped (high border radius), transparent bg, white border, white text
   - The code will dynamically set the active button to gold

---

### 2B: Today's Spotlight Section

Add a **Container Box** → ID: `todaySection`
- Background: slight gold tint (use `#D8AD40` at ~10% opacity, or transparent with a gold left border)
- **This will be hidden by default** — the code shows/hides it

Inside `todaySection`, add:

1. **Text** element — type "🔴 On Today" — Pacifico font, gold color, ~22px

2. **Repeater** → ID: `todayRepeater`
   - Layout: horizontal (1 row)
   - Inside each repeater item, add:
     - **Text** → ID: `todayEmoji` (for sport emoji, ~28px)
     - **Text** → ID: `todayName` (event name, Bitter bold, white)
     - **Text** → ID: `todayTime` (time, Bitter, gold, 13px)
     - **Text** → ID: `todayStream` (streaming info, Bitter, white 60%, 12px)

---

### 2C: Results Count

Add a **Text** element → ID: `resultsCount`
- Font: Bitter, 13px, white at 40% opacity
- The code fills in "Showing X of Y upcoming games"

---

### 2D: Main Event Feed (Repeater)

This is the core of the calendar.

Add a **Repeater** → ID: `eventRepeater`
- Layout: **Grid**, 3 columns, 16px gap
- Adjust to 2 columns at tablet, 1 column at mobile

**Inside each repeater item**, build this card:

#### Card Container
- Add a **Box** → ID: `cardBox`
- Background: `#153B50` (navy)
- Border: 1px solid rgba(255,255,255,0.15)
- Corner radius: 10px
- Padding: 20px
- Shadow: soft drop shadow

#### Inside the card, stack these elements top to bottom:

| # | Element | ID | Font | Color | Size | Notes |
|---|---|---|---|---|---|---|
| 1 | Text | `cardDateHeader` | Pacifico | `#D8AD40` gold | 16px | Date group label — code hides/shows this |
| 2 | Text | `cardSportEmoji` | — | — | 28px | Just the emoji |
| 3 | Text | `cardSportLabel` | Bitter | `#D8AD40` gold | 10px | UPPERCASE, letter-spacing 1px |
| 4 | Text | `cardEventName` | Pacifico | `#F6FCFF` white | 18px | The main event title |
| 5 | Text | `cardDate` | Bitter | `#F6FCFF` white | 13px | "Sat, Feb 22" |
| 6 | Text | `cardTime` | Bitter Bold | `#D8AD40` gold | 13px | "7:00 PM ET" |
| 7 | Line | (decorative) | — | white at 15% | 1px tall | Visual divider |
| 8 | Text | `cardLocation` | Bitter | white at 70% | 12px | "📍 Value City Arena" |
| 9 | Text | `cardStreaming` | Bitter | white at 70% | 12px | "📺 ESPN+" |
| 10 | Text | `cardAdditional` | Bitter italic | white at 50% | 11px | Extra info |
| 11 | Button | `cardTicketBtn` | Bitter Bold | white on `#C04C49` | 12px | "Get Tickets 🎟" — pill-shaped, red bg |

**Optional:** Add a thin **Box** → ID: `cardFeaturedBar`
- Width: 4px, height: 100%, positioned on the left edge
- Background: `#D8AD40` gold
- Code will show/hide this for featured events

**💡 Layout tip:** Use a **vertical Stack** or **Column** inside `cardBox` to keep elements stacked neatly. Place `cardDate` and `cardTime` side by side in a small horizontal Box.

---

### 2E: Empty State

Add a **Container Box** → ID: `emptyState`
- Center-aligned content
- **Hidden by default** (code shows it when no results)

Inside:
- Text: "🏟️" (64px)
- Text: "No Games Found" — Pacifico, 24px, white at 60%
- Text: "Try a different sport, date range, or search term." — Bitter, 14px, white at 40%

---

### 2F: Load More Button

Add a **Button** → ID: `loadMoreBtn`
- Label: "Load More Games"
- Style: outlined (transparent bg, `#D8AD40` gold border, gold text)
- Pill-shaped (high border radius)
- Center-aligned
- **Hidden by default** (code shows when there are more results)

---

### 2G: Footer CTA (optional)

Add a **Section** with navy background:
- Text: "Come Watch With Us" — Pacifico, white, large
- Text: "Cold drinks. Big screens. The best fans in Columbus." — Bitter, white 60%
- Button: "Visit Raise the Bar" — red `#C04C49` bg, white text, links to homepage

---

## Step 3: Set Element IDs

This is **critical** — every element must have the exact ID the code expects.

To set an element's ID:
1. Click the element in the editor
2. Right-click → **View Properties** (or look in the Properties panel)
3. Change the **ID** field to match the IDs listed above

**Double-check these IDs:**
- `#sportDropdown`
- `#searchInput`
- `#btnWeek`, `#btnMonth`, `#btnAll`
- `#todaySection`, `#todayRepeater`
- `#todayEmoji`, `#todayName`, `#todayTime`, `#todayStream`
- `#resultsCount`
- `#eventRepeater`
- `#cardBox`, `#cardDateHeader`, `#cardSportEmoji`, `#cardSportLabel`
- `#cardEventName`, `#cardDate`, `#cardTime`
- `#cardLocation`, `#cardStreaming`, `#cardTicketBtn`, `#cardAdditional`
- `#emptyState`
- `#loadMoreBtn`

---

## Step 4: Paste the Page Code

1. Click on the calendar page in the editor
2. Open the **Code Panel** at the bottom (if not visible, enable Dev Mode)
3. Delete any existing code
4. Paste the entire contents of `velo-page-code.js`
5. Update `COLLECTION_ID` on line 13
6. Update any field keys in the `F` object if they differ from expected

---

## Step 5: Responsive Adjustments

Switch between Editor breakpoints and adjust:

| Breakpoint | Repeater Columns | Control Bar |
|---|---|---|
| Desktop | 3 columns | All in one row |
| Tablet | 2 columns | Wrap dropdown + search to row 2 |
| Mobile | 1 column | Stack everything vertically |

For each breakpoint, click the repeater → adjust columns in the Layout panel.

---

## Step 6: Preview & Test

1. Click **Preview** in the Wix Editor
2. Open the **Developer Console** (toggle it in Preview mode) to see any errors
3. Test:
   - [ ] Events load from CMS
   - [ ] Sport dropdown filters correctly
   - [ ] Search filters live as you type
   - [ ] View toggle switches between week/month/all
   - [ ] Cards show correct data (name, date, time, location, streaming)
   - [ ] Ticket buttons link correctly (open in new tab)
   - [ ] Empty state appears when no results match
   - [ ] Load More appears and works when 30+ results
   - [ ] Today section shows/hides based on whether games exist today
   - [ ] Mobile layout stacks properly

---

## Troubleshooting

**"Cannot read property of undefined" errors:**
→ A field key in the `F` object doesn't match your CMS. Check field keys.

**No events loading:**
→ `COLLECTION_ID` is wrong. Find the exact ID from CMS settings.

**Date sorting is wrong:**
→ The `F.DATE` field key doesn't match. Also check if your Date column is a "Date and Time" type (not "Text").

**Dropdown is empty:**
→ Sport emojis aren't in the `EMOJI_TO_SPORT` map. Add any missing ones.

**Buttons don't change color:**
→ Wix `.style` only works on certain element types. Make sure buttons are standard Wix Button elements (not "Stylable Buttons" from Wix add-ons).

**"$w is not defined" error:**
→ Code is in the wrong place. It must be in the **page code panel**, not a backend file.
