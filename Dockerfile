# Use the official Nginx image as the base
FROM nginx:latest

# Copy the built site from your final project repository into Nginx’s default web directory
COPY --from=final-project . /usr/share/nginx/html/
