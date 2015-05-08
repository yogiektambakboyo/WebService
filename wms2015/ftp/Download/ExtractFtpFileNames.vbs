Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objArgs = WScript.Arguments
myFile = objArgs(0)
if objArgs.Count>=2 then
	myFolder = objArgs(1)
else
	myFolder = "."
end if
if objArgs.Count>=3 then
	myProcessor = objArgs(2)
else
	myProcessor = "DelSizeEqu.bat"
end if
if objArgs.Count>=4 then
	myWildCard = objArgs(3)
else
	myWildCard = "*.txt"
end if
Set objFile = objFSO.OpenTextFile(myFile,1)
strFile = objFSO.GetBaseName(myFile)
strExtension = lcase(objFSO.GetExtensionName(myFile))
Set objFileWrite = objFSO.CreateTextFile(myFile & ".tmp",true)
bolFileName = false
Do While Not objFile.AtEndOfStream
	strLine=objFile.ReadLine
	if strLine="150 Here comes the directory listing." then
		bolFileName=true
	else
		if strLine="226 Directory send OK." then
			bolFileName=false
		else
			if bolFileName then
				intSpacePosSize=instr(26,strLine," ")
				strSize2File=ltrim(mid(strLine,intSpacePosSize+1,len(strLine)-intSpacePosSize))
				arrSize2File=split(strSize2File," ")
				strSize=arrSize2File(0)
				strFile=arrSize2File(5)
				objFileWrite.WriteLine("call " & myProcessor & " " & strFile & " " & strSize & " " & myFolder & " " & myWildCard )
			end if
		end if
	end if
Loop
objFile.Close
objFileWrite.Close
objFSO.DeleteFile(myFile)
objFSO.MoveFile myFile & ".tmp",myFile
