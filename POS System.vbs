Set objFSO = CreateObject("Scripting.FileSystemObject")
Set WshShell = CreateObject("WScript.Shell")

' Get folder of this VBS file
folder = objFSO.GetParentFolderName(WScript.ScriptFullName)
WshShell.CurrentDirectory = folder

' Build full path to batch file
batFile = folder & "\start-app.bat"

' Check if batch file exists
If objFSO.FileExists(batFile) Then
    ' Run batch file hidden
    WshShell.Run """" & batFile & """", 0, False
Else
    MsgBox "Cannot find " & batFile, vbCritical, "Error"
End If
