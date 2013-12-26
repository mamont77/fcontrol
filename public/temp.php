<?php

$dir = '30_export';
removeDir($dir);

$zip = new ZipArchive;
$res = $zip->open($dir . '.zip');
if ($res === true) {
    $zip->extractTo($dir);
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}

/**
 * @param $dir
 */
function removeDir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    removeDir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                };
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
