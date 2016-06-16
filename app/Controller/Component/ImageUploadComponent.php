<?php

/**
 * ImageUploadComponent
 *
 * This component is used to upload images.
 * Generate thimbnails of uploaded images, also resizes the uploaded images.
 *
 * @author Lakum
 */
class ImageUploadComponent extends Component {

    /**
     * This method is used to upload image with validations.
     * It also process for image resize and cropping.
     *
     * @param array  $data          array of image data
     * @param string $dataKey       the data key of array
     * @param int    $imgScale      width of bigger version of image
     * @param int    $thumbScale    width of thumb version of image
     * @param string $folderName    directory name where image will be stored
     * @param bool   $square        thumbnail shoud be square or not
     * @param int    $maxFileSizeKb maximum file size
     *
     * @return string
     */
    public function uploadImageAndThumbnail($data, $dataKey, $imgScale, $thumbScale, $folderName, $square = false, $maxFileSizeKb = false) {
        if (strlen($data[$dataKey]['name']) >= 1) {
            $error = 0;
            $tempUploadDir = WWW_ROOT . "files/temp";
            $bigUploadDir = WWW_ROOT . "files/" . $folderName;
            $smallUploadDir = WWW_ROOT . "files/" . $folderName . "/thumbs";

//            if (!is_dir($tempUploadDir)) {
//                mkdir($tempUploadDir, 0777, true);
//            }
//            if (!is_dir($bigUploadDir)) {
//                mkdir($bigUploadDir, 0777, true);
//            }
//            if (!is_dir($smallUploadDir)) {
//                mkdir($smallUploadDir, 0777, true);
//            }

            $fileType = $this->getFileExtension($data[$dataKey]['name']);
            $fileType = strtolower($fileType);
            if (($fileType != "jpeg") && ($fileType != "jpg") && ($fileType != "gif") && ($fileType != "png")) {
                return 'invalidType';
            } elseif (isset($maxFileSizeKb) && round(filesize($data[$dataKey]['tmp_name']) / 10024, 2) > $maxFileSizeKb) {
                return 'invalidSize';
            } else {
                $imgSize = GetImageSize($data[$dataKey]['tmp_name']);
                if (!$imgSize) {
                    return 'invalid';
                }
            }
            // Generate a unique name for the image (from the timestamp)
            $randomName = str_replace(".", "", strtotime("now"));
            $fileName = $randomName . md5($data[$dataKey]['name']);
            settype($fileName, "string");
            $fileName .= ".";
            $fileName .= $fileType;
            $tempFile = $tempUploadDir . "/$fileName";
            $resizedFile = $bigUploadDir . "/$fileName";
            $croppedFile = $smallUploadDir . "/$fileName";
            if (is_uploaded_file($data[$dataKey]['tmp_name'])) {
                // Copy the image into the temporary directory
                if (!copy($data[$dataKey]['tmp_name'], "$tempFile")) {
                    print "Error Uploading File!.";
                    exit();
                } else {
                    $this->resizeImage($tempFile, $imgScale, $resizedFile);
                    if ($thumbScale != 0) {
                        if ($square) {
                            if (isset($medHeight) && isset($medWidth) && !empty($medHeight) && !empty($medWidth)) {
                                $this->cropImageNew($tempFile, $croppedFile, $medWidth, $medHeight); // for small
                            } else {
                                $this->cropImage($tempFile, $thumbScale, $croppedFile);
                            }
                        } else {
                            $this->resizeImage($tempFile, $thumbScale, $croppedFile);
                        }
                        unlink($tempFile);
                    }
                }
            }
            return $fileName;
        }
    }

    /**
     * This method is used to upload image with validations.
     * It also process for image resize and cropping.
     *
     * @param array  $downloadedImageName array of image data
     * @param string $dataKey             the data key of array
     * @param int    $imgScale            width of bigger version of image
     * @param int    $thumbScale          width of thumb version of image
     * @param string $folderName          directory name where image will be stored
     * @param bool   $square              thumbnail shoud be square or not
     * @param int    $maxFileSizeKb       maximum file size
     *
     * @return string
     */
    public function uploadImageAndThumbnailForSocial($downloadedImageName, $dataKey, $imgScale, $thumbScale, $folderName, $square = false, $maxFileSizeKb = false) {
        $error = 0;
        $tempUploadDir = WWW_ROOT . "files/temp";
        $bigUploadDir = WWW_ROOT . "files/" . $folderName;
        $smallUploadDir = WWW_ROOT . "files/" . $folderName . "/thumbs";

//        if (!is_dir($tempUploadDir)) {
//            mkdir($tempUploadDir, 0777, true);
//        }
//        if (!is_dir($bigUploadDir)) {
//            mkdir($bigUploadDir, 0777, true);
//        }
//        if (!is_dir($smallUploadDir)) {
//            mkdir($smallUploadDir, 0777, true);
//        }

        $fileType = $this->getFileExtension($downloadedImageName);
        $fileType = strtolower($fileType);

        // Generate a unique name for the image (from the timestamp)
        $fileName = $downloadedImageName;
        $tempFile = $tempUploadDir . "/$fileName";
        $resizedFile = $bigUploadDir . "/$fileName";
        $croppedFile = $smallUploadDir . "/$fileName";

        $this->resizeImage($tempFile, $imgScale, $resizedFile);
        if ($thumbScale != 0) {
            if ($square) {
                if (isset($medHeight) && isset($medWidth) && !empty($medHeight) && !empty($medWidth)) {
                    $this->cropImageNew($tempFile, $croppedFile, $medWidth, $medHeight); // for small
                } else {
                    $this->cropImage($tempFile, $thumbScale, $croppedFile);
                }
            } else {
                $this->resizeImage($tempFile, $thumbScale, $croppedFile);
            }
            unlink($tempFile);
        }
        return $fileName;
    }

    /**
     * This method deletes temporary image.
     *
     * @param string $fileName   name of image file
     * @param string $folderName directory name
     *
     * @return void
     */
    public function deleteImage($fullImagePath) {
        if (file_exists($fullImagePath)) {
            try {
               // unlink($fullImagePath);
            } catch (Exception $ex) {

            }
        }
    }

    /**
     * This method is used to crop uploaded image.
     *
     * @param string $imgName  image name
     * @param int    $scale    image scaling in numbers
     * @param string $fileName file name of cropped image
     *
     * @return void
     */
    public function cropImage($imgName, $scale, $fileName) {
        $fileType = $this->getFileExtension($imgName);
        $fileType = strtolower($fileType);
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                $imgSrc = ImageCreateFromjpeg($imgName);
                break;
            case "gif":
                $imgSrc = imagecreatefromgif($imgName);
                break;
            case "png":
                $imgSrc = imagecreatefrompng($imgName);
                break;
        }
        $width = imagesx($imgSrc);
        $height = imagesy($imgSrc);
        $ratioX = $width / $height * $scale;
        $ratioY = $height / $width * $scale;
        // Calculate resampling
        $newHeight = ($width <= $height) ? $ratioY : $scale;
        $newWidth = ($width <= $height) ? $scale : $ratioX;
        // Calculate cropping (division by zero)
        $cropX = ($newWidth - $scale != 0) ? ($newWidth - $scale) / 2 : 0;
        $cropY = ($newHeight - $scale != 0) ? ($newHeight - $scale) / 2 : 0;
        // Setup Resample & Crop buffers
        $resampled = imagecreatetruecolor($newWidth, $newHeight);
        $cropped = imagecreatetruecolor($scale, $scale);
        // Resample
        imagecopyresampled($resampled, $imgSrc, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        // Crop
        imagecopy($cropped, $resampled, 0, 0, $cropX, $cropY, $newWidth, $newHeight);
        // Save the cropped image
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                imagejpeg($cropped, $fileName, 100);
                break;
            case "gif":
                imagegif($cropped, $fileName);
                break;
            case "png":
                imagepng($cropped, $fileName, 9);
                break;
        }
    }

    /**
     * This method crops image which is already uploaded.
     *
     * @param string $imgName     image name (tmp)
     * @param string $fileName    name of image to be stored
     * @param int    $scaleWidth  image width
     * @param int    $scaleHeight image height
     *
     * @return void
     */
    public function cropImageNew($imgName, $fileName, $scaleWidth, $scaleHeight) {
        $fileType = $this->getFileExtension($imgName);
        $fileType = strtolower($fileType);
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                $imgSrc = ImageCreateFromjpeg($imgName);
                break;
            case "gif":
                $imgSrc = imagecreatefromgif($imgName);
                break;
            case "png":
                $imgSrc = imagecreatefrompng($imgName);
                break;
        }
        $width = imagesx($imgSrc);
        $height = imagesy($imgSrc);
        $ratioX = $width / $height * $scaleHeight;
        $ratioY = $height / $width * $scaleWidth;
        // Calculate resampling
        $newHeight = ($width <= $height) ? $ratioY : $scaleHeight;
        $newWidth = ($width <= $height) ? $scaleWidth : $ratioX;
        // Calculate cropping (division by zero)
        $cropX = ($newWidth - $scaleWidth != 0) ? ($newWidth - $scaleWidth) / 2 : 0;
        $cropY = ($newHeight - $scaleHeight != 0) ? ($newHeight - $scaleHeight) / 2 : 0;
        // Setup Resample & Crop buffers
        $resampled = imagecreatetruecolor($newWidth, $newHeight);
        $cropped = imagecreatetruecolor($scaleWidth, $scaleHeight);
        // Resample
        imagecopyresampled($resampled, $imgSrc, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        // Crop
        imagecopy($cropped, $resampled, 0, 0, $cropX, $cropY, $newWidth, $newHeight);
        // Save the cropped image
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                imagejpeg($cropped, $fileName, 100);
                break;
            case "gif":
                imagegif($cropped, $fileName);
                break;
            case "png":
                imagepng($cropped, $fileName, 9);
                break;
        }
    }

    /**
     * This method is used to resize uploaded image.
     *
     * @param string $imgName  image name
     * @param int    $size     size of image
     * @param string $fileName name of file
     *
     * @return void
     */
    public function resizeImage($imgName, $size, $fileName) {
        $fileType = $this->getFileExtension($imgName);
        $fileType = strtolower($fileType);
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                $imgSrc = ImageCreateFromjpeg($imgName);
                break;
            case "gif":
                $imgSrc = imagecreatefromgif($imgName);
                break;
            case "png":
                $imgSrc = imagecreatefrompng($imgName);
                break;
        }
        $trueWidth = imagesx($imgSrc);
        $trueHeight = imagesy($imgSrc);
        if ($size == 0) {
            $width = $trueWidth;
            $height = ($width / $trueWidth) * $trueHeight;
        } else {
            if ($trueWidth >= $trueHeight) {
                $width = $size;
                $height = ($width / $trueWidth) * $trueHeight;
            } else {
                $width = $trueWidth;
                $height = ($width / $trueWidth) * $trueHeight;
            }
        }
        $imgDes = ImageCreateTrueColor($width, $height);
        imagecopyresampled($imgDes, $imgSrc, 0, 0, 0, 0, $width, $height, $trueWidth, $trueHeight);
        // Save the resized image
        switch ($fileType) {
            case "jpeg":
            case "jpg":
                imagejpeg($imgDes, $fileName, 100);
                break;
            case "gif":
                imagegif($imgDes, $fileName);
                break;
            case "png":
                imagepng($imgDes, $fileName, 9);
                break;
        }
    }

    /**
     * This method is used to get file extension.
     *
     * @param string $str file name
     *
     * @return string
     */
    public function getFileExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    /**
     * This method is used to get file extension.
     *
     * @param string $type file name
     *
     * @return string
     */
    public function getFileExtensionFromUploadedFile($type = 'image/jpeg') {
        $ext = 'jpeg';
        $getArray = explode('/', $type);
        if (!empty($getArray[1])) {
            $ext = $getArray[1];
        }
        return $ext;
    }

    /**
     * This method is used to remove the image.
     *
     * @param string $fileName   file name to be deleted
     * @param string $folderName directory name where image is stored
     *
     * @return void
     */
    public function removeImage($fileName, $folderName) {
        //unlink("img/" . $folderName . "/" . $fileName);
    }

    /**
     * Resize image function
     *
     * @param type $img         for image
     * @param type $newFilename for img name
     * @param type $quality     for quality of image
     *
     * @return bool
     */
    public function compressImage($img, $newFilename, $quality = 75) {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        $imgInfo = getimagesize($img);
        switch ($imgInfo[2]) {
            case 1:
                $image = imagecreatefromgif($img);
                break;
            case 2:
                $image = imagecreatefromjpeg($img);
                break;
            case 3:
                $image = imagecreatefrompng($img);
                break;
            case 4:
                $image = imagecreatefromstring($img);
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        $sourceWidth = $imgInfo[0];
        $sourceHeight = $imgInfo[1];
        $tmpImg = imagecreatetruecolor($sourceWidth, $sourceHeight);
        switch ($imgInfo[2]) {
            case 1:
            case 3:
                imagealphablending($tmpImg, false);
                $color = imagecolorallocatealpha($tmpImg, 255, 255, 255, 127);
                imagefilledrectangle($tmpImg, 0, 0, $sourceWidth, $sourceHeight, $color);
                imagealphablending($tmpImg, true);
                imagesavealpha($tmpImg, true);
                break;
            default:
                $fillColor = imagecolorallocate($tmpImg, 221, 221, 221);
                imagefill($tmpImg, 0, 0, $fillColor);
                break;
        }
        imagecopyresampled($tmpImg, $image, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);
        //Generate the file, and rename it to $newfilename
        try {
            switch ($imgInfo[2]) {
                case 1:
                    imagegif($tmpImg, $newFilename, $quality);
                    break;
                case 2:
                    imagejpeg($tmpImg, $newFilename, $quality);
                    break;
                case 3:
                    if ($quality < 30) {
                        $quality = 9;
                    } else if ($quality < 60) {
                        $quality = 5;
                    } else {
                        $quality = 3;
                    }

                    imagepng($tmpImg, $newFilename, $quality);
                    break;
                default:
                    trigger_error('Unsupported filetype!', E_USER_WARNING);
                    break;
            }
        } catch (Exception $e) {
            trigger_error('Failed resize image!', E_USER_WARNING);
        }
        imagedestroy($image);
        imagedestroy($tmpImg);
        return $newFilename;
    }

    /**
     * This method is used to get file extension.
     *
     * @param string $type file name
     *
     * @return string
     */
    public function getFileExtensionFromUploadedFileUsingMimeType($img = 'image/jpeg') {
        $imgInfo = getimagesize($img);
        if (!empty($imgInfo['mime'])) {
            if ($imgInfo['mime'] == 'image/png') {
                $ext = 'png';
            } else if ($imgInfo['mime'] == 'image/jpeg') {
                $ext = 'jpeg';
            } else if ($imgInfo['mime'] == 'image/jpe') {
                $ext = 'jpeg';
            } else if ($imgInfo['mime'] == 'image/jpg') {
                $ext = 'jpeg';
            } else if ($imgInfo['mime'] == 'image/gif') {
                $ext = 'gif';
            } else {
                $ext = 'png';
            }
        }
        return $ext;
    }
    
    /**
     * This method is used to get file extension.
     *
     * @param string $type file name
     *
     * @return string
     */
    public function getFileExtensionFromUploadedVideoUsingMimeType($vid = 'video/mp4') {
        if (!empty($vid)) {
            if ($vid == 'video/mp4') {
                $ext = 'mp4';
            } else if ($vid == 'video/x-flv') {
                $ext = 'flv';
            } else if ($vid == 'application/x-mpegURL') {
                $ext = 'm3u8';
            } else if ($vid == 'video/MP2T') {
                $ext = 'ts';
            } else if ($vid == 'video/3gpp') {
                $ext = '3gp';
            } else if ($vid == 'video/quicktime') {
                $ext = 'mov';
            } else if ($vid == 'video/x-msvideo') {
                $ext = 'avi';
            } else if ($vid == 'video/x-ms-wmv') {
                $ext = 'wmv';
            } else {
                $ext = 'mp4';
            }
        }
        return $ext;
    }
    
}
