#!/bin/bash

# Docker Setup Script for Social Network
echo "🐳 Setting up Social Network with Docker..."

# Create uploads directories
mkdir -p uploads/profiles
mkdir -p uploads/posts

# Set permissions
chmod -R 777 uploads/

# Copy Docker database config
cp classes/Database.docker.php classes/Database.php

echo "✅ Setup complete!"
echo ""
echo "To start the application:"
echo "  docker-compose up -d"
echo ""
echo "To stop the application:"
echo "  docker-compose down"
echo ""
echo "Access the application at: http://localhost:8080"
echo "Access phpMyAdmin at: http://localhost:8081"
