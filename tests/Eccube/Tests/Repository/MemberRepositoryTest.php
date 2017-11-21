<?php

namespace Eccube\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Member;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * MemberRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class MemberRepositoryTest extends EccubeTestCase
{

    protected $Member;
    public function setUp()
    {
        parent::setUp();
        $this->Member = $this->app['eccube.repository.member']->find(1);
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')
            ->find(\Eccube\Entity\Master\Work::WORK_ACTIVE_ID);

        for ($i = 0; $i < 3; $i++) {
            $Member = new Member();
            $salt = bin2hex(openssl_random_pseudo_bytes(5));
            $password = 'password';
            $encoder = $this->app['security.encoder_factory']->getEncoder($Member);
            $Member
                ->setLoginId('member-1')
                ->setPassword($encoder->encodePassword($password, $salt))
                ->setSalt($salt)
                ->setSortNo($i)
                ->setWork($Work);
            $this->app['orm.em']->persist($Member);
            $this->app['eccube.repository.member']->save($Member);
        }
    }

    public function testLoadUserByUsername()
    {
        $this->actual = $this->Member;
        $this->expected = $this->app['eccube.repository.member']->loadUserByUsername('admin');
        $this->verify();
    }

    public function testLoadUserByUsernameWithException()
    {
        $username = 'aaaaa';
        try {
            $Member = $this->app['eccube.repository.member']->loadUserByUsername($username);
            $this->fail();
        } catch (UsernameNotFoundException $e) {
            $this->expected = sprintf('Username "%s" does not exist.', $username);
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testRefreshUser()
    {
        $this->expected = $this->Member;
        $this->actual = $this->app['eccube.repository.member']->refreshUser($this->Member);
        $this->verify();
    }

    public function testRefreshUserWithException()
    {
        try {
            $Member = $this->app['eccube.repository.member']->refreshUser(new DummyMember());
            $this->fail();
        } catch (UnsupportedUserException $e) {
            $this->expected = 'Instances of "Eccube\Tests\Repository\DummyMember" are not supported.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSupportedClass()
    {
        $this->assertTrue($this->app['eccube.repository.member']->supportsClass(get_class($this->Member)));
    }

    public function testUp()
    {
        $rank = $this->Member->getSortNo();
        $this->app['eccube.repository.member']->up($this->Member);

        $this->expected = $rank + 1;
        $this->actual = $this->Member->getSortNo();
        $this->verify();
    }

    public function testUpWithException()
    {
        $this->Member->setSortNo(999);
        $this->app['orm.em']->flush();

        try {
            $this->app['eccube.repository.member']->up($this->Member);
            $this->fail();
        } catch (\Exception $e) {

        }
    }

    public function testDown()
    {
        /** @var EntityManager $em */
        $em = $this->app['orm.em'];
        $qb = $em->createQueryBuilder();
        $max = $qb->select('MAX(m.sort_no)')
            ->from(Member::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $this->Member->setSortNo($max + 1);
        $this->app['orm.em']->flush();

        $rank = $this->Member->getSortNo();
        $this->app['eccube.repository.member']->down($this->Member);

        $this->expected = $rank - 1;
        $this->actual = $this->Member->getSortNo();
        $this->verify();
    }

    public function testDownWithException()
    {
        $this->Member->setSortNo(0);
        $this->app['orm.em']->flush();

        try {
            $this->app['eccube.repository.member']->down($this->Member);
            $this->fail();
        } catch (\Exception $e) {

        }
    }

    public function testSave()
    {
        $Member = new Member();
        $Member
            ->setLoginId('member-100')
            ->setPassword('password')
            ->setSalt('salt')
            ->setSortNo(100);

        $this->app['eccube.repository.member']->save($Member);
    }

    public function testSaveWithRankNull()
    {
        /** @var EntityManager $em */
        $em = $this->app['orm.em'];
        $qb = $em->createQueryBuilder();
        $rank = $qb->select('MAX(m.sort_no)')
            ->from(Member::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $Member = new Member();
        $Member
            ->setLoginId('member-100')
            ->setPassword('password')
            ->setSalt('salt')
            ->setSortNo(100);
        $this->app['eccube.repository.member']->save($Member);

        $this->expected = $rank + 1;
        $this->actual = $Member->getSortNo();

        $this->verify();
    }

    public function testDelete()
    {
        $Member = $this->createMember();
        $id = $Member->getId();
        $this->app['eccube.repository.member']->delete($Member);

        $Member = $this->app['eccube.repository.member']->find($id);
        $this->assertNull($Member);
    }

    public function testDeleteWithException()
    {
        $Member1 = $this->createMember();
        $Member2 = $this->createMember();
        $Member2->setCreator($Member1);
        $this->app['orm.em']->flush();

        // 参照制約で例外となる
        try {
            $this->app['eccube.repository.member']->delete($Member1);
            $this->fail();
        } catch (\Exception $e) {

        }
    }
}

class DummyMember implements UserInterface
{
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return 'password';
    }
    public function getSalt()
    {
        return 'salt';
    }
    public function getUsername()
    {
        return 'user';
    }
    public function eraseCredentials()
    {
        return;
    }
}
