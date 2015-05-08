@ECHO off
setlocal enabledelayedexpansion
SET HOST=192.168.31.9
SET USR=oracle
SET PSWD=B0rw1t4
SET FLDR=/oracle/lms-online/interface/TRN
SET WILDCARD=*.csv

"C:\Program Files\Microsoft SQL Server\90\Tools\Binn\SQLCMD.EXE"  -i Upload.sql -o Upload.log

REM Upload semua file CSV


echo user !USR!> ftpcmd.dat
echo !PSWD!>> ftpcmd.dat
echo bin>> ftpcmd.dat
FOR /F "delims=" %%A IN (Folder.txt) DO (
	echo cd !FLDR!/%%A>> ftpcmd.dat
	echo lcd %%A>> ftpcmd.dat
	echo mput !WILDCARD!>> ftpcmd.dat
	echo lcd ..>> ftpcmd.dat
)
echo quit>> ftpcmd.dat
echo on
ftp -i -n -s:ftpcmd.dat !HOST!> FtpLog.log
PAUSE
del /Q ftpcmd.dat

REM Hapus file CSV yang sama ukurannya dengan file yang ada di FTP 
FOR /F "tokens=*" %%A IN (Folder.txt) DO (
	echo user !USR!> %%A.dat
	echo !PSWD!>> %%A.dat
	echo bin>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	for /f "delims=" %%A IN ('DIR /B /ON .\%%A\!WILDCARD!') DO (
		@ECHO dir %%A>> %%A.dat
	)
	echo quit>> %%A.dat
	ftp -i -n -s:%%A.dat !HOST!> FtpDir.log
	PAUSE
	del /Q %%A.dat
	copy /Y FtpDir.log File.bat
	cscript ExtractFtpFileNames.vbs File.bat %%A

	call File.bat
)
