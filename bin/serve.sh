#!/usr/bin/env bash
# Find an available port starting from 9000 and serve
PORT=9000
while lsof -i :$PORT >/dev/null 2>&1; do
    PORT=$((PORT + 1))
    if [ $PORT -gt 9100 ]; then
        echo "No available ports found between 9000-9100"
        exit 1
    fi
done
echo "Starting testbench server on port $PORT..."
echo "URL: http://127.0.0.1:$PORT"
php vendor/bin/testbench serve --port=$PORT
