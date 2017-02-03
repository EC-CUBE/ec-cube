<?php

namespace Eccube\Tests\Mock;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Dummy of CsrfToken.
 *
 * @author Kentaro Ohkouchi
 */
class CsrfTokenManagerMock implements CsrfTokenManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        return new CsrfToken($tokenId, null);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken($tokenId)
    {
        return new CsrfToken($tokenId, null);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenValid(CsrfToken $token)
    {
        return true;
    }
}
