<?php

namespace Eccube\Tests\Rector\CodingStyle\Fixture;

use Symfony\Component\Routing\Attribute\Route;

class RouteAttributeOrder
{
    #[Route('/contact', requirements: ['id' => '\d+'], name: 'contact', methods: ['GET', 'POST'])]
    public function contact()
    {
    }
}

?>
-----
<?php

namespace Eccube\Tests\Rector\CodingStyle\Fixture;

use Symfony\Component\Routing\Attribute\Route;

class RouteAttributeOrder
{
    #[Route(path: '/contact', name: 'contact', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function contact()
    {
    }
}

?>
