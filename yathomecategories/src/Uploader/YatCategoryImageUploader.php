<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

declare(strict_types=1);

namespace Yateo\Yathomecategories\Uploader;

use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class YatCategoryImageUploader
 * @package Yateo\Yathomecategories\Uploader
 */
class YatCategoryImageUploader implements ImageUploaderInterface
{
    public const IMAGE_PATH = 'yathomecategories/views/images/';
    
    public $imagetimes = "";
    public $old_imagetimes = "";


    /**
     * @param int $itemId
     * @param UploadedFile $image
     * @param string $imagenames
     */
    public function upload($itemId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);

        $destination = _PS_MODULE_DIR_ . self::IMAGE_PATH . $itemId.'_'.$this->imagetimes.'.jpg';
        $old_destination = _PS_MODULE_DIR_ . self::IMAGE_PATH . $itemId.'_'.$this->old_imagetimes.'.jpg';
        
        $this->deleteOldImage($old_destination);
        
        $cacheImage = 'yathomecategories_mini_' . $itemId.'.jpg';
        if (file_exists(_PS_TMP_IMG_DIR_ . $cacheImage)) {
            @unlink(_PS_TMP_IMG_DIR_ . $cacheImage);
        }
        
        $this->uploadFromTemp($tempImageName, $destination);
    }

    /**
     * Deletes old image
     *
     * @param $itemId
     */
    private function deleteOldImage($destination)
    {
        if (file_exists($destination)) {
            unlink($destination);
        }
    }

    /**
     * Creates temporary image from uploaded file
     *
     * @param UploadedFile $image
     *
     * @throws ImageUploadException
     *
     * @return string
     */
    protected function createTemporaryImage(UploadedFile $image)
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName || !move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('Failed to create temporary image file');
        }

        return $temporaryImageName;
    }

    /**
     * Uploads resized image from temporary folder to image destination
     *
     * @param $temporaryImageName
     * @param $destination
     *
     * @throws ImageUploadException
     * @throws MemoryLimitException
     */
    protected function uploadFromTemp($temporaryImageName, $destination)
    {
        if (!\ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }

        if (!\ImageManager::resize($temporaryImageName, $destination)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        unlink($temporaryImageName);
    }

    

    /**
     * Check if image is allowed to be uploaded.
     *
     * @param UploadedFile $image
     *
     * @throws UploadedImageConstraintException
     */
    protected function checkImageIsAllowedForUpload(UploadedFile $image)
    {
        $maxFileSize = \Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && $image->getSize() > $maxFileSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $maxFileSize, $image->getSize()), UploadedImageConstraintException::EXCEEDED_SIZE);
        }

        if (!\ImageManager::isRealImage($image->getPathname(), $image->getClientMimeType())
            || !\ImageManager::isCorrectImageFileExt($image->getClientOriginalName())
            || preg_match('/\%00/', $image->getClientOriginalName()) // prevent null byte injection
        ) {
            throw new UploadedImageConstraintException(sprintf('Image format "%s", not recognized, allowed formats are: .gif, .jpg, .png', $image->getClientOriginalExtension()), UploadedImageConstraintException::UNRECOGNIZED_FORMAT);
        }
    }


    /**
     * Set the value of imagetimes
     */
    public function setImagetimes(string $imagetimes): self
    {
        $this->imagetimes = $imagetimes;

        return $this;
    }


    /**
     * Set the value of old_imagetimes
     */
    public function setOldImagetimes($old_imagetimes): self
    {
        $this->old_imagetimes = $old_imagetimes;

        return $this;
    }
}