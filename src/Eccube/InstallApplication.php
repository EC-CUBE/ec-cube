<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Monolog\Logger;

class InstallApplication extends \Silex\Application
{
    /** @var Application app */
    protected static $app;

    /**
     * Alias
     *
     * @return object
     */
    public static function alias($name)
    {
        $args = func_get_args();
        array_shift($args);
        $obj = static::$app[$name];

        if (is_callable($obj)) {
            return call_user_func_array($obj, $args);
        } else {
            return $obj;
        }
    }

    public function __construct(array $values = array())
    {
        $app = $this;
        static::$app = $this;
        ini_set('error_reporting', E_ALL | ~E_STRICT);
        parent::__construct($values);

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/site.log',
        ));

        $app->register(new \Silex\Provider\SessionServiceProvider());

        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.form.templates' => array('bootstrap_3_horizontal_layout.html.twig'),
        ));

        $this->before(function (Request $request, \Silex\Application $app) {
            $app['twig'] = $app->share($app->extend("twig", function (\Twig_Environment $twig, \Silex\Application $app) {
                $paths[] = __DIR__ . '/Resource/template/install';
                $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

                return $twig;
            }));
        }, self::EARLY_EVENT);

        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\ValidatorServiceProvider());

        $this->register(new \Silex\Provider\TranslationServiceProvider(), array(
            'locale' => 'ja',
        ));
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', __DIR__ . '/Resource/locale/ja.yml', 'ja');

            return $translator;
        }));

        $app->mount('', new ControllerProvider\InstallControllerProvider());
        $app->register(new ServiceProvider\InstallServiceProvider());

        $app->error(function (\Exception $e, $code) use ($app) {
            if ($code === 404) {
                return $app->redirect($app['url_generator']->generate('install'));
            } elseif ($app['debug']) {
                return;
            }

            return $app['twig']->render('error.twig', array(
                'error' => 'エラーが発生しました.',
            ));
        });
    }

    public function boot()
    {
        parent::boot();
    }

    public function addSuccess($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.success', $message);
    }

    public function addError($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.error', $message);
    }

    public function addDanger($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.danger', $message);
    }

    public function addWarning($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.warning', $message);
    }

    public function addInfo($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.info', $message);
    }

    /*
     * 以下のコードの著作権について
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * For the full copyright and license information, please view the silex
     * LICENSE file that was distributed with this source code.
     */
    /** FormTrait */
    /**
     * Creates and returns a form builder instance
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    public function form($data = null, array $options = array())
    {
        return $this['form.factory']->createBuilder('form', $data, $options);
    }

    /** MonologTrait */
    /**
     * Adds a log record.
     *
     * @param string $message The log message
     * @param array  $context The log context
     * @param int    $level   The logging level
     *
     * @return bool Whether the record has been processed
     */
    public function log($message, array $context = array(), $level = Logger::INFO)
    {
        return $this['monolog']->addRecord($level, $message, $context);
    }

    /** SecurityTrait */
    /**
     * Gets a user from the Security Context.
     *
     * @return mixed
     *
     * @see TokenInterface::getUser()
     */
    public function user()
    {
        if (null === $token = $this['security']->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    /**
     * Encodes the raw password.
     *
     * @param UserInterface $user     A UserInterface instance
     * @param string        $password The password to encode
     *
     * @return string The encoded password
     *
     * @throws \RuntimeException when no password encoder could be found for the user
     */
    public function encodePassword(UserInterface $user, $password)
    {
        return $this['security.encoder_factory']->getEncoder($user)->encodePassword($password, $user->getSalt());
    }

    /** SwiftmailerTrait */
    /**
     * Sends an email.
     *
     * @param \Swift_Message $message          A \Swift_Message instance
     * @param array          $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent messages
     */
    public function mail(\Swift_Message $message, &$failedRecipients = null)
    {
        return $this['mailer']->send($message, $failedRecipients);
    }

    /** TranslationTrait */
    /**
     * Translates the given message.
     *
     * @param string $id         The message id
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this['translator']->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id         The message id
     * @param int    $number     The number to use to find the indice of the message
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this['translator']->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /** TwigTrait */
    /**
     * Renders a view and returns a Response.
     *
     * To stream a view, pass an instance of StreamedResponse as a third argument.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $twig = $this['twig'];

        if ($response instanceof StreamedResponse) {
            $response->setCallback(function () use ($twig, $view, $parameters) {
                $twig->display($view, $parameters);
            });
        } else {
            if (null === $response) {
                $response = new Response();
            }
            $response->setContent($this['view']->render($view, $parameters));
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return Response A Response instance
     */
    public function renderView($view, array $parameters = array())
    {
        return $this['twig']->render($view, $parameters);
    }

    /** UrlGeneratorTrait */
    /**
     * Generates a path from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
