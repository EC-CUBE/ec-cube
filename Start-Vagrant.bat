ECHO OFF
cd /d %~dp0
for /f "tokens=2* delims= " %%F IN ('vagrant status ^| find /I "default"') DO (SET "STATE=%%F%%G")

ECHO Close this window if it remains open, and http://localhost:8888 is responsive

IF "%STATE%" NEQ "saved" (
  ECHO Starting Vagrant VM from powered down state...
  vagrant up
) ELSE (
  ECHO Resuming Vagrant VM from saved state...
  vagrant resume
)

if errorlevel 1 (
  ECHO FAILURE! Vagrant VM unresponsive...
)
