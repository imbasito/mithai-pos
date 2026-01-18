@echo off
echo  Clearing POS App Cache...
echo.

set "CACHE_DIR=%APPDATA%\pos"

if exist "%CACHE_DIR%" (
    echo  Removing cache files in %CACHE_DIR%...
    rmdir /s /q "%CACHE_DIR%\Cache" 2>nul
    rmdir /s /q "%CACHE_DIR%\Code Cache" 2>nul
    rmdir /s /q "%CACHE_DIR%\GPUCache" 2>nul
    echo  Cache cleared.
) else (
    echo  Cache directory not found (maybe first run).
)

echo.
echo  Please restart the application now.
pause
