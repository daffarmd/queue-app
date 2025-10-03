@echo off
echo Starting Laravel Application Servers...
echo.

REM Start Laravel web server
echo [1/3] Starting Laravel web server...
start "Laravel Web Server" /B php artisan serve > laravel-web.log 2>&1
timeout /t 2 /nobreak >nul

REM Start Laravel Reverb WebSocket
echo [2/3] Starting Laravel Reverb WebSocket...
start "Laravel Reverb" /B php artisan reverb:start > laravel-reverb.log 2>&1
timeout /t 2 /nobreak >nul

REM Start TTS web server
echo [3/3] Starting TTS web server...
start "TTS Server" /B python -m piper.http_server -m id_ID-news_tts-medium > tts-server.log 2>&1
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo All servers started successfully!
echo ========================================
echo.
echo Logs are being written to:
echo   - laravel-web.log
echo   - laravel-reverb.log
echo   - tts-server.log
echo.
echo To stop all servers, run: stop-servers.bat
echo.
pause
