<?php

/**
 * @file plugins/generic/userWebsiteSettings/upload.php
 *
 * Copyright (c) 2015 Carola Fanselow
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *
 * @brief code for uploading images
 */

	$remove = isset($_POST['buttonRemoveProfileImage']);
	$upload = isset($_POST['buttonUploadSelectedImage']);

	$tmpFile = $_FILES["fileToUpload"]["tmp_name"];
	$referer = "$_SERVER[HTTP_REFERER]";

	// check if the extension is jpg
	$imageName = $_FILES['fileToUpload']['name'];
	$imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
	$extOkay = $imageExt=="jpg" || $imageExt=="jpeg";

    $imageNoFake = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	$sizeOkay = $_FILES["fileToUpload"]["size"] <= 2000000;

	if ($remove|| ($upload && !$tmpFile=="" && $extOkay && $imageNoFake && $sizeOkay)) {

		$userId = $_POST['userId'];
		$currentPath = realpath(dirname(__FILE__));
		$imagePath = $currentPath . "/profileImg/".$userId.".jpg";

		if (is_readable($imagePath)) {
			unlink($imagePath);	
		}	

		if ($upload) {

		    if (move_uploaded_file($tmpFile, $imagePath)) {
				chmod($imagePath, 0777);
   			} else {

   			}
		}
	}

	header("Location:".$referer);

?>


