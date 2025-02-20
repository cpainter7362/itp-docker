#!/bin/bash

# Clean up any existing temp container first
docker rm -f temp-nginx 2>/dev/null || true

# Create the folder structure
mkdir -p volumes/final-project/config/conf.d
mkdir -p volumes/home-page/config/conf.d
mkdir -p volumes/home-page/home/html

# Create and start a temporary nginx container
docker run --name temp-nginx -d nginx:latest

# Create nginx.conf for final-project
cat > volumes/final-project/config/nginx.conf << 'EOF'
user  nginx;
worker_processes  auto;

error_log  /var/log/nginx/error.log notice;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;

    sendfile        on;
    keepalive_timeout  65;

    include /etc/nginx/conf.d/*.conf;
}
EOF

# Copy nginx.conf to home-page
cp volumes/final-project/config/nginx.conf volumes/home-page/config/nginx.conf

# Copy conf.d files from container
docker cp temp-nginx:/etc/nginx/conf.d/. volumes/final-project/config/conf.d/
docker cp temp-nginx:/etc/nginx/conf.d/. volumes/home-page/config/conf.d/
docker cp temp-nginx:/usr/share/nginx/html/. volumes/home-page/home/html/

# Stop and remove the temporary container
docker stop temp-nginx
docker rm temp-nginx

# Create default.conf for final-project
cat > volumes/final-project/config/conf.d/default.conf << 'EOF'
server {
    listen 7901;
    listen [::]:7901;
    server_name  localhost;

    location / {
        proxy_pass http://hp-svc:6969;
    }

    location /itp-docker {
        alias   /usr/share/nginx/html;
        index  index.html index.htm;
    }

    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }
}
EOF

# Create default.conf for home-page
cat > volumes/home-page/config/conf.d/default.conf << 'EOF'
server {
    listen 6969;
    listen [::]:6969;
    server_name  localhost;

    location / {
        root   /usr/share/nginx/html;
        index  index.html index.htm;
    }

    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }
}
EOF

# Create index.html
cat > volumes/home-page/home/html/index.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Home</h1>
    <p>Please visit the <a href="/itp-docker/">itp-docker</a> page.</p>
</body>
</html>
EOF