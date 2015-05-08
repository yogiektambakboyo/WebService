@echo off
setlocal
set file=%1
set bytesize=%2
set folder=%3

FOR /F "usebackq" %%A IN ('.\%folder%\%file%') DO set size=%%~zA

if %size% EQU %bytesize% (
	move /Y .\%folder%\%file% .\%folder%\Archive\
)
