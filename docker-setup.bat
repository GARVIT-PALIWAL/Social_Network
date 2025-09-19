@echo off
echo 🐳 Setting up Social Network with Docker...

REM Create uploads directories
if not exist "uploads\profiles" mkdir uploads\profiles
if not exist "uploads\posts" mkdir uploads\posts

REM Copy Docker database config
copy "classes\Database.docker.php" "classes\Database.php"

echo ✅ Setup complete!
echo.
echo To start the application:
echo   docker-compose up -d
echo.
echo To stop the application:
echo   docker-compose down
echo.
echo Access the application at: http://localhost:8080
echo Access phpMyAdmin at: http://localhost:8081
pause
