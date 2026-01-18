@echo off
REM ================================================
REM  QPOS - Build Assets using bundled Node.js
REM ================================================
echo Building production assets...
echo.
"%~dp0nodejs\node.exe" "%~dp0node_modules\vite\bin\vite.js" build --config vite.config.js
echo.
echo Build complete!
pause
