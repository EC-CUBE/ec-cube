<?php

namespace Eccube;

class Router
{
    var $adminDir;
    var $isAdmin;
    var $template;

    function __construct()
    {
        $this->adminDir = (substr(ADMIN_DIR, -1) === '/' ) ? substr(ADMIN_DIR, 0, strlen(ADMIN_DIR) - 1) : ADMIN_DIR;
    }

    public function action()
    {
        // Routingするため値が変わってしまうので戻す
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];
        if (end(split('\.', $path)) !== 'php') {
            $path .= (substr($path, -1) === '/') ? 'index.php' : '.php';
        }
        $_SERVER['SCRIPT_NAME'] = $path;
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . $path;
        $this->template = str_replace('.php', '', str_replace(ROOT_URLPATH, '', $path));

        $settings = $this->getSettingsFromUrl();

        $namespaces = array('Eccube\\Page');
        if ($this->isAdmin) $namespaces[] = 'Admin';
        if (!empty($settings['dir'])) $namespaces[] = ucfirst($settings['dir']);
        if (!empty($settings['class'])) $namespaces[] = ucfirst($settings['class']);
        $namespace = implode('\\', $namespaces);

        $obj = new $namespace;
        call_user_func(array($obj, 'init'));
        call_user_func(array($obj, $settings['action']));

    }

    private function getSettingsFromUrl()
    {
        $map = RouteMap::getMap();
        $this->isAdmin = strpos($this->template, $this->adminDir) !== FALSE;

        $mapKey= 'index';
        foreach ($map as $path => $settings) {
            if ($this->isAdmin) $path = str_replace('admin', $this->adminDir, $path);
            if (preg_match('/' . preg_quote($path, '/') . '/', $this->template)) $mapKey = $path;
        }
        return $map[$mapKey];
    }

    private function getPathSettingsFromTemplate($templateKey)
    {
        $map = RouteMap::getMap();

        foreach ($map as $path => $settings) {
            if ($templateKey === $settings['template']) {
                return array($$path, $settings);
            }
        }
    }

    public function generatePath($templateKey)
    {
        list($path) = $this->getPathSettingsFromTemplate($templateKey);
        if (strpos($path, 'admin') === 0) {
            $path = str_replace('admin', $this->adminDir, $path);
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . ROOT_URLPATH . $path;
        return $path;
    }

    public function generateUrl($templateKey)
    {
        list($path, $settings) = $this->getPathSettingsFromTemplate($templateKey);
        $url = (isset($settings['ssl']) && $settings['ssl'] === true) ? HTTPS_URL : HTTP_URL;
        if (strpos($path, 'admin') === 0) {
            $url .= str_replace('admin', $this->adminDir, $path);
        }
        return $url;
    }

}