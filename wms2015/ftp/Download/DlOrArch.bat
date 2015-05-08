@echo off
setlocal enabledelayedexpansion
set FILE=%1
set BYTESIZE=%2
set FOLDER=%3
set WILDCARD=%4

IF EXIST .\!FOLDER!\Archive\%~n1!WILDCARD:~-4! (
	echo mv %~n1!WILDCARD:~-4! ./Archive/>> !FOLDER!%.dat
	echo mv !FILE! ./Archive/>> !FOLDER!%.dat
) ELSE (
	echo get %~n1!WILDCARD:~-4!>> !FOLDER!%.dat
	echo get !FILE!>> !FOLDER!%.dat
)
