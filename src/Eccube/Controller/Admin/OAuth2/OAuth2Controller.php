<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\OAuth2;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\OAuth2AuthorizationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OAuth2Controller extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @Route("/%eccube_admin_route%/authorize", name="admin_oauth2_authorize")
     * @Template("@admin/OAuth2/authorization.twig")
     *
     * @return array|RedirectResponse
     */
    public function authorize(Request $request)
    {
        // TODO validation
        $response_type = $request->get('response_type');
        $client_id = $request->get('client_id');
        $client_secret = $request->get('client_secret');
        $redirect_uri = $request->get('redirect_uri');
        $state = $request->get('state');
        $scope = $request->get('scope');

        $builder = $this->formFactory->createBuilder(OAuth2AuthorizationType::class);

        $form = $builder->getForm();

        $form['response_type']->setData($response_type);
        $form['client_id']->setData($client_id);
        $form['client_secret']->setData($client_secret);
        $form['redirect_uri']->setData($redirect_uri);
        $form['state']->setData($state);
        $form['scope']->setData($scope);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                return $this->redirectToRoute('oauth2_authorize', $form->getData());
            }
        }

        return [
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => $response_type,
            'state' => $state,
            'scope' => $scope,
            'client_secret' => $client_secret,
            'form' => $form->createView(),
        ];
    }
}
