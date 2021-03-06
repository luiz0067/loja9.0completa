<?php

class ISC_IMAGE_LIBRARY_FACTORY_NOPHPSUPPORT_EXCEPTION extends Exception {

	public function __construct ($filePath)
	{
		parent::__construct("No image libraries that support the file at " . $filePath . " could be found in this PHP installation. Image library extensions may not be enabled, or the enabled extensions may not support this file type.");
	}
}

class ISC_IMAGE_LIBRARY_FACTORY_FILEDOESNTEXIST_EXCEPTION extends Exception {

	public function __construct ($filePath)
	{
		parent::__construct("The file " . $filePath . " does not exist.");
	}
}

class ISC_IMAGE_LIBRARY_FACTORY_INVALIDIMAGEFILE_EXCEPTION extends Exception {

	public function __construct ($filePath)
	{
		parent::__construct("The file at " . $filePath . " is not a valid image file.");
	}
}

class ISC_IMAGE_LIBRARY_FACTORY {

	/**
	* Checks the file at $filePath to see if it's a valid image of any image type
	*
	* @param bool $filePath False if not a valid image, otherwise true.
	*/
	public static function isValidImageFile ($filePath)
	{
		$result = @getimagesize($filePath);
		return ($result !== false);
	}

	/**
	* Returns an array of IMAGETYPE_XXX constants that are supported by the image manipulation libraries in the current PHP installation.
	*
	* @return array
	*/
	public static function getSupportedImageTypes ()
	{
		$types = array();

		if (ISC_IMAGE_LIBRARY_IMAGICK::isLibrarySupported()) {
			$types = array_merge($types, ISC_IMAGE_LIBRARY_IMAGICK::getSupportedImageTypes());
		}

		if (ISC_IMAGE_LIBRARY_GD::isLibrarySupported()) {
			$types = array_merge($types, ISC_IMAGE_LIBRARY_GD::getSupportedImageTypes());
		}

		return array_unique($types);
	}

	/**
	* Returns an array of file extensions that are supported by the image manipulation libraries in the current PHP installation.
	*
	* @return array
	*/
	public static function getSupportedImageExtensions ()
	{
		$types = self::getSupportedImageTypes();
		$extensions = array();

		foreach ($types as $type) {
			$extensions = array_merge($extensions, self::getExtensionsForImageType($type));
		}

		return $extensions;
	}

	/**
	* Returns one, preferred file
	*
	* @param mixed $imageType
	*/
	public static function getExtensionForImageType ($imageType)
	{
		$extensions = self::getExtensionsForImageType($imageType);
		return $extensions[0];
	}

	/**
	* Returns all file extensions for a given IMAGETYPE_XXX constant. Returned as an array as some image types have multiple common extensions.
	*
	* @param int $imageType One of IMAGETYPE_XXX constants.
	* @param bool $uncommon If set to true will also return uncommon but known extensions for a the given image type (such as JFIF variations for the JPEG image type). Default is false.
	* @return array
	*/
	public static function getExtensionsForImageType ($imageType, $uncommon = false)
	{
		// don't use image_type_to_extension because it was defined in 5.2, not "5" as per php docs, it returns capitals but we prefer lowercase, it returns 'jpeg' but we prefer 'jpg' and it returns 'bmp' for IMAGETYPE_WBMP which is just plain wrong
		// the arrays below should be in order of preference as getExtensionForImageType will return the first element
		switch ($imageType) {
			case IMAGETYPE_BMP:
				if ($uncommon) {
					return array('bmp', 'dib');
				} else {
					return array('bmp');
				}
				break;

			case IMAGETYPE_GIF:
				return array('gif');
				break;

			case IMAGETYPE_JPEG:
				if ($uncommon) {
					return array('jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi');
				} else {
					return array('jpg', 'jpeg', 'jpe');
				}
				break;

			case IMAGETYPE_PNG:
				return array('png');
				break;

			case IMAGETYPE_WBMP:
				return array('wbmp');
				break;

			case IMAGETYPE_XBM:
				return array('xbm');
				break;

			default:
				// for development timeframe reasons, I've only coded for GD-supported types so far
				throw new Exception('Invalid IMAGETYPE_XXX specified.');
				break;
		}
	}

	/**
	* Returns an image manipulation library that is capable of handling the image file at $filePath -- will detect the image type and return the appropriate library from those available on the server
	*
	* @param string $filePath
	* @param bool $setInstanceFilePath
	* @return ISC_IMAGE_LIBRARY_INTERFACE
	* @throws ISC_IMAGE_LIBRARY_FACTORY_INVALIDIMAGEFILE_EXCEPTION If the image file at $filePath is not a valid image
	* @throws ISC_IMAGE_LIBRARY_FACTORY_NOPHPSUPPORT_EXCEPTION If there were no image libraries installed that could support the image
	*/
	public static function getImageLibraryInstance ($filePath = null)
	{
		if ($filePath) {
			if (!file_exists($filePath)) {
				throw new ISC_IMAGE_LIBRARY_FACTORY_FILEDOESNTEXIST_EXCEPTION($filePath);
			}

			if (!self::isValidImageFile($filePath)) {
				throw new ISC_IMAGE_LIBRARY_FACTORY_INVALIDIMAGEFILE_EXCEPTION($filePath);
			}
		}

		// was going to use an array of classnames and call_user_func but it's a bit slow, and how often are libraries added? rarely
		// use direct calls instead

		try {
			if (ISC_IMAGE_LIBRARY_IMAGICK::isLibrarySupported() && (!$filePath || ISC_IMAGE_LIBRARY_IMAGICK::isFileSupported($filePath))) {
				$instance = new ISC_IMAGE_LIBRARY_IMAGICK($filePath);
			} else if (ISC_IMAGE_LIBRARY_GD::isLibrarySupported() && (!$filePath || ISC_IMAGE_LIBRARY_GD::isFileSupported($filePath))) {
				$instance = new ISC_IMAGE_LIBRARY_GD($filePath);
			} else {
				throw new ISC_IMAGE_LIBRARY_FACTORY_NOPHPSUPPORT_EXCEPTION($filePath);
			}
		} catch (ISC_IMAGE_BASECLASS_INVALIDIMAGE_EXCEPTION $exception) {
			throw new ISC_IMAGE_LIBRARY_FACTORY_INVALIDIMAGEFILE_EXCEPTION($filePath);
		}

		return $instance;
	}
}
