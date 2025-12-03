<?php
/**
 * Create a resized thumbnail preserving aspect ratio.
 * Supports JPEG, PNG, and WEBP.
 *
 * @param string $srcPath Absolute path to source image
 * @param string $destPath Absolute path to write thumbnail
 * @param int $maxWidth Maximum width of thumbnail in pixels
 * @throws Exception on unsupported type or missing GD functions
 */
function create_image_thumbnail($srcPath, $destPath, $maxWidth = 400)
{
    if (!file_exists($srcPath)) {
        throw new Exception('Source image not found');
    }

    $info = getimagesize($srcPath);
    if (!$info) throw new Exception('Unable to read image info');

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    // Calculate new size
    if ($width <= $maxWidth) {
        // No need to resize, copy
        if (!copy($srcPath, $destPath)) {
            throw new Exception('Failed to copy image for thumbnail');
        }
        return;
    }

    $ratio = $height / $width;
    $newWidth = $maxWidth;
    $newHeight = (int)($newWidth * $ratio);

    // Create source image resource
    switch ($mime) {
        case 'image/jpeg':
            $srcImg = @imagecreatefromjpeg($srcPath);
            break;
        case 'image/png':
            $srcImg = @imagecreatefrompng($srcPath);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) throw new Exception('WEBP not supported by GD');
            $srcImg = @imagecreatefromwebp($srcPath);
            break;
        default:
            throw new Exception('Unsupported image type: ' . $mime);
    }

    if (!$srcImg) throw new Exception('Failed to create image resource from ' . $mime);

    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and WEBP
    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save thumbnail
    $saved = false;
    switch ($mime) {
        case 'image/jpeg':
            $saved = imagejpeg($thumb, $destPath, 85);
            break;
        case 'image/png':
            $saved = imagepng($thumb, $destPath, 6);
            break;
        case 'image/webp':
            $saved = imagewebp($thumb, $destPath, 85);
            break;
    }

    imagedestroy($srcImg);
    imagedestroy($thumb);

    if (!$saved) throw new Exception('Failed to save thumbnail');
}

?>
