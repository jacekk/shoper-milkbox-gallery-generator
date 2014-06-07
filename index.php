<?php

/* settings START */

define('PROCESS_FILES', true);
define('DIR', './galleries/first/');
define('THUMB_SIZE', 128);
define('BIG_MAX_HEI', 1000);
define('BIG_MAX_WID', 1600);
define('BASE_URL', 'http://example.com/galleries/');
define('THUMB_DIR', 'thumbs');
define('BIG_DIR', 'big');

$thumbTpl = <<<TPL
<a title="" href="{big}" data-milkbox="milkbox:gall1">
    <img src="{thumb}" alt="" />
</a>
TPL;

/* settings END */

require_once './vendor/autoload.php';

$thumbsDir = sprintf('%s%s/', DIR, THUMB_DIR);
$bigDir = sprintf('%s%s/', DIR, BIG_DIR);

$images = glob(DIR.'*.*');

if (is_array($images) && count($images)) {
    if (! is_dir($thumbsDir)) {
        mkdir($thumbsDir);
    }
    if (! is_dir($bigDir)) {
        mkdir($bigDir);
    }
    $markup = '';
    foreach ($images as $item) {
        $fileName = basename($item);
        $fileName = strtolower($fileName);
        $fileName = preg_replace('/[^a-z0-9\-\.]/i', '_', $fileName);
        if (PROCESS_FILES) {
            $thumb = new PHPThumb\GD($item);
            $thumb->resize(BIG_MAX_WID, BIG_MAX_HEI);
            $thumb->save($bigDir.$fileName);
            $thumb->adaptiveResize(THUMB_SIZE, THUMB_SIZE);
            $thumb->save($thumbsDir.$fileName);
        }
        $search = array('{big}','{thumb}');
        $replace = array(
            sprintf('%s%s/%s', BASE_URL, THUMB_DIR, $fileName),
            sprintf('%s%s/%s', BASE_URL, BIG_DIR, $fileName),
        );
        $markup .= str_replace($search, $replace, $thumbTpl);
    }
    if (isset($_SERVER['argv'])) {
        $markupFile = './markup.html';
        file_put_contents($markupFile, $markup);
        printf('check file: %s', $markupFile);
    } else {
        echo htmlspecialchars($markup);
    }
} else {
    printf('no files in: %s', DIR);
}
