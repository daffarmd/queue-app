@echo off
echo Stopping Laravel Application Servers...
echo.

REM Kill PHP processes (Laravel web server and Reverb)
echo [1/2] Stopping Laravel web server and Reverb...
taskkill /F /IM php.exe >nul 2>&1
if %errorlevel% equ 0 (
    echo     PHP processes stopped successfully
) else (
    echo     No PHP processes found running
)

REM Kill Python processes (TTS server)
echo [2/2] Stopping TTS web server...
taskkill /F /IM python.exe >nul 2>&1
if %errorlevel% equ 0 (
    echo     Python processes stopped successfully
) else (
    echo     No Python processes found running
)

echo.
echo ========================================
echo All servers stopped!
echo ========================================
echo.
pause
