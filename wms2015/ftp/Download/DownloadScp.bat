@ECHO off
setlocal enabledelayedexpansion
SET HOST=192.168.31.9
SET USR=oracle
SET PSWD=B0rw1t4
SET FLDR=/oracle/lms-online/interface/TRN
SET WILDCARD=*.txt
SET CTLWILDCARD=*.ctl

REM "C:\Program Files\Microsoft SQL Server\90\Tools\Binn\SQLCMD.EXE"  -i Upload.sql -o Upload.log


FOR /F "tokens=*" %%A IN (Folder.txt) DO (
	ECHO Download file yang sudah memiliki %%A\!CTLWILDCARD! di FTP
	echo open ftp://!USR!:!PSWD!@!HOST!> %%A.dat
	echo bin>> %%A.dat
	echo option confirm off>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	echo echo 150 Here comes the directory listing.>> %%A.dat
	ECHO ls !CTLWILDCARD!>> %%A.dat
	echo echo 226 Directory send OK.>> %%A.dat
	echo exit>> %%A.dat
	winscp.com /script=%%A.dat /timeout=-1 >%%A.log
	del /Q %%A.dat

	copy /Y %%A.log File.bat
	cscript ExtractFtpFileNames.vbs File.bat %%A DlOrArch.bat !WILDCARD!

	echo open ftp://!USR!:!PSWD!@!HOST!> %%A.dat
	echo bin>> %%A.dat
	echo option confirm off>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	echo lcd %%A>> %%A.dat

	call File.bat
	del /Q File.bat
	
	echo lcd ..>> %%A.dat
	echo exit>> %%A.dat
	winscp.com /script=%%A.dat /timeout=-1 >%%A.log
	del /Q %%A.dat
	
	ECHO Periksa file %%A\!WILDCARD! apakah ukurannya sama dengan yang ada di FTP
	echo open ftp://!USR!:!PSWD!@!HOST!> %%A.dat
	echo bin>> %%A.dat
	echo option confirm off>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	echo echo 150 Here comes the directory listing.>> %%A.dat
	call :ListTxt %%A
	echo echo 226 Directory send OK.>> %%A.dat
	echo exit>> %%A.dat
	winscp.com /script=%%A.dat /timeout=-1 >%%A.log
	del /Q %%A.dat

	copy /Y %%A.log File.bat
	cscript ExtractFtpFileNames.vbs File.bat %%A DelSizeEqu.bat !CTLWILDCARD!

	call File.bat
	del /Q File.bat
	
	ECHO Proses %%A\!WILDCARD! yang memiliki %%A\!CTLWILDCARD! dan 
	ECHO memindahkan keduanya ke %%A\Archive bila berhasil 

	call :ListCtl %%A
	
)
goto :eof
:ListTxt
for /f "delims=" %%A IN ('DIR /B /ON .\%1\!WILDCARD!') DO (
	ECHO ls *%%A>> %1.dat
)
goto :eof
:ListCtl
for /f "delims=" %%A IN ('DIR /B /ON .\%1\!CTLWILDCARD!') DO (
	ECHO !WILDCARD!: %%~nA!WILDCARD:~-4!
	ECHO !CTLWILDCARD!: %%~nxA
)
goto :eof
