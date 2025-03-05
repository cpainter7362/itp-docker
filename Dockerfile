# Use the official Nginx image as the base
FROM nginx:latest

# Copy only the built/bundled files from the docs directory
COPY --from=final-project /docs/ /usr/share/nginx/html/
