<?php
$im = imagecreatetruecolor(500, 40);

//modo sobreescritura de pixeles anteriores activado
imagealphablending($im ,false);

$negro= imagecolorallocatealpha($im, 1, 1, 1,1);
$fondo = imagecolorallocatealpha($im, 255, 255, 255,127);

imagefill($im, 1, 1, $fondo);
imagestring($im, 5, 5, 10, $_SESSION['dato'], $negro);

// imagestring($im, 5, 2, 7, $_SESSION['dato'], $negro);
// Guardar la imagen como 'textosimple.jpg'

// Get the image width and height.
    $imw = imagesx($im);
    $imh = imagesy($im);

    // Set the X variables.
    $xmin = $imw;
    $xmax = 0;

    // Start scanning for the edges.
    for ($iy=0; $iy<$imh; $iy++){
        $first = true;
        for ($ix=0; $ix<$imw; $ix++){
            $ndx = imagecolorat($im, $ix, $iy);
            if ($ndx != $fondo){
                if ($xmin > $ix){ $xmin = $ix; }
                if ($xmax < $ix){ $xmax = $ix; }
                if (!isset($ymin)){ $ymin = $iy; }
                $ymax = $iy;
                if ($first){ $ix = $xmax; $first = false; }
            }
        }
    }

    // The new width and height of the image. (not including padding)
    $imw = 1+$xmax-$xmin; // Image width in pixels
    $imh = 1+$ymax-$ymin; // Image height in pixels

    // Make another image to place the trimmed version in.
    $im2 = imagecreatetruecolor($imw+10, $imh+10);

    // Rellena con el color del fondo
    imagefill($im2, 1, 1, $fondo);
    imagestring($im2, 5, 2, 2, $_SESSION['dato'], $negro); //pone en la posición dos, dos


//modo sobreescritura de pixeles anteriores desactivado
imagealphablending($im2,true);

$rotar = imagerotate($im2, 90, 0);

//cabecera
header("Content-type: image/png");

//salida conservando el canal alfa
imagesavealpha($rotar,true);

imagepng($rotar, './temporal/'.$_SESSION['id'].'.png');

// Liberar memoria
imagedestroy($rotar);

?> 

