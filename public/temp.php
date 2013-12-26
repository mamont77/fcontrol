<?php

$zip = new ZipArchive;
$res = $zip->open('30_export.zip');
if ($res === true) {
    $zip->extractTo('30_export');
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}
?>