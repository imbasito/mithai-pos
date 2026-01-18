@echo off
REM ================================================
REM  Node.js Portable Setup Script for QPOS
REM  Downloads and extracts portable Node.js
REM ================================================

echo.
echo ========================================
echo   QPOS - Node.js Portable Setup
echo ========================================
echo.

REM Check if nodejs folder already exists
if exist "nodejs\node.exe" (
    echo [INFO] Node.js is already installed in the nodejs folder.
    echo [INFO] Delete the nodejs folder if you want to reinstall.
    pause
    exit /b 0
)

REM Create nodejs directory
if not exist "nodejs" mkdir nodejs

echo [INFO] Downloading Node.js v20.11.0 (LTS) portable...
echo.

REM Download Node.js using PowerShell
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://nodejs.org/dist/v20.11.0/node-v20.11.0-win-x64.zip' -OutFile 'nodejs-temp.zip'}"

if %ERRORLEVEL% neq 0 (
    echo [ERROR] Failed to download Node.js. Check your internet connection.
    pause
    exit /b 1
)

echo [INFO] Extracting Node.js...

REM Extract using PowerShell
powershell -Command "Expand-Archive -Path 'nodejs-temp.zip' -DestinationPath 'nodejs-extract' -Force"

REM Move contents from nested folder to nodejs/
xcopy /E /Y "nodejs-extract\node-v20.11.0-win-x64\*" "nodejs\"

REM Cleanup
rmdir /S /Q "nodejs-extract"
del "nodejs-temp.zip"

echo.
echo [SUCCESS] Node.js has been installed to the nodejs folder!
echo [INFO] You can now build the Electron app with bundled Node.js.
echo.

REM Verify installation
echo [INFO] Verifying installation...
nodejs\node.exe --version

pause
