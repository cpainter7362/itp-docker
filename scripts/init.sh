#!/bin/bash

# Create the folder structure
mkdir -p volumes/final-project/config/conf.d
mkdir -p volumes/home-page/config/conf.d
mkdir -p volumes/home-page/home/html

# Create a temporary nginx container and copy files
docker rm -f temp-nginx 2>/dev/null || true
docker run --name temp-nginx nginx:latest
docker cp temp-nginx:/etc/nginx/nginx.conf volumes/final-project/config/
docker cp temp-nginx:/etc/nginx/conf.d/default.conf volumes/final-project/config/conf.d/
docker cp temp-nginx:/etc/nginx/nginx.conf volumes/home-page/config/
docker cp temp-nginx:/etc/nginx/conf.d/default.conf volumes/home-page/config/conf.d/
docker cp temp-nginx:/usr/share/nginx/html/. volumes/home-page/home/html/

# Clean up temporary container
docker rm temp-nginx

# Make the script modify the configuration files
sed -i 's/listen[[:space:]]*80;/listen 7901;/g' volumes/final-project/config/conf.d/default.conf
sed -i 's/listen[[:space:]]*\[::\]:80;/listen [::]:7901;/g' volumes/final-project/config/conf.d/default.conf

# Replace the location block in final-project conf
sed -i '/location \/ {/,/}/c\    location / {\n        proxy_pass http://hp-svc:6969;\n    }\n\n    location /your-github-repo-name {\n        alias   /usr/share/nginx/html;\n        index  index.html index.htm;\n    }' volumes/final-project/config/conf.d/default.conf

# Update homepage ports
sed -i 's/listen[[:space:]]*80;/listen 6969;/g' volumes/home-page/config/conf.d/default.conf
sed -i 's/listen[[:space:]]*\[::\]:80;/listen [::]:6969;/g' volumes/home-page/config/conf.d/default.conf

# Update homepage HTML
cat > volumes/home-page/home/html/index.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Home</h1>
    <p>Please visit the <a href="/your-github-repo-name/">your-github-repo-name</a> page.</p>
</body>
</html>
EOF