# Horde Statistics (Node.js)

Real-time statistics for the Stable Horde network.

## Features
- Performance metrics polling with client-side rolling averages
- Model list with delta highlighting
- Individual model history page
- Lightweight API proxy with caching
- Modular Node.js structure (Express routers + utilities)

## Structure
```
src/
  routes/
    performance.js
    models.js
  utils/
    cache.js
    fetchJson.js
  server.js
public/
  index.html
  models.html
  models_history.html
  style/
    style.css
```

## Scripts
```powershell
npm install       # install dependencies
npm run dev       # start with nodemon
npm start         # start server
npm run lint      # lint
npm run lint:fix  # auto-fix
```

## Environment Variables
- PORT: listening port (default 3000)
- TRUST_PROXY: set any non-empty value when behind reverse proxy
- LOG_FORMAT: morgan format string (default 'dev')

## Health Check
GET /health -> { status: 'ok', time: ISO8601 }

## API
- GET /api/performance
- GET /api/models/:name

## Notes
Client averages are per-page-session; refresh resets history.
