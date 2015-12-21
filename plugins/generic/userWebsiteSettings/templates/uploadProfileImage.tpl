{**
 * plugins/generic/userWebsiteSettings/templates/uploadProfileImage.tpl
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Popup window for uploading an profile image.
 *}

<!DOCTYPE html>
<html lang="en-US" xml:lang="en-US">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Upload Profile Image</title>
	<link rel="stylesheet" href="{$baseUrl}/plugins/generic/userWebsiteSettings/css/uploadProfileImage.css" type="text/css" />
</head> 

<div id="uploadProfileImage">

<h3>Upload or remove your profile image</h3>

	<p>Please upload only jpg/jpeg-files with a maximum size of 2MB.</p><br>
	<div id="profileImage">
		<img alt="no profile image uploaded" src="{$imagePath}"/>
		<p id="imageText">Your current profile image</p>
	</div>	

	<div id="uploadForm">
	<form  action="{$baseUrl}/plugins/generic/userWebsiteSettings/upload.php"
			method="post" enctype="multipart/form-data">
  
		<a>Remove profile image:</a><br>
		<input type="submit"  name="buttonRemoveProfileImage" id="buttonRemoveProfileImage" value="Remove"><br>
	
		<a>Upload new profile image:</a><br>
    	<input type="file" name="fileToUpload" id="fileToUpload"><br>
    	<input type="submit" value="Upload selected file / Refresh"
			id="buttonUploadSelectedImage" name="buttonUploadSelectedImage"><br>
	
		<input type="hidden" name="userId" value={$userId}>

	</form>
	</div>

	<input type="button" id="buttonWindowClose" value="Close Window" onClick="window.close()"><br>

</div>

</body>
</html>

