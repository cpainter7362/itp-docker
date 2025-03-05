# PHP with NGINX Docker Deployment

## Project Architecture

### Request Flow
When a request arrives at `localhost:8089`:
1. The request is received by the `http-svc` (NGINX Service)
2. For static content (HTML, CSS, images):
   - NGINX serves these files directly from the shared volume
3. For PHP files:
   - NGINX passes the request to `php-svc` via FastCGI
   - PHP-FPM processes the script and returns the result
   - NGINX sends the processed result back to the client

### Service Connections
- `http-svc`: NGINX container exposing port 8089
- `php-svc`: PHP-FPM container running on internal port 9000
- Services communicate via Docker internal networking
- Both services share a common volume for web content

### Content Sources
- Static content:
  - Source: `volumes/html`
  - Served directly by NGINX
- PHP scripts:
  - Source: `volumes/html` (same volume)
  - Processed by PHP-FPM via FastCGI
  - Example: `/phpinfo/` path serves PHP info page

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
   git checkout -b php
   ```

2. Initialize Project Structure
   ```bash
   ./init.sh
   ```
   This creates:
   - Required directory structure in `volumes/`
   - NGINX configuration in `volumes/config/conf.d/`
   - HTML homepage in `volumes/html/`
   - PHP info script in `volumes/html/phpinfo/`

3. Start Services
   ```bash
   docker-compose up -d
   ```

4. Verify Installation
   - Browse to http://localhost:8089
   - Should see NGINX welcome page with PHP demo link
   - Click link to access PHP info page with purple styling

### Common Operations

Start Services:
```bash
docker-compose up -d
```

Stop Services:
```bash
docker-compose down
```

View Logs:
```bash
docker-compose logs
```

Reset Environment:
```bash
docker-compose down
rm -rf volumes/
./init.sh
docker-compose up -d
```

## Technical Details

### NGINX Configuration
The NGINX server is configured with:
- Port 80 exposed internally (mapped to 8089 on host)
- Default document root at `/usr/share/nginx/html`
- Support for index files in order: index.html, index.htm, index.php
- PHP handler for all `.php` files

### PHP Configuration
The PHP service:
- Uses lightweight Alpine-based PHP-FPM image
- Runs FastCGI server on port 9000
- Shares the same document root as NGINX
- Processes all PHP files found by NGINX

### Volume Mapping
Both services mount the following volumes:
- `./volumes/config/conf.d` → `/etc/nginx/conf.d`
- `./volumes/html` → `/usr/share/nginx/html`

### Important Notes
- When accessing the PHP info page, make sure to include the trailing slash: `/phpinfo/`
- Without the trailing slash, NGINX will try to find a file named "phpinfo" instead of the index.php inside the directory
- The link on the homepage has been configured to use the correct path with the trailing slash

## File Structure

```
.
├── docker-compose.yml      # Docker Compose configuration
├── init.sh                 # Initialization script
├── templates/              # Configuration templates
│   ├── default.conf        # NGINX server configuration
│   ├── home.html           # Homepage template
│   └── phpinfo/            # PHP demo directory
│       └── index.php       # PHP info script
└── volumes/                # Mounted volumes (created by init.sh)
    ├── config/             # NGINX configuration
    │   └── conf.d/         # Server config files
    └── html/               # Web content
        ├── index.html      # Homepage
        └── phpinfo/        # PHP demo directory
            └── index.php   # PHP info script
```

## Testing Instructions

1. Fresh Installation Test
   ```bash
   git clone https://github.com/cpainter7362/itp-docker.git
   cd itp-docker
   git checkout php
   ./init.sh
   docker-compose up -d
   ```

2. Verification Steps
   - Navigate to http://localhost:8089
   - Verify NGINX welcome page loads
   - Click the "PHP info demo page" link
   - Verify PHP info page loads with purple styling

## Troubleshooting

### Common Issues

1. **PHP Page Not Found**: 
   - Ensure you're using the trailing slash when accessing PHP directories
   - Example: http://localhost:8089/phpinfo/

2. **Configuration Issues**:
   - Check that your default.conf contains the correct fastcgi_pass directive
   - Ensure both containers are on the same Docker network

3. **Volume Mounting Issues**:
   - Make sure the volumes exist and have the correct permissions
   - Run the init.sh script to recreate the volumes if needed

### Diagnostic Commands

Service Status:
```bash
docker-compose ps
```

Container Logs:
```bash
docker-compose logs http-svc
docker-compose logs php-svc
```

NGINX Configuration Test:
```bash
docker-compose exec http-svc nginx -t
```

PHP Connection Test:
```bash
docker-compose exec http-svc curl php-svc:9000
```

File Permissions:
```bash
ls -la volumes/html/phpinfo/
```

### Advanced Debugging

If you need to inspect the containers in more detail:

NGINX Shell:
```bash
docker-compose exec http-svc sh
```

PHP Shell:
```bash
docker-compose exec php-svc sh
```

**Note:** If you encounter issues with PHP execution, ensure the NGINX configuration correctly passes requests to the PHP service using the service name (php-svc) on port 9000.