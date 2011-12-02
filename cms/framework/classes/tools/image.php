<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

class Tools_Image {

    public static $cmd_convert = null;

    public static function _init() {
        static::$cmd_convert = \Config::get('cmd_convert');
    }

    /**
     *
     * @param  string  $source      a
     * @param  int     $max_width   Maximum width
     * @param  int     $max_height  Maximum height
     * @param  string  $dest        Destination file
     * @return bool
     */
    public static function resize($source, $max_width = null, $max_height = null, $dest = null) {

        $image_info = @getimagesize($source);
        list($width, $height, $image_type, ) = $image_info;

        if (!in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
            throw new \Exception(__('The format of this image is not allowed.'));
        }

        list($new_width, $new_height, $ratio) = static::calculate_ratio($width, $height, $max_width, $max_height);

        $resize = $ratio != 1;
        // If color space is not RGB and resize method is the convert command-line binary, resize anyway (we want a RGB color space)
        if ($image_type == IMAGETYPE_JPEG && $image_info['channels'] != 3 && !is_null(static::$cmd_convert)) {
            $resize = true;
        }

        if (!$resize && empty($dest)) {
            return true;
        }
        if (!is_writeable(dirname($dest))) {
            throw new \Exception(__('Destination directory is not writeable.'));
        }
        if (!$resize && !@copy($source, $dest)) {
            throw new \Exception(__('An error occured when copying the image.'));
        }

        // Use the convert command-line binary when available
        if (!is_null(static::$cmd_convert)) {
            static::_resize_convert($source, $new_width, $new_height, $dest, $ratio > 4 ? 2 : 1);
            return true;
        }
        // Fallback to GD if not
        static::_resize_gd($image_info, $source, $new_width, $new_height, $dest);
        return true;
    }

    /**
     * Calculates aspect-ratio from an original size to a maximum size
     *
     * @param  int  $orig_width   Original width
     * @param  int  $orig_height  Original height
     * @param  int  $max_width    Max width
     * @param  int  $max_height   Max height
     * @return array  0: width, 1: height, 2: ratio
     */
    public static function calculate_ratio($orig_width, $orig_height, $max_width = null, $max_height = null) {

        $dont_resize =
            ($orig_width <= $max_width && $orig_height <= $max_height) ||
            (empty($max_width) && $orig_height <= $max_height) ||
            (empty($max_height) && $orig_width <= $max_width) ||
            (empty($max_width) && empty($max_height));

        if ($dont_resize) {
            return array($orig_width, $orig_height, 1);
        } else {
            $ratio_width  = $max_width / $orig_width;
            $ratio_height =  $max_height / $orig_height;
            if (empty($max_width) || (!empty($max_height) && $ratio_width > $ratio_height)) {
                return array((int) round($orig_width * $ratio_height), $max_height, $ratio_height);
            } else {
                return array($max_width, (int) round($orig_height * $ratio_width), $ratio_width);
            }
        }
    }

    /**
     * Resize an image using the convert command-line binary
     *
     * @param type $source
     * @param type $max_width
     * @param type $max_height
     * @param type $dest
     * @param type $iteration_count
     */
    protected static function _resize_convert($source, $max_width, $max_height, $dest, $iteration_count = 1) {

        for ($i = $iteration_count; $i >= 1; $i--) {
            if ($i < $iteration_count) {
                $source = $dest;
            }

            $cmd = strtr("(:cmd) -size (:size) (:source) -colorspace RGB + profile 'icc' (:antialias) -geometry (:size)\\> (:dest)", array(
                '(:cmd)'       => static::$cmd_convert,
                '(:size)'      => $max_width * $i, $max_height * $i,
                '(:source)'    => $source,
                '(:dest)'      => $dest,
                '(:antialias)' => $i == $iteration_count ? '+antialias' : '',
            ));

            system($cmd, $return_value);

            if ($return_value != 0) {
                throw new \Exception(__('An error occured when resizing the image.'));
            }
        }
    }

    /**
     * Resize an image using the GD library
     *
     * @param type $image_info
     * @param type $source
     * @param type $width
     * @param type $height
     * @param type $dest
     */
    protected static function _resize_gd($image_info, $source, $width, $height, $dest) {

        $image_type = $image_info[2];

        if (!in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
            throw new \Exception(__('The format of this image is not allowed.'));
        }

        static $create_resource = array(
            IMAGETYPE_GIF  => 'imagecreatefromgif',
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG  => 'imagecreatefrompng',
        );
        static $save_resource = array(
            IMAGETYPE_GIF  => 'imagegif',
            IMAGETYPE_JPEG => 'imagejpeg',
            IMAGETYPE_PNG  => 'imagepng',
        );

        $old_img = $create_resource[$image_type]($source);
        $new_img = imagecreatetruecolor($width, $height);

        // Allow transparency
        if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_PNG))) {
            imagealphablending($new_img, false);
            imagesavealpha($new_img, true);
            $transparent = imagecolorallocatealpha($new_img, 255, 255, 255, 127);
            imagefilledrectangle($new_img, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($new_img, $old_img, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
        $save_resource[$image_type]($new_img, $dest);

        imagedestroy($old_img);
        imagedestroy($new_img);
    }
}