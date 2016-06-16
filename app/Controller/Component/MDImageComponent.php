<?php

App::uses('Component', 'Controller');

/**
 * MDImage component.
 *
 * This component file used to perform operations like crop, resize, flip, rotate, watermark etc on an image.
 *
 * @package     Cake.Component
 * @subpackage  MD
 * @author      The Chief
 */
class MDImageComponent extends Component {

    /**
     * Component Name
     *
     * @var string
     */
    public $name = 'MDImage';

    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = array();

    /**
     * Controller object to store its instantiating controller
     *
     * @var object
     */
    public $controller;

    /**
     * Stores errors
     *
     * @var array
     */
    public $Errors = array();

    /**
     * Global settings for image upload
     *
     * @var array
     */
    public $GlobalSettings = array(
        'imgMaxWidth' => 1024,
        'imgMaxHeight' => 768,
        'maxFileSize' => 34457280, // Approx 30MB
        'autoResize' => false,
        'uploadBasePath' => ''
    );

    /**
     * Called before the Controller::beforeFilter().
     *
     * @param Controller $controller Controller with components to initialize
     */
    public function initialize(Controller $controller, $settings = array()) {
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error("GD is not loaded. You can not use this component.", E_USER_WARNING);
            return false;
        }
        $this->GlobalSettings['uploadBasePath'] = WWW_ROOT . 'img' . DS;
        $this->setupVars($settings);
        parent::initialize($controller);
    }

    public function setupVars($settings = array()) {
        $this->GlobalSettings = array_merge($this->GlobalSettings, $settings);
    }

    public function uploadImage($uploadedFile, $prefix = '', $uploadToPath = null) {
        if (empty($uploadedFile)) {
            $this->Errors[] = 'Invalid image';
            return false;
        }

        if (!empty($uploadToPath)) {
            $uploadToPath = trim($uploadToPath, '\\');
            $uploadToPath = trim($uploadToPath, DS);
            $this->GlobalSettings['uploadBasePath'] = $uploadToPath . DS;
        }

        if (!file_exists($this->GlobalSettings['uploadBasePath'])) {
            try {
                //mkdir($this->GlobalSettings['uploadBasePath']);
                chmod($this->GlobalSettings['uploadBasePath'], 0777);
            } catch (Exception $e) {
                $this->Errors[] = 'Directory does not exists or not enough permission to write file.';
                return false;
            }
        }

        $filename = $prefix . md5(uniqid()) . '_' . time() . '.' . $this->getFileExtension($uploadedFile['name']);

        $uploadedFilePath = $this->GlobalSettings['uploadBasePath'] . DS . $filename;

        if (isset($uploadedFile['tmp_name'])) {
            move_uploaded_file($uploadedFile['tmp_name'], $uploadedFilePath);
            chmod($uploadedFilePath, 0777);
            $imgWidth = $this->getWidth($uploadedFilePath);
            $imgHeight = $this->getHeight($uploadedFilePath);

            if ($this->GlobalSettings['autoResize']) {
                // Scale the image if it is greater than the width set above
                if ($imgWidth > $this->GlobalSettings['imgMaxWidth'] || $imgHeight > $this->GlobalSettings['imgMaxHeight']) {
                    $scale = 1;
                    $imgWidth = $this->GlobalSettings['imgMaxWidth'];
                    $imgHeight = $this->GlobalSettings['imgMaxHeight'];
                    $uploaded = $this->resizeImage($uploadedFilePath, $this->GlobalSettings['imgMaxWidth'], $this->GlobalSettings['imgMaxHeight'], $uploadedFilePath);
                } else {
                    $scale = 1;
                    $uploaded = $this->resizeImage($uploadedFilePath, $imgWidth, $imgHeight, $uploadedFilePath);
                }
            }
        } else {
            return false;
        }
        return array('imagePath' => $uploadedFilePath, 'imageName' => $filename, 'imageWidth' => $imgWidth, 'imageHeight' => $imgHeight);
    }

    public function getHeight($image) {
        $sizes = getimagesize($image);
        $height = $sizes[1];
        return $height;
    }

    public function getWidth($image) {
        $sizes = getimagesize($image);
        $width = $sizes[0];
        return $width;
    }

    public function getFileExtension($filename) {
		$expRes = explode(".", $filename);
        return end($expRes);
    }

    /**
     *
     * @param string $srcFile   The source file.
     * @param int    $srcX      The start x position to crop from.
     * @param int    $srcY      The start y position to crop from.
     * @param int    $srcWidth  The width to crop.
     * @param int    $srcHeight The height to crop.
     * @param int    $dstWidth  The destination width.
     * @param int    $dstHeight The destination height.
     * @param int    $srcAbs    Optional. If the source crop points are absolute.
     * @param string $dstFile   Optional. The destination file to write to.
     *
     * @return string|boolean New filepath on success or false on failure.
     */
    public function cropImage($srcFile, $srcX, $srcY, $srcWidth, $srcHeight, $dstWidth, $dstHeight, $srcAbs = false, $dstFile = false) {
        // Set artificially high because GD uses uncompressed images in memory
        ini_set('memory_limit', '256M');
        $src = imagecreatefromstring(file_get_contents($srcFile));

        $dst = imagecreatetruecolor($dstWidth, $dstHeight);
        if (is_resource($dst) && function_exists('imagealphablending') && function_exists('imagesavealpha')) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        if ($srcAbs) {
            $srcWidth -= $srcX;
            $srcHeight -= $srcY;
        }

        if (function_exists('imageantialias')) {
            imageantialias($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        imagedestroy($src); // Free up memory

        if (!$dstFile) {
            $dstFile = str_replace(basename($srcFile), 'cropped-' . basename($srcFile), $srcFile);
        }

        $return = false;

        $imgInfo = getimagesize($srcFile);
        //Generate the file, and rename it to $newfilename
        switch ($imgInfo[2]) {
            case 1:
                if (imagegif($dst, $dstFile)) {
                    $return = $dstFile;
                }
                break;
            case 2:
                if (imagejpeg($dst, $dstFile, 91)) {
                    $return = $dstFile;
                }
                break;
            case 3:
                if (imagepng($dst, $dstFile)) {
                    $return = $dstFile;
                }
                break;
            default:
                trigger_error('Failed resize image!', E_USER_WARNING);
                break;
        }

        return $return;
    }

    /**
     * Resize image from orignal image
     *
     * @param string $img
     * @param int    $resizeWidth
     * @param int    $resizeHeight
     * @param string $newfilename
     *
     * @return string resize image path
     * @author The Chief
     */
    public function resizeImage($img, $resizeWidth, $resizeHeight, $newFilename) {
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
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        $nWidth = $resizeWidth;
        $nHeight = $resizeHeight;

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }

        imagecopyresampled($newImg, $image, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

        //Generate the file, and rename it to $newfilename
        switch ($imgInfo[2]) {
            case 1:
                imagegif($newImg, $newFilename);
                break;
            case 2:
                imagejpeg($newImg, $newFilename, 91);
                break;
            case 3:
                imagepng($newImg, $newFilename);
                break;
            default:
                trigger_error('Failed resize image!', E_USER_WARNING);
                break;
        }
        return $newFilename;
    }

    public function oldresizeImageWithFixedWidth($img, $resizeWidth, $newFilename) {
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
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        if ($imgInfo[0] > $imgInfo[1]) {
            $ratio = $imgInfo[1] / $imgInfo[0];
        } else {
            $ratio = $imgInfo[0] / $imgInfo[1];
        }
        $resizeHeight = round($resizeWidth * $ratio);

        $nWidth = $resizeWidth;
        $nHeight = $resizeHeight;

        if ($nWidth <= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
            $nHeight = $imgInfo[1];
        } else if ($nWidth >= $imgInfo[0] && $nHeight <= $imgInfo[1]) {
            $nHeight = $imgInfo[1];
        }

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }

        if ($nWidth >= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, round(($nWidth / 2) - ($imgInfo[0] / 2)), round(($nHeight / 2) - ($imgInfo[1] / 2)), 0, 0, $imgInfo[0], $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else if ($nWidth <= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, 0, round(($nHeight / 2) - ($imgInfo[1] / 2)), 0, 0, $nWidth, $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else if ($nWidth >= $imgInfo[0] && $nHeight <= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, round(($nWidth / 2) - ($imgInfo[0] / 2)), 0, 0, 0, $nWidth, $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else {
            imagecopyresampled($newImg, $image, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
        }

        //Generate the file, and rename it to $newfilename
        switch ($imgInfo[2]) {
            case 1:
                imagegif($newImg, $newFilename);
                break;
            case 2:
                imagejpeg($newImg, $newFilename, 91);
                break;
            case 3:
                imagepng($newImg, $newFilename);
                break;
            default:
                trigger_error('Failed resize image!', E_USER_WARNING);
                break;
        }
        return $newFilename;
    }

    public function resizeImageWithFixedWidth($img, $resizeWidth, $newFilename) {
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
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        if ($imgInfo[0] > $imgInfo[1]) {
            $ratio = $imgInfo[1] / $imgInfo[0];
        } else {
            $ratio = $imgInfo[0] / $imgInfo[1];
        }

        /* if ($resizeWidth > $imgInfo[0]) {
          $ratio = $imgInfo[1] / $resizeWidth;
          } else if ($resizeWidth > $resizeHeight) {
          $ratio = $imgInfo[0] / $resizeWidth;
          } else {
          $ratio = $imgInfo[0] / $imgInfo[1];
          } */

        $resizeHeight = round($imgInfo[1] / ($imgInfo[0] / $resizeWidth));

        $nWidth = $resizeWidth;
        $nHeight = $resizeHeight;

        /* if ($nWidth <= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
          $nHeight = $imgInfo[1];
          } else if ($nWidth >= $imgInfo[0] && $nHeight <= $imgInfo[1]) {
          $nHeight = $imgInfo[1];
          } */

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }

        if ($nWidth >= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, round(($nWidth / 2) - ($imgInfo[0] / 2)), round(($nHeight / 2) - ($imgInfo[1] / 2)), 0, 0, $imgInfo[0], $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else if ($nWidth <= $imgInfo[0] && $nHeight >= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, 0, round(($nHeight / 2) - ($imgInfo[1] / 2)), 0, 0, $nWidth, $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else if ($nWidth >= $imgInfo[0] && $nHeight <= $imgInfo[1]) {
            imagecopyresampled($newImg, $image, round(($nWidth / 2) - ($imgInfo[0] / 2)), 0, 0, 0, $nWidth, $imgInfo[1], $imgInfo[0], $imgInfo[1]);
        } else {
            imagecopyresampled($newImg, $image, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
        }

        //Generate the file, and rename it to $newfilename
        /* switch ($imgInfo[2]) {
          case 1: imagegif($newImg, $newFilename);
          break;
          case 2: imagejpeg($newImg, $newFilename, 91);
          break;
          case 3: imagepng($newImg, $newFilename);
          break;
          default: trigger_error('Failed resize image!', E_USER_WARNING);
          break;
          } */
        //$ext = $this->getFileExtension($newFilename);
        //$newFilename = str_replace($ext, 'jpg', $newFilename);
        if (!imagejpeg($newImg, $newFilename, 91)) {
            trigger_error('Failed resize image!', E_USER_WARNING);
        }
        return $newFilename;
    }

    public function resizeImageWithFixedWidthHeight($img, $resizeWidth, $resizeHeight, $newFilename) {
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
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        $sourceWidth = $imgInfo[0];
        $sourceHeight = $imgInfo[1];

        if (($sourceWidth > $resizeWidth) || ($sourceHeight > $resizeHeight)) {
            //original width exceeds, so reduce the original width to maximum limit.
            //calculate the height according to the maximum width.
            if (($sourceWidth > $resizeWidth) && ($sourceHeight <= $resizeHeight)) {
                $percent = $resizeWidth / $sourceWidth;
                $nWidth = $resizeWidth;
                $nHeight = round($sourceHeight * $percent);
            }

            //image height exceeds, recudece the height to maxmimum limit.
            //calculate the width according to the maximum height limit.
            if (($sourceWidth <= $resizeWidth) && ($sourceHeight > $resizeHeight)) {
                $percent = $resizeHeight / $sourceHeight;
                $nHeight = $resizeHeight;
                $nWidth = round($sourceWidth * $percent);
            }

            //both height and width exceeds.
            //but image can be vertical or horizontal.
            if (($sourceWidth > $resizeWidth) && ($sourceHeight > $resizeHeight)) {
                //if image has more width than height
                //resize width to maximum width.
                if ($sourceWidth > $sourceHeight) {
                    $percent = $resizeWidth / $sourceWidth;
                    $nWidth = $resizeWidth;
                    $nHeight = round($sourceHeight * $percent);
                } else {
                    //image is vertical or square. More height than width.
                    //resize height to maximum height.
                    $percent = $resizeHeight / $sourceHeight;
                    $nHeight = $resizeHeight;
                    $nWidth = round($sourceWidth * $percent);
                }
            }
        } else {
            $sourceAspectRatio = $sourceWidth / $sourceHeight;
            $desiredAspectRatio = $resizeWidth / $resizeHeight;
            if ($sourceAspectRatio > $desiredAspectRatio) {
                $nHeight = $resizeHeight;
                $nWidth = (int)($resizeHeight * $sourceAspectRatio);
            } else {// For Tall Image
                $nWidth = $resizeWidth;
                $nHeight = (int)($resizeWidth / $sourceAspectRatio);
            }
        }

        $tmpImg = imagecreatetruecolor($nWidth, $nHeight);
        $fillColor = imagecolorallocate($tmpImg, 255, 255, 255);
        imagefill($tmpImg, 0, 0, $fillColor);
        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
            imagealphablending($tmpImg, false);
            imagesavealpha($tmpImg, true);
            $transparent = imagecolorallocatealpha($tmpImg, 0, 0, 0, 127);
            imagefilledrectangle($tmpImg, 0, 0, $nWidth, $nHeight, $transparent);
        }

        if ($nWidth >= $sourceWidth && $nHeight >= $sourceHeight) {
            imagecopyresampled($tmpImg, $image, round(($nWidth / 2) - ($sourceWidth / 2)), round(($nHeight / 2) - ($sourceHeight / 2)), 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);
        } else if ($nWidth <= $sourceWidth && $nHeight >= $sourceHeight) {
            imagecopyresampled($tmpImg, $image, 0, round(($nHeight / 2) - ($sourceHeight / 2)), 0, 0, $nWidth, $sourceHeight, $sourceWidth, $sourceHeight);
        } else if ($nWidth >= $sourceWidth && $nHeight <= $sourceHeight) {
            imagecopyresampled($tmpImg, $image, round(($nWidth / 2) - ($sourceWidth / 2)), 0, 0, 0, $nWidth, $sourceHeight, $sourceWidth, $sourceHeight);
        } else {
            imagecopyresampled($tmpImg, $image, 0, 0, 0, 0, $nWidth, $nHeight, $sourceWidth, $sourceHeight);
        }

        $newImg = imagecreatetruecolor($resizeWidth, $resizeHeight);
        $fillColorNew = imagecolorallocate($newImg, 255, 255, 255);
        imagefill($newImg, 0, 0, $fillColorNew);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
            imagefilledrectangle($newImg, 0, 0, $resizeWidth, $resizeHeight, $transparent);
        }

        if ($resizeWidth >= $nWidth && $resizeHeight >= $nHeight) {
            imagecopyresampled($newImg, $tmpImg, round(($resizeWidth / 2) - ($nWidth / 2)), round(($resizeHeight / 2) - ($nHeight / 2)), 0, 0, $nWidth, $nHeight, $nWidth, $nHeight);
        } else if ($resizeWidth <= $nWidth && $resizeHeight >= $nHeight) {
            imagecopyresampled($newImg, $tmpImg, 0, round(($resizeHeight / 2) - ($nHeight / 2)), 0, 0, $resizeWidth, $nHeight, $nWidth, $nHeight);
        } else if ($resizeWidth >= $nWidth && $resizeHeight <= $nHeight) {
            imagecopyresampled($newImg, $tmpImg, round(($resizeWidth / 2) - ($nWidth / 2)), 0, 0, 0, $resizeWidth, $nHeight, $nWidth, $nHeight);
        } else {
            imagecopyresampled($newImg, $tmpImg, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $nWidth, $nHeight);
        }

        //Generate the file, and rename it to $newfilename
        if (!imagejpeg($newImg, $newFilename, 91)) {
            trigger_error('Failed resize image!', E_USER_WARNING);
        }
        imagedestroy($image);
        imagedestroy($tmpImg);
        imagedestroy($newImg);
        return $newFilename;
    }

    public function rotateImage($srcImage, $rotationAngle = 0, $dstImage = "") {
        if (($rotationAngle < 0) || ($rotationAngle > 360)) {
            trigger_error("Error, rotation angle passed out of range: [0,360]", E_USER_WARNING);
        }

        $imgInfo = getimagesize($srcImage);
        $rotatedImage = imagecreatetruecolor($imgInfo[0], $imgInfo[1]);
        switch ($imgInfo[2]) {
            case 1:
                $rotatedImage = imagecreatefromgif($srcImage);
                break;
            case 2:
                $rotatedImage = imagecreatefromjpeg($srcImage);
                break;
            case 3:
                $rotatedImage = imagecreatefrompng($srcImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        $rotatedImage = imagerotate($rotatedImage, $rotationAngle, -1);
        imagealphablending($rotatedImage, false);
        imagesavealpha($rotatedImage, true);
        if (empty($dstImage)) {
            $dstImage = $srcImage;
        }
        switch ($imgInfo[2]) {
            case 1:
                imagegif($rotatedImage, $dstImage);
                break;
            case 2:
                imagejpeg($rotatedImage, $dstImage, 91);
                break;
            case 3:
                imagepng($rotatedImage, $dstImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        imagedestroy($rotatedImage);
        return $dstImage;
    }

    /**
     *
     * @param string $srcImage
     * @param int    $flipType 0: Vertical, 1: Horizontal
     * @param string $dstImage
     *
     * @return string
     */
    public function flipImage($srcImage, $flipType = 0, $dstImage = "") {
        if ($flipType != 0 && $flipType != 1) {
            trigger_error("Error, invalid flip type!", E_USER_WARNING);
        }
        $imgInfo = getimagesize($srcImage);
        $sizeX = $imgInfo[0];
        $sizeY = $imgInfo[1];

        switch ($imgInfo[2]) {
            case 1:
                $flippedImage = imagecreatefromgif($srcImage);
                $orgImage = imagecreatefromgif($srcImage);
                break;
            case 2:
                $flippedImage = imagecreatefromjpeg($srcImage);
                $orgImage = imagecreatefromjpeg($srcImage);
                break;
            case 3:
                $flippedImage = imagecreatefrompng($srcImage);
                $orgImage = imagecreatefrompng($srcImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        imagecolortransparent($flippedImage, imagecolorallocate($flippedImage, 0, 0, 0));
        imagealphablending($flippedImage, false);
        imagesavealpha($flippedImage, true);
        if ($flipType == 0) {
            $isFlipped = imagecopyresampled($flippedImage, $orgImage, 0, 0, 0, ($sizeY - 1), $sizeX, $sizeY, $sizeX, 0 - $sizeY);
        } else {
            $isFlipped = imagecopyresampled($flippedImage, $orgImage, 0, 0, ($sizeX - 1), 0, $sizeX, $sizeY, 0 - $sizeX, $sizeY);
        }
        if (!$isFlipped) {
            trigger_error('Unable to flip an image!', E_USER_WARNING);
        }
        if (empty($dstImage)) {
            $dstImage = $srcImage;
        }
        switch ($imgInfo[2]) {
            case 1:
                imagegif($flippedImage, $dstImage);
                break;
            case 2:
                imagejpeg($flippedImage, $dstImage, 91);
                break;
            case 3:
                imagepng($flippedImage, $dstImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        imagedestroy($flippedImage);
        imagedestroy($orgImage);
        return $dstImage;
    }

    public function addWatermarkImage($srcImage, $watermarkImage, $dstImage = "", $transparency = 100, $position = 0, $watermarkRotation = 0, $resizeWatermark = false) {
        $isWatermarkModified = false;
        $tmpWatermarkImageName = str_replace(basename($watermarkImage), 'tmp-' . basename($watermarkImage), $watermarkImage);
        $transparency /= 100;
        $srcImgInfo = getimagesize($srcImage);
        $srcImgWidth = $srcImgInfo[0];
        $srcImgHeight = $srcImgInfo[1];
        //$gdSrcImage = imagecreatetruecolor($srcImgWidth, $srcImgHeight);
        //$gdWatermarkedImage = imagecreatetruecolor($srcImgWidth, $srcImgHeight);
        switch ($srcImgInfo[2]) {
            case 1:
                $gdSrcImage = imagecreatefromgif($srcImage);
                $gdWatermarkedImage = imagecreatefromgif($srcImage);
                break;
            case 2:
                $gdSrcImage = imagecreatefromjpeg($srcImage);
                $gdWatermarkedImage = imagecreatefromjpeg($srcImage);
                break;
            case 3:
                $gdSrcImage = imagecreatefrompng($srcImage);
                $gdWatermarkedImage = imagecreatefrompng($srcImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        //imagecolortransparent($gdWatermarkedImage, imagecolorallocate($gdWatermarkedImage, 0, 0, 0));
        //imagealphablending($gdWatermarkedImage, false);
        //imagesavealpha($gdWatermarkedImage, true);
        if (($watermarkRotation < 0) || ($watermarkRotation > 360)) {
            trigger_error("Error, rotation angle passed out of range: [0,360]", E_USER_WARNING);
        } else if ($watermarkRotation != 0) {
            $watermarkImage = $this->rotateImage($watermarkImage, $watermarkRotation, $tmpWatermarkImageName);
            $isWatermarkModified = true;
        }
        $wtImgInfo = getimagesize($watermarkImage);
        $wtImgWidth = $wtImgInfo[0];
        $wtImgHeight = $wtImgInfo[1];
        if (is_array($resizeWatermark)) {
            $watermarkImage = $this->resizeImage($watermarkImage, $resizeWatermark['width'], $resizeWatermark['height'], $tmpWatermarkImageName);
            $isWatermarkModified = true;
        } else if ('autofitheight' == $resizeWatermark) {
            $watermarkImage = $this->resizeImage($watermarkImage, $wtImgWidth, $srcImgHeight, $tmpWatermarkImageName);
            $isWatermarkModified = true;
        } else if ('autofitwidth' == $resizeWatermark) {
            $watermarkImage = $this->resizeImage($watermarkImage, $srcImgWidth, $wtImgHeight, $tmpWatermarkImageName);
            $isWatermarkModified = true;
        } else if ('autofit' == $resizeWatermark) {
            $watermarkImage = $this->resizeImage($watermarkImage, $srcImgWidth, $srcImgHeight, $tmpWatermarkImageName);
            $isWatermarkModified = true;
        }

        $wtImgInfo = getimagesize($watermarkImage);
        $wtImgWidth = $wtImgInfo[0];
        $wtImgHeight = $wtImgInfo[1];
        //$gdMaskImage = imagecreatetruecolor($wtImgWidth, $wtImgHeight);
        switch ($wtImgInfo[2]) {
            case 1:
                $gdMaskImage = imagecreatefromgif($watermarkImage);
                break;
            case 2:
                $gdMaskImage = imagecreatefromjpeg($watermarkImage);
                break;
            case 3:
                $gdMaskImage = imagecreatefrompng($watermarkImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        //imagecolortransparent($gdMaskImage, imagecolorallocate($gdMaskImage, 0, 0, 0));
        //imagealphablending($gdMaskImage, true);
        //imagesavealpha($gdMaskImage, true);

        if (is_array($position)) {
            $srcImgMinX = $position['x'];
            $srcImgMinY = $position['y'];
        } else {
            switch ($position) {
                case 1:
                    $srcImgMinX = 5;
                    $srcImgMinY = 5;
                    break;
                case 2:
                    $srcImgMinX = floor(($srcImgWidth - $wtImgWidth - 5));
                    $srcImgMinY = 5;
                    break;
                case 3:
                    $srcImgMinX = floor(($srcImgWidth - $wtImgWidth - 5));
                    $srcImgMinY = floor(($srcImgHeight - $wtImgHeight - 5));
                    break;
                case 4:
                    $srcImgMinX = 5;
                    $srcImgMinY = floor(($srcImgHeight - $wtImgHeight - 5));
                    break;
                default:
                    $srcImgMinX = floor(($srcImgWidth / 2) - ($wtImgWidth / 2));
                    $srcImgMinY = floor(($srcImgHeight / 2) - ($wtImgHeight / 2));
                    break;
            }
        }
        //imagecopyresampled( $gdWatermarkedImage, $gdSrcImage,  0, 0, 0, 0, $srcImgWidth, $srcImgHeight, $srcImgWidth, $srcImgHeight);
        //imagecopyresampled( $gdMaskImage, $gdMaskImage,  0, 0, 0, 0, $wtImgWidth, $wtImgHeight, $wtImgWidth, $wtImgHeight);
        //imagecopymerge($gdWatermarkedImage, $gdMaskImage, $srcImgMinX, $srcImgMinY, 0, 0, $wtImgWidth, $wtImgHeight, 100);
        //imagetruecolortopalette($gdWatermarkedImage, true, 256);
        //imagecopyresampled( $gdWatermarkedImage, $gdMaskImage,  $srcImgMinX, $srcImgMinY, 0, 0, $srcImgWidth, $srcImgHeight, $srcImgWidth, $srcImgHeight);

        for ($y = 0; $y < $srcImgHeight; $y++) {
            for ($x = 0; $x < $srcImgWidth; $x++) {
                $returnColor = null;
                $watermarkX = $x - $srcImgMinX;
                $watermarkY = $y - $srcImgMinY;
                $mainRGB = imagecolorsforindex($gdSrcImage, imagecolorat($gdSrcImage, $x, $y));
                if ($watermarkX >= 0 && $watermarkX < $wtImgWidth && $watermarkY >= 0 && $watermarkY < $wtImgHeight) {
                    $watermarkRGB = imagecolorsforindex($gdMaskImage, imagecolorat($gdMaskImage, $watermarkX, $watermarkY));
                    $watermarkAlpha = round(((127 - $watermarkRGB['alpha']) / 127), 2);
                    $watermarkAlpha = $watermarkAlpha * $transparency;
                    $avgRed = $this->__getAveColor($mainRGB['red'], $watermarkRGB['red'], $watermarkAlpha);
                    $avgGreen = $this->__getAveColor($mainRGB['green'], $watermarkRGB['green'], $watermarkAlpha);
                    $avgBlue = $this->__getAveColor($mainRGB['blue'], $watermarkRGB['blue'], $watermarkAlpha);
                    $returnColor = $this->__getImageColor($gdWatermarkedImage, $avgRed, $avgGreen, $avgBlue);
                } else {
                    $returnColor = imagecolorat($gdSrcImage, $x, $y);
                }
                imagesetpixel($gdWatermarkedImage, $x, $y, $returnColor);
            }
        }

        if (empty($dstImage)) {
            $dstImage = $srcImage;
        }
        switch ($srcImgInfo[2]) {
            case 1:
                imagegif($gdWatermarkedImage, $dstImage);
                break;
            case 2:
                imagejpeg($gdWatermarkedImage, $dstImage, 91);
                break;
            case 3:
                imagepng($gdWatermarkedImage, $dstImage);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }
        imagedestroy($gdSrcImage);
        imagedestroy($gdMaskImage);
        imagedestroy($gdWatermarkedImage);
        if (!empty($isWatermarkModified)) {
            unlink($tmpWatermarkImageName);
        }
        return $gdWatermarkedImage;
    }

    public function autoRotateImage($img, $newFilename) {
        $exifData = $this->__getImageEXIFHeaders($img);
        if (!empty($exifData) && !empty($exifData['IFD0']['Orientation'])) {
            $flipImage = false;
            $flipType = 0; //0: Vertical, 1: Horizontal
            $rotateImage = false;
            $rotationAngle = 0;
            switch ($exifData['IFD0']['Orientation']) {
                case 1: // nothing
                    break;

                case 2: // horizontal flip
                    $flipImage = true;
                    $flipType = 0;
                    break;

                case 3: // 180 rotate left
                    $rotateImage = true;
                    $rotationAngle = 180;
                    break;

                case 4: // vertical flip
                    $flipImage = true;
                    $flipType = 0;
                    break;

                case 5: // vertical flip + 90 rotate right
                    $flipImage = true;
                    $flipType = 0;
                    $rotateImage = true;
                    $rotationAngle = 270;
                    break;

                case 6: // 90 rotate right
                    $rotateImage = true;
                    $rotationAngle = 270;
                    break;

                case 7: // horizontal flip + 90 rotate right
                    $flipImage = true;
                    $flipType = 1;
                    $rotateImage = true;
                    $rotationAngle = 270;
                    break;

                case 8: // 90 rotate left
                    $rotateImage = true;
                    $rotationAngle = 90;
                    break;
            }

            if (empty($newFilename)) {
                $newFilename = $img;
            }

            $processedImg = $img;

            if ($flipImage) {
                $processedImg = $this->flipImage($processedImg, $flipType, $newFilename);
            }

            if ($rotateImage) {
                $processedImg = $this->rotateImage($processedImg, $rotationAngle, $newFilename);
            }

            $img = $processedImg;
        }
        return $img;
    }

    private function __getImageEXIFHeaders($img) {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($img, 0, true);
            if (!empty($exif)) {
                return $exif;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function __getAveColor($colorA, $colorB, $alphaLevel) {
        return round((($colorA * (1 - $alphaLevel)) + ($colorB * $alphaLevel)));
    }

    private function __getImageColor($im, $r, $g, $b) {
        $c = imagecolorexact($im, $r, $g, $b);
        if ($c != -1) {
            return $c;
        }
        $c = imagecolorallocate($im, $r, $g, $b);
        if ($c != -1) {
            return $c;
        }
        return imagecolorclosest($im, $r, $g, $b);
    }

}
