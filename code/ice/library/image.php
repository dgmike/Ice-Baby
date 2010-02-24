<?php if (!defined('BASE_URL')) exit('No direct script access allowed');
/**
 * Image:: Contém métodos de tratamento de imagens como criar thumbnails etc.
 * @author ldmotta <ldmotta@gmail.com>
 */
class Image {
    /**
     * resize_image -> Faz o redimensionamento de imagens
     * @param string $tmpname Recebe o $_FILES['source']['tmp_name'] vindo do $_FILES ou o caminho da imagem de origem
     * @param int $size Largura máxima da imagem, a altura será proporcional
     * @param string $save_dir Diretório de destino da nova imagem
     * @param string $save_name Nome da nova imagem
     * @param tinyint $maxisheight Define o destino de $size, se para largura (Default $maxisheight=0) ou altura ($maxisheight=1).
     * @return bool true caso a imagem tenha sido criada corretamente, ou false.
     * @author David Taubmann http://www.quidware.com (edited from LEDok - http://www.citadelavto.ru/)
     *
     */
    function resize( $tmpname, $size, $save_dir, $save_name, $maxisheight = 0, $quality=100 ) {
        $save_dir   .= ( substr($save_dir,-1) != "/") ? "/" : "";
        $gis        = getimagesize($tmpname);
        $type       = $gis[2];
        switch($type) {
            case "1": $imorig = imagecreatefromgif($tmpname); break;
            case "2": $imorig = imagecreatefromjpeg($tmpname);break;
            case "3": $imorig = imagecreatefrompng($tmpname); break;
            default:  $imorig = imagecreatefromjpeg($tmpname);
        }

        $x = imagesx($imorig);
        $y = imagesy($imorig);

        $woh = (!$maxisheight)? $gis[0] : $gis[1] ;

        if($woh <= $size) {
            $aw = $x;
            $ah = $y;
        } else {
            if(!$maxisheight) {
                $aw = $size;
                $ah = $size * $y / $x;
            } else {
                $aw = $size * $x / $y;
                $ah = $size;
            }
        }
        $im = imagecreatetruecolor($aw, $ah);
        if (imagecopyresampled($im,$imorig , 0,0,0,0,$aw,$ah,$x,$y))
            if (imagejpeg($im, $save_dir.$save_name))
                return true;
            else
                return false;
    }

    /**
     * Redimensiona a imagem, e corta caso ela tenha um ratio diferente.
     * @param string $imgSrc Caminho da imagem original
     * @param int $thumbnail_width Largura final
     * @param int $thumbnail_height Altura final
     * @return img Retorna a imagem modificada
     */
    function CroppedThumbnail($imgSrc, $save_dir, $save_name, $thumbnail_width, $thumbnail_height) { //$imgSrc is a FILE - Returns an image resource.
        $save_dir   .= ( substr($save_dir,-1) != "/") ? "/" : "";

        list($width_orig, $height_orig, $type) = getimagesize($imgSrc);

        switch($type) {
            case "1": $myImage = imagecreatefromgif($imgSrc); break;
            case "2": $myImage = imagecreatefromjpeg($imgSrc);break;
            case "2": $myImage = imagecreatefromjpeg($imgSrc);break;
            case "3": $myImage = imagecreatefrompng($imgSrc); break;
            default:  $myImage = imagecreatefromjpeg($imgSrc);
        }

        $ratio_orig = $width_orig/$height_orig;

        if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
           $new_height = $thumbnail_width/$ratio_orig;
           $new_width = $thumbnail_width;
        } else {
           $new_width = $thumbnail_height*$ratio_orig;
           $new_height = $thumbnail_height;
        }

        $x_mid = $new_width/2;  //horizontal middle
        $y_mid = $new_height/2; //vertical middle

        $process = imagecreatetruecolor(round($new_width), round($new_height));

        imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
        $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

        imagedestroy($process);
        imagedestroy($myImage);

        if (imagejpeg($thumb, $save_dir.$save_name))
            return true;
        else
            return false;
    }

}

