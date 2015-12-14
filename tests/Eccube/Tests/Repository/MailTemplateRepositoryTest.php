<?php
namespace Eccube\Tests\Repository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Doctrine\ORM\NoResultException;
/**
 *  test cases.
 *
 * @author Yasumasa Yoshinaga
 */
class MailTemplateRepositoryTest extends EccubeTestCase
{
    protected $Member;
    public function setUp()
    {
        parent::setUp();
        $this->Member = $this->app['eccube.repository.member']->find(2);
    }
    public function testValiedFindFromFindOrCreate()
    {
        $this->expected = 1;
        $MailTemplate = $this->app['eccube.repository.mail_template']->findOrCreate($this->expected);
        $this->actual = $MailTemplate->getId();
        $this->verify();
    }
    public function testValiedCreateFromFindOrCreate()
    {
        $this->expected = $this->Member->getId();
        $Mailtempalte = $this->app['eccube.repository.mail_template']->findOrCreate(0, $this->Member);
        $this->actual = $Mailtempalte->getCreator()->getId();
        $this->verify();
    }
}