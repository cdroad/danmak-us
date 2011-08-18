@echo off
SET /p X=?:
for /R .\ %%a IN (*.php) DO (
	findstr /I "%X%" "%%~fa" >nul && ( echo ---------------------------&echo "%%~a" & findstr /I /N "%X%" "%%~fa")
)
PAUSE