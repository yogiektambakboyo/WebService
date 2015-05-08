----- Version: 2.0 - 2009-12-12 - SQL 2000 Compatible
----- Pham Kim Ngan (jbngan@gmail.com)
----- Usage:
-- Copy 7za.exe (http://www.7-RAR.org/download.html - Command Line Version) to @CFG_BACKUP_PATH
-- Modify @CFG_BACKUP_PATH = <Backup Store Path> - no long filename/directory please
-- Modify @CFG_7Z_CMD = <7z Full Path Command> - no long filename/directory please
-- Modify @CFG_DAYS_DELETE = Days to keep backups
-- Enable 'xp_cmdshell' (SQL 2005/EXPRESS or higher)
--exec sp_configure 'show advanced options', 1
--go
--reconfigure
--go
--exec sp_configure 'xp_cmdshell', 1
--go
--reconfigure
--go

----- Configuration Variables
DECLARE @CFG_BACKUP_PATH NVARCHAR(256)
DECLARE @CFG_7Z_CMD NVARCHAR(256)
DECLARE @CFG_DAYS_DELETE INT

SET @CFG_BACKUP_PATH = 'C:\BACKUP'
SET @CFG_7Z_CMD = 'C:\PROGRA~1\Winrar\rar.exe a -ep'
SET @CFG_DAYS_DELETE = 30

DECLARE @Today DATETIME
DECLARE @TodayName CHAR(14)


SET @Today = GETDATE()
SET @TodayName = CONVERT(CHAR(8), @Today, 112) + LEFT(REPLACE(CONVERT(CHAR(12), @Today, 114),':',''),6)

DECLARE @id INT
DECLARE @name VARCHAR(50)
DECLARE @path VARCHAR(256)
DECLARE @cmd VARCHAR(256)

----- Create Temporarity Directory
DECLARE @TempDir VARCHAR(256)
SET @TempDir = @CFG_BACKUP_PATH + '\' + CONVERT(VARCHAR(256), NEWID())
SET @cmd = 'md ' + @TempDir
EXEC xp_cmdshell @cmd, no_output

----- List of current databases, only 'ONLINE' databases to be backup
DECLARE @dbList TABLE
    (
      dbno INT IDENTITY,
      dbname NVARCHAR(256)
    )

INSERT  INTO @dbList ( dbname )
        SELECT  name
        FROM    master.dbo.sysdatabases
		--mdr 28/6
		--ltb 4/7
        WHERE   name IN ('BCP_HQ','BCP_MDR','BCP_LTB','BCP_MDN','BCP_KDR','BCP_MLG','BCP')
		--
                AND DATABASEPROPERTYEX(name, 'Status') = 'ONLINE'
                --AND sid<>0x01
				--AND ( name NOT IN ( 'tempdb','master','model','msdb')) 
                --AND name not like 'Report%'
				--AND name not like '%Temp'


------ Starting backup, one by one
SELECT  @id = dbno,
        @name = dbname
FROM    @dbList
WHERE   dbno = 1
WHILE @@ROWCOUNT = 1
    BEGIN
        PRINT N'++ Backup: ' + @name
        SET @path = @TempDir + '\' + @name + '_log_backup_'+@TodayName+'.trn'
		BACKUP LOG @name TO DISK = @path

        SELECT  @id = dbno,
                @name = dbname
        FROM    @dbList
        WHERE   dbno = @id + 1
    END

PRINT N'++ Compressing: ' + @TempDir

----- Delete output file if existed
SET @cmd = 'del /f /q ' + @CFG_BACKUP_PATH + '\' + @TodayName + 'HQLOG.RAR'
EXEC xp_cmdshell @cmd, no_output

DECLARE @Count INT
DECLARE @StartTime DATETIME
SET @StartTime = GETDATE()
----- Compress, -mx1 = Set Compression Ratio to 1 (very low)
--@CFG_BACKUP_PATH + 

SET @cmd = @CFG_7Z_CMD 
SET @cmd = @cmd +' '+ @CFG_BACKUP_PATH + '\' + @TodayName + 'HQLOG.RAR ' + @TempDir + '\*.trn'
EXEC xp_cmdshell @cmd, no_output

SET @Count = DATEDIFF(second, @StartTime, GETDATE())
PRINT N'++ Compression Time: ' + CONVERT(VARCHAR, @Count) + ' seconds'
SET @Count = DATEDIFF(second, @Today, GETDATE())
PRINT N'++ Total Execution Time: ' + CONVERT(VARCHAR, @Count) + ' seconds'

---- Delete temporarity directory
SET @cmd = 'rd /s /q ' + @TempDir
EXEC xp_cmdshell @cmd, no_output

---- Delete previous backup versions
DECLARE @OlderDateName CHAR(8)
SET @OlderDateName = CONVERT(CHAR(8), @Today - @CFG_DAYS_DELETE, 112)

----- List all .RAR files
CREATE TABLE #delList
    (
      subdirectory VARCHAR(256),
      depth INT,
      [file] BIT
    )
INSERT  INTO #delList
        EXEC xp_dirtree @CFG_BACKUP_PATH, 1, 1
DELETE  #delList
WHERE   RIGHT(subdirectory, 4) <> '.RAR'

SELECT  @Count = COUNT(1)
FROM    #delList
PRINT N'++ Number of Backups: ' + CONVERT(NVARCHAR, @Count)

SELECT TOP 1
        @name = subdirectory
FROM    #delList
WHERE   LEN(subdirectory) = 12
        AND RIGHT(subdirectory, 4) = '.RAR'
        AND REPLACE(subdirectory, '.RAR', '') < @OlderDateName

WHILE ( @@ROWCOUNT = 1 ) 
    BEGIN
        PRINT N'++ Delete Older Backup: ' + @name
        SET @cmd = 'del /f /q ' + @CFG_BACKUP_PATH + '\' + @name
        EXEC xp_cmdshell @cmd, no_output

        DELETE  #delList
        WHERE   subdirectory = @name

        SELECT TOP 1
                @name = subdirectory
        FROM    #delList
        WHERE   LEN(subdirectory) = 12
                AND RIGHT(subdirectory, 4) = '.RAR'
                AND REPLACE(subdirectory, '.RAR', '') < @OlderDateName
    END

DROP TABLE #delList

PRINT N'++ Done.'
PRINT ''
PRINT ''
PRINT ''