<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function _initialize()
    {
        $this->clearDownloadDir();
    }

    private function clearDownloadDir()
    {
        $downloadDir = dirname(__DIR__) . '/_downloads/';
        if (file_exists($downloadDir)) {
            $files = scandir($downloadDir);
            $files = array_filter($files, function ($fileName) use ($downloadDir) {
                return is_file($downloadDir.$fileName) && (strpos($fileName, '.') != 0);
            });
            foreach ($files as $f) {
                unlink($downloadDir.$f);
            }
        }
    }

    public function getBaseUrl()
    {
        return $this->getModule('WebDriver')->_getUrl();
    }
}
