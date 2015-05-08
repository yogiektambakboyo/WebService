@ECHO off
setlocal enabledelayedexpansion
SET HOST=192.168.31.9
SET USR=oracle
SET PSWD=B0rw1t4
SET FLDR=/oracle/lms-online/interface/TRN
SET WILDCARD=*.txt
SET CTLWILDCARD=*.ctl

REM "C:\Program Files\Microsoft SQL Server\90\Tools\Binn\SQLCMD.EXE"  -i Upload.sql -o Upload.log

ECHO Upload semua !WILDCARD!
echo open ftp://!USR!:!PSWD!@!HOST!> ftpcmd.dat
echo bin>> ftpcmd.dat
echo option confirm off>> ftpcmd.dat
FOR /F "delims=" %%A IN (Folder.txt) DO (
	echo cd !FLDR!/%%A>> ftpcmd.dat
	echo lcd %%A>> ftpcmd.dat
	echo mput !WILDCARD!>> ftpcmd.dat
	echo lcd ..>> ftpcmd.dat
)
echo exit>> ftpcmd.dat
winscp.com /script=ftpcmd.dat /timeout=-1 >FtpUploadScp.log

del /Q ftpcmd.dat

FOR /F "tokens=*" %%A IN (Folder.txt) DO (
	ECHO Hapus file %%A\!WILDCARD! yang sama ukurannya dengan yang ada di FTP 
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
	cscript ExtractFtpFileNames.vbs File.bat %%A

	call File.bat

	ECHO Upload file %%A\!CTLWILDCARD! yang %%A\!WILDCARD! nya sudah masuk ke Archive	
	echo open ftp://!USR!:!PSWD!@!HOST!> %%A.dat
	echo bin>> %%A.dat
	echo option confirm off>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	echo lcd %%A>> %%A.dat

	call :CtlUpload %%A
	
	echo lcd ..>> %%A.dat
	echo exit>> %%A.dat
	
	winscp.com /script=%%A.dat /timeout=-1 >%%ACtl.log
	del /Q %%A.dat
	
	ECHO Hapus file %%A\!CTLWILDCARD! yang sudah ada di FTP
	echo open ftp://!USR!:!PSWD!@!HOST!> %%A.dat
	echo bin>> %%A.dat
	echo option confirm off>> %%A.dat
	echo cd !FLDR!/%%A>> %%A.dat
	echo echo 150 Here comes the directory listing.>> %%A.dat
	call :ListCtl %%A
	echo echo 226 Directory send OK.>> %%A.dat
	echo exit>> %%A.dat
	winscp.com /script=%%A.dat /timeout=-1 >%%A.log
	del /Q %%A.dat

	copy /Y %%A.log File.bat
	cscript ExtractFtpFileNames.vbs File.bat %%A

	call File.bat
	del /Q .\%%A\Archive\!CTLWILDCARD!
)
goto :eof
:ListTxt
for /f "delims=" %%A IN ('DIR /B /ON .\%1\!WILDCARD!') DO (
	ECHO ls *%%A>> %1.dat
)
:break
goto :eof
:CtlUpload
for /f "delims=" %%A IN ('DIR /B /ON .\%1\!CTLWILDCARD!') DO (
	IF EXIST .\%1\Archive\%%~nA!WILDCARD:~-4! (
		echo put %%~nxA>> %1.dat
	)
)
:break
goto :eof
:ListCtl
for /f "delims=" %%A IN ('DIR /B /ON .\%1\!CTLWILDCARD!') DO (
	ECHO ls *%%A>> %1.dat
)
:break
goto :eof
