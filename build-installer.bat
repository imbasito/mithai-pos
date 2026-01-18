@echo off
REM ================================================
REM  QPOS - Build Electron Installer
REM ================================================
echo Building Electron installer...
echo.
"%~dp0nodejs\node.exe" "%~dp0node_modules\electron-builder\cli.js" build --win
echo.
echo Build complete! Check the dist folder.
pause
