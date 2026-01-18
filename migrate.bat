@echo off
REM ================================================
REM  QPOS - Run Database Migrations
REM ================================================
echo Running Laravel database migrations...
echo.
"%~dp0php\php.exe" artisan migrate --force
echo.
echo Migrations complete!
pause
