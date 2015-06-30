<?php

namespace Eccube\Application;

use Symfony\Component\Form\FormBuilder;
use Monolog\Logger;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * TODO PHP5.4以上の対応でよくなったら消す
 */
class ApplicationTrait extends \Silex\Application
{

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
     * @param mixed $data The initial data for the form
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
     * @param array $context The log context
     * @param int $level The logging level
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
     * @param UserInterface $user A UserInterface instance
     * @param string $password The password to encode
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
     * @param \Swift_Message $message A \Swift_Message instance
     * @param array $failedRecipients An array of failures by-reference
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
     * @param string $id The message id
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     * @param string $locale The locale
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
     * @param string $id The message id
     * @param int $number The number to use to find the indice of the message
     * @param array $parameters An array of parameters for the message
     * @param string $domain The domain for the message
     * @param string $locale The locale
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
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param Response $response A Response instance
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
            $response->setContent($twig->render($view, $parameters));
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
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
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
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
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
