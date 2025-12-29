# Horde Statistics

> Real-time statistics dashboard for the [Stable Horde](https://stablehorde.net/) network - a crowdsourced distributed cluster for Stable Diffusion image generation.

[![Node.js Version](https://img.shields.io/badge/node-%3E%3D18-brightgreen)](https://nodejs.org/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## 📖 About

**Horde Statistics** is a lightweight web application that provides real-time monitoring and analytics for the Stable Horde network. It offers live performance metrics, model availability tracking, and historical data visualization with client-side rolling averages.

### What is Stable Horde?

[Stable Horde](https://stablehorde.net/) is a crowdsourced distributed cluster of Stable Diffusion workers. This dashboard helps users and contributors monitor the network's health, performance, and available AI models in real-time.

## ✨ Features

- **📊 Real-time Performance Metrics** - Live polling of network statistics with automatic updates
- **📈 Client-side Rolling Averages** - Calculate 1-minute, 1-hour, and 24-hour averages without server overhead
- **🤖 Model Tracking** - Browse available models with delta highlighting for changes
- **📜 Model History** - View historical performance data for individual models
- **⚡ Lightweight API Proxy** - Built-in caching layer to reduce upstream API load
- **🏗️ Modular Architecture** - Clean separation of concerns with Express routers and utility modules
- **🔒 Security Headers** - Helmet.js integration for enhanced security
- **📦 Response Compression** - Automatic gzip compression for faster responses

## 🚀 Quick Start

### Prerequisites

- [Node.js](https://nodejs.org/) (version 18 or higher)
- npm (comes with Node.js)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/gabriel20xx/HordeStatistics.git
   cd HordeStatistics
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start the development server**
   ```bash
   npm run dev
   ```

4. **Open your browser**
   
   Navigate to `http://localhost:3000`

## 📋 Available Scripts

| Command | Description |
|---------|-------------|
| `npm start` | Start the production server |
| `npm run dev` | Start development server with auto-reload (nodemon) |
| `npm run lint` | Run ESLint to check code style |
| `npm run lint:fix` | Automatically fix ESLint issues |
| `npm test` | Run tests (not yet implemented) |

## ⚙️ Configuration

### Environment Variables

Configure the application using environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `PORT` | Server listening port | `3000` |
| `TRUST_PROXY` | Enable when behind a reverse proxy (nginx, Apache) | `false` |
| `LOG_FORMAT` | Morgan logging format (`dev`, `combined`, `common`, `short`, `tiny`) | `dev` |

**Example:**

```bash
PORT=8080 TRUST_PROXY=1 LOG_FORMAT=combined npm start
```

Or create a `.env` file (you'll need to add dotenv package):

```env
PORT=8080
TRUST_PROXY=1
LOG_FORMAT=combined
```

## 🏗️ Project Structure

```
HordeStatistics/
├── src/
│   ├── routes/
│   │   ├── performance.js    # Performance metrics API endpoint
│   │   └── models.js          # Model data API endpoint
│   ├── utils/
│   │   ├── cache.js           # Simple in-memory cache utility
│   │   └── fetchJson.js       # HTTP client with timeout/abort support
│   └── server.js              # Main Express application
├── public/
│   ├── index.html             # Main dashboard page
│   └── style/
│       └── style.css          # Application styles
├── package.json
└── README.md
```

## 🔌 API Endpoints

### Health Check

Check if the server is running:

```http
GET /health
```

**Response:**
```json
{
  "status": "ok",
  "time": "2025-12-29T16:00:00.000Z"
}
```

### Performance Metrics

Get current network performance statistics:

```http
GET /api/performance
```

**Response:** Proxied from `https://stablehorde.net/api/v2/status/performance`

**Cache TTL:** 1 second

**Example Response:**
```json
{
  "queued_requests": 42,
  "queued_megapixelsteps": 1234.56,
  "past_minute_megapixelsteps": 789.12,
  "worker_count": 150
}
```

### Model Information

Get historical data for a specific model:

```http
GET /api/models/:name
```

**Parameters:**
- `name` (path) - URL-encoded model name

**Response:** Proxied from `https://stablehorde.net/api/v2/status/models/:name`

**Cache TTL:** 1.5 seconds

**Example:**
```bash
curl http://localhost:3000/api/models/stable_diffusion
```

## 🚢 Deployment

### Using Node.js

```bash
# Install dependencies
npm install --production

# Start the server
NODE_ENV=production PORT=3000 npm start
```

### Using PM2

```bash
# Install PM2 globally
npm install -g pm2

# Start with PM2
pm2 start src/server.js --name horde-statistics

# View logs
pm2 logs horde-statistics

# Enable startup on boot
pm2 startup
pm2 save
```

### Using Docker

Create a `Dockerfile`:

```dockerfile
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --production
COPY . .
EXPOSE 3000
CMD ["node", "src/server.js"]
```

Build and run:

```bash
docker build -t horde-statistics .
docker run -p 3000:3000 horde-statistics
```

### Reverse Proxy Configuration

#### Nginx

```nginx
server {
    listen 80;
    server_name stats.example.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

**Important:** Set `TRUST_PROXY=1` when running behind a reverse proxy.

## 🔧 Troubleshooting

### Port already in use

```bash
# Find process using port 3000
lsof -i :3000  # macOS/Linux
netstat -ano | findstr :3000  # Windows

# Kill the process or use a different port
PORT=3001 npm start
```

### Dependencies not installing

```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### API returns 502 errors

- Check if `https://stablehorde.net` is accessible
- Verify network connectivity
- Check if you're being rate-limited

## 📝 Notes

- **Client-side averages** are calculated per browser session - refreshing the page resets the history
- **Cache** is in-memory only - restarting the server clears all cached data
- **Static files** are served with a 1-hour cache header for better performance
- The application uses **ES modules** (type: "module" in package.json)

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Commit your changes** (`git commit -m 'Add amazing feature'`)
4. **Push to the branch** (`git push origin feature/amazing-feature`)
5. **Open a Pull Request**

### Code Style

This project uses [ESLint](https://eslint.org/) with the [Standard](https://standardjs.com/) configuration. Please ensure your code passes linting:

```bash
npm run lint
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Related Links

- [Stable Horde Website](https://stablehorde.net/)
- [Stable Horde API Documentation](https://stablehorde.net/api/)
- [Stable Horde GitHub](https://github.com/Haidra-Org/AI-Horde)

## 👤 Author

**gabriel20xx**

- GitHub: [@gabriel20xx](https://github.com/gabriel20xx)

---

⭐ If you find this project useful, please consider giving it a star!
