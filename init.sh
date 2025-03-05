#!/bin/bash

# Stop any running containers
docker-compose down

# Create necessary directories
mkdir -p volumes/config/conf.d
mkdir -p volumes/html

# Remove existing content
rm -rf volumes/config/conf.d/*
rm -rf volumes/html/*

# Copy configuration files
cp templates/default.conf volumes/config/conf.d/default.conf

# Copy HTML file
cp templates/home.html volumes/html/index.html

# Remove phpinfo directory if it exists
rm -rf volumes/html/phpinfo

# Create phpinfo directory and copy index.php
mkdir -p volumes/html/phpinfo
cp templates/phpinfo/index.php volumes/html/phpinfo/index.php

echo "Initialization complete. Run 'docker-compose up -d' to start services."