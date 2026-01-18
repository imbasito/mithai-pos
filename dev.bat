@echo off
REM ================================================
REM  QPOS - Development Server using bundled Node.js
REM ================================================
echo Starting Vite development server...
echo.
"%~dp0nodejs\node.exe" "%~dp0node_modules\vite\bin\vite.js" --config vite.config.js
