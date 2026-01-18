@echo off
REM ================================================
REM  QPOS - Install Dependencies using bundled Node.js
REM ================================================
echo Installing npm dependencies...
echo.
"%~dp0nodejs\npm.cmd" install
echo.
echo Dependencies installed!
pause
