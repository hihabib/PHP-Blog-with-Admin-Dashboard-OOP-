<?php

/**
 * class Utility
 * 
 * The Utility class contains necessary utility methods. All methods are static.
 */
class Utility
{
    /**
     * Compress Image
     *
     * @param string $inputPath Path of input image
     * @param string $outputPath Path of output image
     * @param int|null $outputWidth Output image width. Height will be calculated automically maintaining the aspect ratio if the $outputHeight = null, which is $output height's default value. If both $outputWidth and $outputHeight is null, the final image's height and width will stay same. 
     * @param int|null $outputHeight Output image height. Width will be calculated automically maintaining the aspect ratio if $outputWidth is null. If both $outputWidth and $outputHeight is null, the final image's height and width will stay same. 
     * @param int $quality quality percentage. 
     * 
     * @return bool returns true if the image compress successfully. Otheresize return false
     * 
     */
    public static function compressAndResizeImage(string $inputPath, string $outputPath, int|null $outputWidth = null, int|null $outputHeight = null, int $quality = 60): bool
    {
        // Get the image file extension
        $extension = pathinfo($inputPath, PATHINFO_EXTENSION);

        // Load the original image based on its type
        switch ($extension) {
            case 'jpg':
                $source = imagecreatefromjpeg($inputPath);
                break;
            case 'jpeg':
                $source = imagecreatefromjpeg($inputPath);
                break;
            case 'png':
                $source = imagecreatefrompng($inputPath);
                break;
            case 'webp':
                $source = imagecreatefromwebp($inputPath);
                break;
            default:
                return false; // Unsupported image format
        }

        // Get the original image dimensions
        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        
        // Calculate new dimensions while maintaining the aspect ratio

        // if only $outputWidth is given and $outputHeight is null
        if($outputWidth != null && $outputHeight == null){
            $newWidth = $outputWidth;
            $newHeight = round(($originalHeight / $originalWidth) * $newWidth);
        }

        // if only $outputHeight is given and $outputWidth is null
        if($outputWidth == null && $outputHeight != null){
            $newHeight = $outputHeight;
            $newWidth = round(($originalWidth / $originalHeight) * $newHeight);
        }

        // if both $outputHeight and $outputWidth are given
        if($outputWidth != null && $outputHeight != null){
            $newHeight = $outputHeight;
            $newWidth = $outputWidth;
        }
        
        // Create a new image with the desired dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Resize the image
        imagecopyresampled($resizedImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        imagepalettetotruecolor($resizedImage);
        imagealphablending($resizedImage, true);
        imagesavealpha($resizedImage, true);

        // Save the resized and compressed image
        $success = imagewebp($resizedImage, $outputPath, $quality);

        // Free up memory
        imagedestroy($source);
        imagedestroy($resizedImage);

        return $success;
    }
}
