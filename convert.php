#!/usr/local/bin/php
<?php
/**
 * ファイルのエンコーディングを $fromEncoding から $toEncoding へ変換します.
 *
 * @version $Revision$ $Date$
 * @author  Kentaro Ohkouchi
 */

/** include files suffix. */
$includes = ".php,.inc,.tpl,.css,.sql";

/** convert from encoding. */
$fromEncoding = "EUC-JP";
/** convert to encoding. */
$toEncoding = "UTF-8";

$includeArray = explode(',', $includes);
$fileArrays = listdirs('.');

foreach ($fileArrays as $path) {
    if (is_file($path)) {
        $fileName = basename($path);
        $suffix = substr($fileName, -4);

        foreach ($includeArray as $include) {
            if ($suffix == $include) {
                $contents = file_get_contents($path);
                $convertedContents = mb_convert_encoding($contents,
                                                         $toEncoding,
                                                         $fromEncoding);

                if (is_writable($path)) {

                    // file open
                    $handle = fopen($path, "w");
                    if (!$handle) {
                        echo "Cannot open file (". $path . ")";
                        continue;
                    }

                    if (fwrite($handle, $convertedContents) === false) {
                        echo "Cannot write to file (" . $path . ")";
                        continue;
                    }

                    echo "converted " . $path . "\n";
                    fclose($handle);
                } else {

                    echo "The file " . $filename . "is not writable";
                }
            }
        }
    }
}

function listdirs($dir) {
    static $alldirs = array();
    $dirs = glob($dir . '/*');
    if (count($dirs) > 0) {
        foreach ($dirs as $d) $alldirs[] = $d;
    }
    foreach ($dirs as $dir) listdirs($dir);
    return $alldirs;
}
?>

