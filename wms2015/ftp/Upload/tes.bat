@ECHO off
setlocal enabledelayedexpansion
SET WILDCARD=*.txt
SET CTLWILDCARD=*.ctl

for /f "delims=" %%A IN ('DIR /B /ON .\ASN\!CTLWILDCARD!') DO (
	ECHO %%~nA !WILDCARD:~-4!
)
PAUSE
