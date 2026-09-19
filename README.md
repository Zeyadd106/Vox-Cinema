# Vox Cinemas

Movie ticket booking platform — migrated from Laravel/Blade to a modern TypeScript full-stack app.

- **Frontend:** React 19 + TypeScript + Tailwind CSS 4 (Vite) — `client/`
- **Backend:** Node.js + Express + TypeScript + SQLite (`node:sqlite`) + JWT auth — `server/`

## Features

- **Real VOX Egypt catalog**: 20 now-showing + 18 coming-soon titles mirrored from egy.voxcinemas.com (titles, Egyptian age ratings, synopses, posters stored locally, release dates), programmed across all cinemas and halls

- **Multi-cinema**: browse cinemas (Mall of Egypt, City Centre Almaza, City Centre Alexandria), each with its own halls (Standard / IMAX / GOLD), seat layouts and programming
- Header like voxcinemas.com: utility bar (search overlay, Login/Sign-Up, Egypt locale, EN/AR toggle with RTL), dropdown nav (Movies, Food & Drinks, Ways to Watch, Offers), cinema strip with Find Times shortcut, mobile flyout menu
- Header cinema picker (persisted) + homepage **"Find Times and Book"** widget: cinema + movie + date → grouped showtimes with format/hall labels
- Browse now-showing & coming-soon movies, search, movie details with trailer + showtimes filtered by cinema
- Movie page like voxcinemas.com: title + rating badge + Facebook/X share, poster + trailer hero, metadata aside (genre/runtime/release/starring/language/subtitles), Today/Tomorrow date tabs, showtimes grouped by cinema → format, recommendations, Now Showing / Coming Soon shortcuts
- Seat selection (A–G × 10) with live availability, $12/ticket booking flow
- **Seat holds with expiry**: seats are held for 10 minutes (`HOLD_TTL_MINUTES`) while you check out; expired holds auto-release, double-holds are rejected
- **Real fee & tax pricing**: booking fee + tax rate from Admin → Settings are applied to every booking with a line-item breakdown (subtotal / fee / tax / total)
- Simulated credit-card / PayPal checkout, e-ticket confirmation page
- **QR e-tickets + usher check-in**: each paid booking gets a signed QR code; Admin → Check-in validates tickets and prevents reuse
- Auth (register/login, JWT), user dashboard (upcoming/history), my bookings, cancel
- Admin panel: stats dashboard, movie CRUD with poster upload, **cinema & hall management (auto seat-layout generation)**, showtime CRUD per hall, all bookings, user management (toggle admin), ticket check-in, site settings

## Prerequisites

- Node.js 22.5+ (uses built-in `node:sqlite`; tested on Node 24)
- npm

## Run from a fresh terminal

```bash
# 1. install everything (root helper installs server + client)
npm run install:all

# 2. configure backend (optional — works out of the box with defaults)
copy server\.env.example server\.env   # Windows
# cp server/.env.example server/.env   # macOS/Linux

# 3. start both servers together
npm run dev
```

- API: http://localhost:4000 (`/api/health`)
- App: http://localhost:5173

The SQLite database (`server/data/vox.db`) is created and seeded automatically on first start.

### Seed accounts

| Role  | Email                  | Password   |
| ----- | ---------------------- | ---------- |
| Admin | admin@voxcinemas.com   | password   |
| User  | user@example.com       | password   |

### Other commands

```bash
npm run build       # build server (tsc) + client (tsc + vite)
npm run typecheck   # typecheck both workspaces
npm run start       # run the built backend (serve client/dist separately)
```

To run servers individually: `npm run dev:server` (port 4000), `npm run dev:client` (port 5173, proxies `/api` + `/uploads` to the backend).

## Project structure

```
├── client/            # React + TS + Tailwind (Vite)
│   ├── src/
│   │   ├── components/  # Layout, MovieCard, SeatMap, ProtectedRoute
│   │   ├── pages/       # Home, Movies, MovieDetail, ComingSoon, Login,
│   │   │                # Register, Dashboard, BookMovie, Bookings,
│   │   │                # BookingDetail, Payment, Confirmation, admin/*
│   │   ├── context/     # AuthContext (JWT)
│   │   ├── services/    # axios api client
│   │   ├── App.tsx / main.tsx / types.ts / index.css
│   └── public/          # logo.png
├── server/            # Node + Express + TS + SQLite
│   ├── src/
│   │   ├── routes/      # auth, movies, showtimes, bookings, payments, admin
│   │   ├── middleware/  # auth (JWT), admin
│   │   ├── config/      # db (schema), seed
│   │   ├── utils/       # jwt, booking refs
│   │   ├── app.ts / server.ts
│   ├── uploads/posters/ # uploaded movie posters (served statically)
│   └── data/            # vox.db (auto-created + seeded)
├── .env.example
└── package.json         # root orchestration scripts
```

## API overview

All endpoints are JSON under `/api`:

- `POST /auth/register|/login`, `GET /auth/me`
- `GET /movies?status=`, `GET /movies/:id`, `POST /movies/:id/notify`, admin `POST|PUT|DELETE /movies`
- `GET /showtimes?cinema_id=&movie_id=&date=`, `GET /movies/:id/showtimes` (alias `/showtimes/movie/:id`), auth `GET /showtimes/:id/seats` (hall-scoped), admin `POST|PUT|DELETE /showtimes` (hall required)
- `GET /cinemas`, `GET /cinemas/:id` (halls + now playing + showtimes), `GET /cinemas/halls?cinema_id=`, admin `POST|PUT|DELETE /cinemas`, `POST|PUT|DELETE /cinemas/halls`
- Auth `GET|POST /bookings`, `GET /bookings/:id`, `GET /bookings/:id/ticket` (QR payload), `DELETE /bookings/:id/cancel`, `DELETE /bookings/:id`
- Auth `POST /holds`, `GET /holds/:token`, `DELETE /holds/:token` (seat holds with expiry + price quote)
- Auth `GET /payments`, `POST /payments/process`
- Auth `GET /dashboard` (upcoming/history)
- Admin `GET /admin/stats|/users|/settings|/dashboard`, `PATCH /admin/users/:id/toggle-admin`, `PUT|DELETE /admin/users/:id`, `PUT /admin/settings`, `POST /admin/check-in` (ticket validation)
