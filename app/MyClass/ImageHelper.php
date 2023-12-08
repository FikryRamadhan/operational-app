<?php

namespace App\MyClass;

use Illuminate\Support\Str;
use Image;

class ImageHelper
{
	private $image;

	private $pathFile;

	private $deleteImageAfter;

	private $pathDeleteFile = "";

	/**
	 * make image
	 */
	public function __construct($image, string $pathFile, bool $deleteImageAfter = false)
	{
		$this->image = Image::make($image);
		$this->pathFile = "storage/{$pathFile}";
		$this->deleteImageAfter = $deleteImageAfter;
		$this->pathDeleteFile = "public/{$pathFile}";
	}

	public function __destruct()
	{
		// Deleting image after
		$this->deleteImage();
	}

	/**
	 * compress image
	 */
	public function compressImage($width, $height = null)
    {
        $this->image->resize($width, $height, function($constraint){
            $constraint->aspectRatio();
        });

        return $this;
    }

    public function addTextStudentAttendanceImage(array $data)
    {
        $presentDate = "{$data['day']}, {$data['date']}";

        // Check if the "mobile" word exists in User-Agent
        $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

        // Check if the "tablet" word exists in User-Agent
        $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet"));

        // Platform check
        $isWin = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows"));
        $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android"));
        $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone"));
        $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad"));
        $isIOS = $isIPhone || $isIPad;

        if($isMob){
            if($isTab){
                // tablet
                $xAxis = 15;
                $yAxis1 = 269;
                $yAxis2 = 277;
                $yAxis3 = 288;
                $yAxis4 = 297;
                $yAxis5 = 308;
            }else{
                // mobile
                $xAxis = 20;
                $yAxis1 = 29;
                $yAxis2 = 37;
                $yAxis3 = 48;
                $yAxis4 = 57;
                $yAxis5 = 68;
            }
            }else{
                // dekstop
                $xAxis = 15;
                $yAxis1 = 269;
                $yAxis2 = 277;
                $yAxis3 = 288;
                $yAxis4 = 297;
                $yAxis5 = 308;
            }

        $options = function($font) {
            $font->file(public_path('assets/fonts/open-sans/OpenSans-Regular.ttf'));
            $font->size(8);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('top');
        };

        $this->image->text($data['student_code'], $xAxis, $yAxis1, $options)
            ->text($data['full_name'], $xAxis, $yAxis2, $options)
            ->text($data['classroom_name'], $xAxis, $yAxis3, $options)
            ->text($presentDate, $xAxis, $yAxis4, $options)
            ->text($data['time'], $xAxis, $yAxis5, $options);

        return $this;
    }

    public function addTextStudentAttendanceImageApi(array $data)
    {
        $presentDate = "{$data['day']}, {$data['date']}";
        // mobile
        $xAxis = 20;
        $yAxis1 = 29;
        $yAxis2 = 37;
        $yAxis3 = 48;
        $yAxis4 = 57;
        $yAxis5 = 68;

        $options = function($font) {
            $font->file(public_path('assets/fonts/open-sans/OpenSans-Regular.ttf'));
            $font->size(8);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('top');
        };

        $this->image->text($data['student_code'], $xAxis, $yAxis1, $options)
            ->text($data['full_name'], $xAxis, $yAxis2, $options)
            ->text($data['classroom_name'], $xAxis, $yAxis3, $options)
            ->text($presentDate, $xAxis, $yAxis4, $options)
            ->text($data['time'], $xAxis, $yAxis5, $options);

        return $this;
    }

    public function saveImage()
    {
    	$this->image->save($this->pathFile);

    	return $this;
    }

    public function deleteImage()
    {
    	if($this->deleteImageAfter && $this->isPathDeleteFileExist()) {
			\App\MyClass\Helper::deleteFile($this->pathDeleteFile);
		}

		return $this;
    }

    public function isPathDeleteFileExist() : bool
    {
    	return empty($this->pathDeleteFile) === false;
    }

    public function getFullPathFile() : string
    {
        return public_path($this->pathFile);
    }

}
