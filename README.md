# ITP Docker Project

## Project Architecture

### Request Flow
When a request arrives at `localhost:8081`:
1. The request is received by `fp-svc` (Final Project Service)
2. For root path ('/'):
   - Request is proxied to `hp-svc` on port 6969
   - Returns homepage with navigation link
3. For '/itp-docker' path:
   - `fp-svc` serves content directly from final project repository

### Service Connections
- `fp-svc`: Nginx container exposing port 8081
- `hp-svc`: Internal Nginx container on port 6969
- Services communicate via Docker internal networking
- `watchdog-svc`: Alpine container for debugging

### HTML Sources
- Homepage (`hp-svc`):
  - Source: `volumes/home-page/home/html`
  - Served on port 6969
- Project Page (`fp-svc`):
  - Source: Final project repository
  - Served at `/itp-docker` path

## Setup Instructions

### Prerequisites
1. Docker Desktop
   - Required for container management
   - Install from docker.com/products/docker-desktop
   - Must be running before starting

2. Git
   - Required for repository management
   - Install from git-scm.com/downloads

### Project Setup

1. Repository Setup
   ```bash
   git clone https://github.com/cpainter7362/itp-docker.git
   cd itp-docker
   ```

2. Initialize Project Structure
   ```bash
   ./scripts/init.sh
   ```
   This creates:
   - Required directory structure
   - Nginx configurations
   - HTML templates

3. Start Services
   ```bash
   docker compose up -d
   ```

4. Verify Installation
   - Browse to http://localhost:8081
   - Should see homepage with project link
   - Click link to access project page

### Common Operations

Start Services:
```bash
docker compose up -d
```

Stop Services:
```bash
docker compose down
```

View Logs:
```bash
docker compose logs
```

Reset Environment:
```bash
docker compose down
rm -rf volumes/
./scripts/init.sh
docker compose up -d
```

## Testing Instructions

1. Fresh Installation Test
   ```bash
   git clone https://github.com/cpainter7362/itp-docker.git
   cd itp-docker
   ./scripts/init.sh
   docker compose up -d
   ```

2. Verification Steps
   - Navigate to http://localhost:8081
   - Verify homepage loads
   - Click project link
   - Verify project page loads

## Troubleshooting

Service Status:
```bash
docker compose ps
```

Container Logs:
```bash
docker compose logs fp-svc
docker compose logs hp-svc
```

Network Testing:
```bash
docker compose exec watchdog-svc sh
wget -qO- http://hp-svc:6969
wget -qO- http://fp-svc:7901
```

**Note:** Following these instructions exactly should result in a fully functional application. Any deviation may result in setup failure.