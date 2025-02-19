# itp-docker
# ITP Docker Assignment

## Architecture

This application consists of three services:

1. Front-facing service (fp-svc):
   - Listens on localhost:8081
   - Proxies requests to hp-svc
   - Serves the final project content under /your-github-repo-name

2. Homepage service (hp-svc):
   - Serves the homepage content
   - Internal service (not exposed to host)
   - Listens on port 6969

3. Watchdog service:
   - Provides shell access for testing

Request flow:
1. User visits localhost:8081
2. fp-svc receives request
3. Request is proxied to hp-svc which serves the homepage
4. When user clicks the link, they're taken to /your-github-repo-name
5. fp-svc serves the final project content

## Prerequisites

- Docker
- Docker Compose
- Git

## Setup Instructions

1. Clone this repository:
   ```bash
   git clone https://github.com/cpainter7362/itp-docker.git
   cd itp-docker