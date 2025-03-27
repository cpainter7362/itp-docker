#!/bin/bash

# Clean up any existing volumes directory
rm -rf volumes
# Or use git clean as shown in the video
# git clean -fdx

# Copy template files to volumes
mkdir -p volumes
cp -r volume_templates/* volumes/

# Generate secure random passwords
PASSWORD_STARTER=$(docker run --rm alpine/openssl:3.3.3 rand -base64 64)
PASSWORD_ROOT=${PASSWORD_STARTER:0:32}
PASSWORD_USER=${PASSWORD_STARTER:32:32}

# Create MySQL environment file
echo "MYSQL_ROOT_PASSWORD=\"$PASSWORD_ROOT\"" > volumes/mysql/.env

# Create shared PHP and MySQL environment file
echo "MYSQL_DATABASE=\"php_app\"" > volumes/php_mysql.env
echo "MYSQL_USER=\"php_agent\"" >> volumes/php_mysql.env  
echo "MYSQL_PASSWORD=\"$PASSWORD_USER\"" >> volumes/php_mysql.env
echo "MYSQL_TCP_PORT=3311" >> volumes/php_mysql.env
echo "MYSQL_HOST=db_svc" >> volumes/php_mysql.env
echo "TZ=America/New_York" >> volumes/php_mysql.env

echo "Initialization complete. Environment files created with secure passwords."