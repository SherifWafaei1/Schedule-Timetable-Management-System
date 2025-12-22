#!/bin/sh
set -e

# If Render (or environment) sets PORT, replace Apache ports to bind to that port.
if [ -n "$PORT" ]; then
  sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf || true
  sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true
fi

exec apache2-foreground
