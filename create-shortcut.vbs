Set WshShell = CreateObject("WScript.Shell")
Set FSO = CreateObject("Scripting.FileSystemObject")
strPath = FSO.GetParentFolderName(WScript.ScriptFullName)

' Create desktop shortcut
Set oShortcut = WshShell.CreateShortcut(WshShell.SpecialFolders("Desktop") & "\POS.lnk")
oShortcut.TargetPath = "wscript.exe"
oShortcut.Arguments = Chr(34) & strPath & "\POS.vbs" & Chr(34)
oShortcut.WorkingDirectory = strPath
oShortcut.IconLocation = strPath & "\icon.ico"
oShortcut.Description = "POS - Point of Sale System"
oShortcut.Save

MsgBox "Desktop shortcut created successfully!" & vbCrLf & vbCrLf & "Double-click 'POS' on your desktop to start the application.", vbInformation, "POS Setup"
