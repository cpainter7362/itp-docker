ITP Docker Project with MySQL Integration
Project Architecture
Request Flow
When a request arrives at localhost:8080:

The request is received by hp-svc (Home Page Service)
For PHP processing:

Request is passed to php_svc container
PHP scripts connect to db_svc for data access


For '/todos' path:

MySQL data is retrieved through PHP PDO
Todo items are displayed from database



Service Connections

hp-svc: Nginx container exposing port 8080
php_svc: PHP-FPM container with MySQL support
db_svc: MySQL database container on port 3311
watchdog-svc: Alpine container for debugging
Services communicate via Docker internal networking

Data Sources

Database (db_svc):

Source: MySQL 8.0.41 container
Stores todo items in todos table
Accessed through PHP PDO


Web Content (hp-svc):

Source: volumes/home-page/home/html
Contains PHP scripts for database interaction



Setup Instructions
Prerequisites

Docker Desktop

Required for container management
Install from docker.com/products/docker-desktop
Must be running before starting


Git

Required for repository management
Install from git-scm.com/downloads



Project Setup

Repository Setup
bashCopygit clone https://github.com/cpainter7362/itp-docker.git
cd itp-docker
git checkout mysql

Initialize Project Structure
bashCopy./init.sh
This creates:

Required directory structure
Nginx configurations
MySQL initialization scripts
Secure environment variables


Start Services
bashCopydocker-compose up -d

Verify Installation

Browse to http://localhost:8080
Should see homepage with navigation links
Visit /todos to see database content
Visit /todos/mysql to test database connection



Common Operations
Start Services:
bashCopydocker-compose up -d
Stop Services:
bashCopydocker-compose down
View Logs:
bashCopydocker-compose logs
Reset Environment:
bashCopydocker-compose down
rm -rf volumes/
./init.sh
docker-compose up -d
Testing Instructions

Fresh Installation Test
bashCopygit clone https://github.com/cpainter7362/itp-docker.git
cd itp-docker
git checkout mysql
./init.sh
docker-compose up -d

Verification Steps

Navigate to http://localhost:8080
Verify homepage loads
Click "Todo App" link
Verify database items display
Click "MySQL Connection Test" link
Verify successful connection details



MySQL Integration
Database Configuration

Container: MySQL 8.0.41
Port: 3311 (externally accessible)
Security: Auto-generated passwords
Database: php_app
Default Table: todos

PHP Connectivity

PDO MySQL extension installed
Connection parameters via environment variables
Error handling with proper exception management

Data Persistence

Volume mapping for database files
SQL initialization scripts for structure
Sample data loaded at first startup

Troubleshooting
Service Status:
bashCopydocker-compose ps
Container Logs:
bashCopydocker-compose logs db_svc
docker-compose logs php_svc
docker-compose logs hp-svc
Database Connection:
bashCopydocker exec -it mysql-container mysql -u php_agent -p
# Password in volumes/php_mysql.env
Network Testing:
bashCopydocker-compose exec watchdog-svc sh
wget -qO- http://hp-svc:80
wget -qO- http://db_svc:3306
Note: Following these instructions exactly should result in a fully functional application with MySQL integration. Any deviation may result in setup failure.