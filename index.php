<?php

define('GENERATE', true);
define('DIR', './galleries/first/');
define('THUMB_SIZE', 128);
define('BIG_MAX_WID', 1600);
define('BASE_URL', 'http://example.com/galleries/');

$thumbTpl = <<<TPL
<a title="" href="{big}" data-milkbox="milkbox:gall1">
    <img src="{thumb}" alt="" />
</a>
TPL;

if (is_dir(DIR.'thumbs') === false) {
    mkdir(DIR.'thumbs');
}
if (is_dir(DIR.'big') === false) {
    mkdir(DIR.'big');
}

require_once './vendor/autoload.php';

$images = glob(DIR.'*.*');
if (is_array($images)) {
    $html = '';
    $thumbsDir = sprintf('%sthumbs/', DIR);
    $bigDir = sprintf('%sbig/', DIR);
    foreach ($images as $item) {
        $fileName = basename($item);
        $fileName = strtolower($fileName);
        if (GENERATE) {
            $thumb = new PHPThumb\GD($item);
            $thumb->adaptiveResize(THUMB_SIZE, THUMB_SIZE);
            $thumb->save($thumbsDir.$fileName);
        }
        $search = array('{big}','{thumb}');
        $replace = array(
            sprintf('%sbig/%s', BASE_URL, $fileName),
            sprintf('%sthumbs/%s', BASE_URL, $fileName),
        );
        $html .= str_replace($search, $replace, $thumbTpl);
    }
    echo htmlspecialchars($html);
} else {
    printf('no files in: %s', DIR);
}
