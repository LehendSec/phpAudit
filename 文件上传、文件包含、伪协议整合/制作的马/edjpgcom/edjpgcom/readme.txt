edjpgcom is a free Windows application that allows you to change (or
add) a JPEG commment in a JPEG file. That's all it does. All other
fields in a JFIF or Exif file are left untouched. It even keeps the
filesystem timestamp! It's based on the rdjpgcom and wrjpgcom
utilities from the Independent JPEG Group's 6b distribution. (Heck,
it's essentially these two programs combined with a basic dialog
control.)

Installation:
-------------

Copy the executable egjpgcom.exe someplace convenient. It's
completely self contained and uses no registry entries.

Usage:
------
	edjpgcom "filename.jpg"

The quotes around the filename are *required* (Don't ask.)

A dialog will popup. The text area will contain any existing comment
text. You can cut and paste using the usual Ctrl-C, Ctrl-V,
Ctrl-X. If you press OK, this text will be added to your file and a
backup file will be created with the same name as your file but with a
".bak" added. If you don't want this backup file, check the 'Delete
Backup?' checkbox. Of course pressing Cancel will exit without doing
anything. 

If you want to delete a comment, just delete all the comment text. 
Edjpgcom will then delete the comment section. 

Now I don't really expect this program to be used from the command
line. Instead, I assume that you will add it to either the external
programs list of another editing/managment program OR add it to the
context menu for JPEG files in windows.

NOTE: Changing file associations akin to editing your registry. 
Be careful and be sure you know how to undo anything you might 
have done.

For example, in Windows 98:
* open Windows Explorer
* View -> Folder Options
* select the "File Types" tab
* Find and select the JPG file type(s)
	NOTE: Sometimes you may not be able to find the JPG File types
	If this happens see below.
* press the "Edit" button
* press the "New" button
* for the "Action:" enter 
  Comment
* for the "Application used..." enter
  "c:\full\path\to\edjpgcom.exe" "%1"
where the quotes are important (if you do not use quotes around the 
program path name, Windows may assume only short filenames can be 
passed to the program) and replacing c:\full\path\to with the correct
directory path where you put the executable. 

Now "Comment" should appear when you right-click on a JPEG file. This
works well in combination with "View as webpage" or "Thumbnails".

In Win2k, the procedure is the same except you get to the 
add action dialog this way:
* Open "Windows NT Explorer"
* Tools -> Folder Options
* select the "File Types" tab
* Find and select the JPG file type(s)
* press the "Advanced" button


NOTE: Sometimes you may not be able to find the file type associated
with .jpg files. This usually happens after several programs have reset 
your file associations. I use an utility from PC Mag called
"Freedom of Association" to reassign the .jpg extension to a registered type.

Another alternative is to add a shortcut to egjpgcom.exe to your Windows 
"Send To" directory. You can then send a file to edjpgcom. It will politely 
refuse to handle a non-jpeg file.

If you use ThumbsPlus to manage your photos (highly recommended), you can add 
edjpgcom as an external program using the following steps:
* Right-click on the tool bar to bring up the customization window
* Press the "External Programs" button
* Enter the full pathname to edjpgcom.exe in the Program box.
* Make sure the "Short Names", "multiple files" and DDE are NOT checked.

Now you will have an icon on your toolbar and a menu item on the right-click
popup window for image files.


Template File
-------------
If the environment variable EDJPGCOM_TEMPLATE exists and points to a readable
file, then the contents of this file will be used as the default comment when 
adding a comment to a file that does not already have one.


License:
--------
Permission to use this software for any purpose, without fee, is
hereby granted. THIS SOFTWARE IS BEING PROVIDED AS IS, without any
express or implied warranty.

If you like it send me one of your photos (email or a postcard) at:

edjpgcom@yahoo.com
Erik Magnuson
7490 Windover Way
Titusville, FL 32780

Send comments/bug reports to edjpgcom@yahoo.com.

How it works:
-------------
When egjpgcom starts, it opens the file, extracts the comments and
then closes the file again. When you press OK, it creates a temporary
file with the same path and filename as the original, but with an
random 3 digit extension instead of ".jpg". It then copies the header
data from the original file, the new comment, and the rest of the
original file to this temporary file. It closes the temp file and
attempts to rename the original file to ".bak". If this succeeds, it
will rename the temporary file to have the original filename. Last, it
deletes the backup file if that options is selected.

What this means is that if egjpgcom should crash for any reason, you
should still have your original file (unless you deleted it!). You
might have to clean up a .nnn file but that's it.

It's been tested on Win2k, NT 4.0 SP5, Win95a, Win98, and Win98SE. 


Limitations:
------------
* The egjpgcom program itself only supports a single filename as
input. However, if multiple files are selected and "Add Comments"
context menu is chosen, multiple copies of egjpgcom will be
started. Hopefully, the filename in the title bar will help you figure
out which is which.

* While a JPEG comment field can up to 64k chars, edjpegcom is limited
to 32000 characters by the Win32 edit dialog.

* egjpgcom uses the DOS/Windows CRLF convention for line
terminators. It will convert any plain CR or LF to a CRLF pair.

* edjpgcom has not been tested with multiple COM sections in a single file.

* edjpgcom does not get any comment data from other comment fields like
  Photoshop FileInfo (IPTC/NAA subset)
  Exif ImageDescription
  Exif UserComment

* Error messages are primitive.

Credits:
--------
This software is based in part on the work of the Independent JPEG Group.
Thanks to Jacob Navia for lcc-win32.
rec.photo.digital for all the information.

History:
--------
17 Feb 2001	v0.1 foisted upon an indifferent world
19 Feb 2001	v0.2 Fixed Win9x file creation timestamp (well, it worked on Win2k!)
10 Apr 2001	v0.3 Deletes comment section if empty comment entered
		     Does not write file is comment text unchanged and "OK" pressed.
		     Added a simple program icon.
19 Dec 2001     v0.4 Larger dialog box size
                     Defaults to delete backup
		     Optional template file if empty comment block.
