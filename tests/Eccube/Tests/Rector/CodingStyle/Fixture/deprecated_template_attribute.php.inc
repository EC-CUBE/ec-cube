<?php

namespace Eccube\Tests\Rector\CodingStyle\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DeprecatedTemplateAttribute
{
    #[Template('@admin/Content/cache.twig')]
    public function index()
    {
    }
}

?>
-----
<?php

namespace Eccube\Tests\Rector\CodingStyle\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DeprecatedTemplateAttribute
{
    #[Template(template: '@admin/Content/cache.twig')]
    public function index()
    {
    }
}

?>
