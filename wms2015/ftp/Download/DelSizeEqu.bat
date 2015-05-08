@echo off
setlocal enabledelayedexpansion
set FILE=%1
set FTPSIZE=%2
set FOLDER=%3
set WILDCARD=%4

FOR /F "usebackq" %%A IN ('.\!FOLDER!\!FILE!') DO set LCLSIZE=%%~zA

if !LCLSIZE! NEQ !FTPSIZE! (
	del /Q .\!FOLDER!\!FILE!
	del /Q .\!FOLDER!\%~n1!WILDCARD:~-4!
)
