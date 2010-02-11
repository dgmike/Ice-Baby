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
    $save_dir     .= ( substr($save_dir,-1) != "/") ? "/" : "";
    $gis        = getimagesize($tmpname);
    $type        = $gis[2];
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
    $im = imagecreatetruecolor($aw,$ah);
    if (imagecopyresampled($im,$imorig , 0,0,0,0,$aw,$ah,$x,$y))
        if (imagejpeg($im, $save_dir.$save_name))
            return true;
        else
            return false;
}


}

