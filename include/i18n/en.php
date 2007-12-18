<?php
//---------------------------------------------------------------------------------------------------
//							
//	WebJeff - FileManager v1.6
//	
//	Jean-Fran�ois GAZET
//	http://www.webjeff.org
//	webmaster@webjeff.org	
//
//---------------------------------------------------------------------------------------------------

$mess=array(
"0" => "Last version",
"1" => "Filename",
"2" => "Size",
"3" => "Type",
"4" => "Modified",
"5" => "Actions",
"6" => "Rename",
"7" => "Suppr.",
"8" => "Directory",
"9" => "Midi File",
"10" => "Text file",
"11" => "Javascript",
"12" => "GIF picture",
"13" => "JPG picture",
"14" => "HTML page",
"15" => "HTML page",
"16" => "REAL file",
"17" => "REAL file",
"18" => "PERL script",
"19" => "ZIP file",
"20" => "WAV file",
"21" => "PHP script",
"22" => "PHP script",
"23" => "File",
"24" => "Parent directory",
"25" => "Upload one or more files (max. ".ConfService::getConf('UPLOAD_MAX_NUMBER').") in the directory : ",
"26" => "Create a new directory in : ",
"27" => "Upload",
"28" => "Create a new file in : ",
"29" => "Create",
"30" => "Write a name for the directory then click on &quot;Create&quot;",
"31" => "You must select a file",
"32" => "Go back",
"33" => "Error uploading file !",
"34" => "The file",
"35" => "has been successfully created in the directory",
"36" => "It's size is",
"37" => "You must write a valid name",
"38" => "The directory",
"39" => "has been create in",
"40" => "This directory already exists (names are case insensitive)",
"41" => "has been renamed to",
"42" => "to",
"43" => "already exists  (names are case insensitive)",
"44" => "has been deleted",
"45" => "directory",
"46" => "file",
"47" => "Do you really want to delete the",
"48" => "OK",
"49" => "CANCEL",
"50" => "Exe file",
"51" => "Edit",
"52" => "Editing file",
"53" => "Save",
"54" => "Cancel",
"55" => "has been modified",
"56" => "BMP picture",
"57" => "PNG picture",
"58" => "CSS File",
"59" => "MP3 File",
"60" => "RAR File",
"61" => "GZ File",
"62" => "Root directory",
"63" => "Log out",
"64" => "XLS File",
"65" => "Word File",
"66" => "Copy",
"67" => "Selected file",
"68" => "Paste in",
"69" => "Or select another directory",
"70" => "Move",
"71" => "This file already exists (names are case insensitive)",
"72" => "The root path is not correct. Check it in the conf/conf.php file",
"73" => "has been copied into the directory",
"74" => "has been moved into the directory",
"75" => "The file users.txt is not in the directory prive",
"76" => "This file has been removed",
"77" => "Send",
"78" => "Pass",
"79" => "PDF File",
"80" => "MOV File",
"81" => "AVI File",
"82" => "MPG File",
"83" => "MPEG File",
"84" => "Help",
"85" => "Refresh",
"86" => "Close",
"87" => "Search",
"88" => "Download",
"89" => "Unable to open file",
"90" => "Print",
"91" => "FLASH File",
"92" => "Language",
"93" => "To choose your language, your browser must accept cookies.",
"94" => "Login",
"95" => "Select your language :",
"96" => "Please select the destination folder in the tree : ",
"97" => "Upload File",
"98" => "Click anywhere on this box to close it.",
"99" => "is not writeable. There might be a permission problem, please check your administrator.", 
"100"=> "Cannot find file ",
"101"=> "The origin and destination folders are the same!",
"102"=> "Error while creating file :", 
"103"=> "Cannot find folder ", 
"104"=> "Go to the given location", 
"105"=> "Email an url to access directly to this location.", 
"106"=> "Send Mail",
"107"=> "Your name and/or email", 
"108"=> "The destination email", 
"109"=> "Clickable URL", 
"110"=> "Add a comment", 
"111"=> "The following email has been sent :",
"112"=> "Email sending failed : ",
"113"=> "The selection is empty!", 
"114"=> "Unkown error happened during copy!",
"115"=> "The file has been saved successfully", 
"116"=> "files", 
"117"=> "The folder",
"118"=> "Download Multiple File",
"119"=> "Click on each file to download it.",
"120"=> "You are not allowed to delete the whole arborescence!",
"121"=> "Image File ", 
"122"=> "Recycle Bin",
"123"=> "has been moved to the ", 
"124"=> "Overwrite existing files?", 
"125"=> "A file/folder with that name already exists (names are case insensitive). Please choose another one!", 
"126"=> "Thumbnails", 
"127"=> "Size", 
"128"=> "files selected.",
"129"=> "View",
"130"=> "Folders", 
"131"=> "Details", 
"132"=> "No file selected", 
"133"=> "Name",
"134"=> "Type",
"135"=> "Dimensions",
"136"=> "View Larger Image",
"138"=> "Last Modif.",
"139"=> "Edit Online", 
"140"=> "Play whole folder", 
"141"=> "Reading folder", 
"142"=> "Logged as ",
"143"=> "Guest browsing. Log in.",
"144"=> "Not logged in.",
"145"=> "MyBookmarks",
"146"=> "Delete Bookmark",
"147"=> "Bookmarks", 
"148"=> "Parent", 
"149"=> "Refresh",
"150"=> "Display",
"151"=> "Switch display mode...",
"152"=> "Bookmark",
"153"=> "Add current location to MyBookmarks",
"154"=> "New Dir",
"155"=> "Create new directory",
"156"=> "New File",
"157"=> "Create new empty file",
"158"=> "Rename selected file or folder",
"159"=> "Copy selection to...",
"160"=> "Move selected files to ...",
"161"=> "Delete selected files.",
"162"=> "Edit text files online.",
"163"=> "Log in",
"164"=> "Log out",
"165"=> "Settings",
"166"=> "About",
"167"=> "About AjaXplorer",
"168" => "Connect to AjaXplorer",
"169" => "Disconnect from AjaXplorer",
"170" => "Current Folder",
"parent_access_key" => "P",
"refresh_access_key" => "h",
"display_access_key" => "i",
"bookmarks_access_key" => "B",
"upload_access_key" => "U",
"folder_access_key" => "D",
"file_access_key" => "F",
"rename_access_key" => "R",
"copy_access_key" => "C",
"move_access_key" => "M",
"delete_access_key" => "S",
"edit_access_key" => "E",
"view_access_key" => "V",
"download_access_key" => "w",
"settings_access_key" => "t",
"about_access_key" => "A", 
"empty_recycle_access_key" => "y", 
"restore_access_key" => "o", 
"171" => "Browse your computer",
"172" => "Your Selection",
"173" => "New folder name",
"174" => "New file name",
"175" => "Select destination folder",
"176" => "The selected files will be moved to the Recycle Bin.",
"177" => "Are you sure you want to delete permanently the selected files?",
"178" => "Previous",
"179" => "Next", 
"180" => "Enter login/password",
"181" => "Login",
"182" => "Password",
"183" => "Please select a destination folder different from the origin!",
"184" => "Search in current folder and sub-folders",
"185" => "Stop searching", 
"186" => "Image Preview", 
"187" => "Online Edition - ", 
"189" => "Edit My Preferences",
"190" => "Language", 
"191" => "Default Display", 
"192" => "Details List", 
"193" => "Thumbnails", 
"194" => "Change Password",
"195" => "User Preferences", 
"196" => "Your language differs from the current language!\\n Do you want to reload the page to switch language?", 
"197" => "User preferences changed successfullly! \\n\\n. If you changed password it will take effect after your deconnexion. \\n\\n. If you changed language, please hit refresh (F5) on your browser.", 
"198" => "New",
"199" => "Confirm",
"200" => "Switch Repository to...", 
"201" => "Warning, some changes are unsaved!\\n Are you sure you want to close?",
"202" => "Warning, recursive copy!",
"203" => "Destintation folder is the same as original folder!",
/* GROUPED SENTENCE : 'the file "filename.ext" exceeds size limit (1Mb)\nIt will not be uploaded.*/
"204" => "The file \"",
"205" => "\" exceeds size limit (",
"206" => "Mb).\\nIt will not be uploaded.",
/* END SENTENCE */
"207" => "You have no write permission on this folder",
"208" => "You have no read permission on this folder", 
"209" => "Internal server error, please contact Administrator!",
"210" => "Upload failed", 
"211" => "The file is too big!", 
"212" => "No file found on server!", 
"213" => "Error while copying file to current folder", 
"214" => "Browse files", 
"215" => "Start Upload", 
"216" => "Clear Queue", 
"217" => "Clear Completed", 
"218" => "Remove from queue", 
"219" => "Completed", 
"220" => "Empty", 
"221" => "Empty Recycle Bin", 
"222" => "Restore", 
"223" => "Restore file to its original location", 
"224" => "Go to", 
"225" => "Rename bookmark"
);
?>