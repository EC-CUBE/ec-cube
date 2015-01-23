<?php

namespace Eccube;

class Router
{
    var $adminDir;
    var $isAdmin;
    var $isApi;
    var $template;
    var $args;
    var $method;

    function __construct()
    {
        $this->adminDir = (substr(ADMIN_DIR, -1) === '/' ) ? substr(ADMIN_DIR, 0, strlen(ADMIN_DIR) - 1) : ADMIN_DIR;
        $this->method = ($_SERVER['REQUEST_METHOD']) ?: 'GET';
    }

    public function action()
    {
        // Routingするため値が変わってしまうので戻す
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];
        /* 既存パス対応 */
        if (end(split('\.', $path)) !== 'php') {
            $path .= (substr($path, -1) === '/') ? 'index.php' : '.php';
        }
        
        $_SERVER['SCRIPT_NAME'] = $path;
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . $path;
        $this->template = str_replace('.php', '', str_replace(ROOT_URLPATH, '', $path));

        $settings = $this->getSettingsFromUrl();

        $namespace = 'Eccube';
        if ($this->isApi) {
            $namespace .= '\\Api';
        } else {
            $namespace .= '\\Page';
        }
        if ($this->isAdmin) {
            $namespace .= '\\Admin';
        }
        if (!empty($settings['class'])) {
            $namespace .= $settings['class'];
        }

        $obj = new $namespace;
        call_user_func(array($obj, 'init'));
        call_user_func(array($obj, $settings['action']));

    }

    private function getSettingsFromUrl()
    {
        $map = RouteMap::getMap();
        $this->isAdmin = strpos($this->template, $this->adminDir) !== FALSE;
        $this->isApi = strpos($this->template, 'api/') !== FALSE;
        $mapKey= 'index';
        foreach ($map as $path => $settings) {
            $pathes = array_filter(explode('/', $path));
            $templates = array_filter(explode('/', $this->template));

            $pathCount = count($pathes);
            $tempCount = count($templates);

            // URL構成が合わない定義は評価しない
            if ($pathCount !== $tempCount) {
                continue;
            }

            // methodが合わない定義は評価しない
            $method = ($settings['method']) ?: "GET|POST";
            $methods = explode('|', $method);
            if (!in_array($this->method, $methods)) {
                continue;
            }
            if ($this->isAdmin) {
                $path = str_replace('admin', $this->adminDir, $path);
            }
            // 置換文字列がある場合、対応する値をGETパラメータに設定する
            if (preg_match_all('/\/\[[^:\]]*(?::([^:\]]*+))?\]/', $path, $matches, PREG_SET_ORDER) > 0) {
                $classPath = substr($path, 0, strpos($path, '/[:'));
                $argsPath = substr($path, strpos($path, '/[:'), strlen($path));
                $args = array_filter(explode('/', $argsPath));
                if (strpos($this->template, $classPath) !== 0) {
                    continue;
                }
                $query = str_replace($classPath, '', $this->template);
                $queries = array_filter(explode('/', $query));

                foreach ($args as $argKey => $argVal) {
                    $argVal = str_replace(array('[:', ']'), array('', ''), $argVal);
                    $_REQUEST[$argVal] = $queries[$argKey];
                }
                $mapKey = $path;
            } else {
                if ($pathes == $templates) {
                    $mapKey = $path;
                }
            }
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