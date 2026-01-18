@echo off
REM ================================================
REM  QPOS - Start Electron App
REM ================================================
echo Starting QPOS Desktop Application...
echo.
"%~dp0nodejs\node.exe" "%~dp0node_modules\electron\cli.js" .
