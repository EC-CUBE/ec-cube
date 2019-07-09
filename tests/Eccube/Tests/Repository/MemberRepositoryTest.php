<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Member;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
        $this->Member = $this->app['eccube.repository.member']->find(2);
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')
            ->find(\Eccube\Entity\Master\Work::WORK_ACTIVE_ID);

        for ($i = 0; $i < 3; $i++) {
            $Member = new Member();
            $Member
                ->setLoginId('member-'.$i)
                ->setPassword('password')
                ->setSalt($this->app['eccube.repository.member']->createSalt(5))
                ->setRank($i)
                ->setWork($Work)
                ->setDelFlg(Constant::DISABLED);
            $Member->setPassword($this->app['eccube.repository.member']->encryptPassword($Member));
            $this->app['orm.em']->persist($Member);
        }
        $this->app['orm.em']->flush();
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

    /**
     * loadUserByUsername内のgetNullOrSingleResultが正しい値を返却するかを確認する
     * ※getNullOrSingleResultは「NonUniqueResultException」をスローするが >
     * > 同一IDのデーターを投入→取得した際にエラーがでないか確認を行う
     * 投入データーは、同一レコード2件
     * 2件のデータを投入しょうとしているが、本ケースでは、LoginIdがプライマリーキーのために >
     * > 重複データーは作成されない
     * 重複データーが作成されなければ、getNullOrSingleResultは「NonUniqueResultException」を >
     * > スローしないため、重複データーが登録されない事、同一プライマリーをflushしてもエラーが >
     * > 発生しない事を確認
     * 結果としては、一件のレコードをかえされる事を期待
     *
     */
    public function testLoadUserByUsernameSetSameRecord()
    {
        $this->Member = $this->app['eccube.repository.member']->find(2);
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')
            ->find(\Eccube\Entity\Master\Work::WORK_ACTIVE_ID);

        for ($i = 0; $i < 3; $i++) {
            $Member = new Member();
            $Member
                ->setLoginId('member-1')
                ->setPassword('password')
                ->setSalt($this->app['eccube.repository.member']->createSalt(5))
                ->setRank($i)
                ->setWork($Work)
                ->setDelFlg(Constant::DISABLED);
            $Member->setPassword($this->app['eccube.repository.member']->encryptPassword($Member));
            $this->app['orm.em']->persist($Member);
        }
        $this->app['orm.em']->flush();

        $this->assertInstanceOf('Eccube\Entity\Member', $this->app['eccube.repository.member']->loadUserByUsername('admin'));
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
        $rank = $this->Member->getRank();
        $result = $this->app['eccube.repository.member']->up($this->Member);
        $this->assertTrue($result);

        $this->expected = $rank + 1;
        $this->actual = $this->Member->getRank();
        $this->verify();
    }

    public function testUpWithException()
    {
        $this->Member->setRank(999);
        $this->app['orm.em']->flush();

        $result = $this->app['eccube.repository.member']->up($this->Member);
        $this->assertFalse($result);
    }

    public function testDown()
    {
        $rank = $this->Member->getRank();
        $result = $this->app['eccube.repository.member']->down($this->Member);
        $this->assertTrue($result);

        $this->expected = $rank - 1;
        $this->actual = $this->Member->getRank();
        $this->verify();
    }

    public function testDownWithException()
    {
        $this->Member->setRank(0);
        $this->app['orm.em']->flush();

        $result = $this->app['eccube.repository.member']->down($this->Member);
        $this->assertFalse($result);
    }

    public function testSave()
    {
        $Member = new Member();
        $Member
            ->setLoginId('member-100')
            ->setPassword('password')
            ->setSalt($this->app['eccube.repository.member']->createSalt(5))
            ->setRank(100)
            ->setDelFlg(Constant::DISABLED);
        $Member->setPassword($this->app['eccube.repository.member']->encryptPassword($Member));
        $result = $this->app['eccube.repository.member']->save($Member);
        $this->assertTrue($result);
    }

    public function testSaveWithRankNull()
    {
        $Members = $this->app['eccube.repository.member']->findAll();
        foreach ($Members as $Member) {
            $this->app['orm.em']->remove($Member);
        }
        $this->app['orm.em']->flush();

        $Member = new Member();
        $Member
            ->setLoginId('member-100')
            ->setPassword('password')
            ->setSalt($this->app['eccube.repository.member']->createSalt(5))
            ->setRank(100)
            ->setDelFlg(Constant::DISABLED);
        $Member->setPassword($this->app['eccube.repository.member']->encryptPassword($Member));
        $result = $this->app['eccube.repository.member']->save($Member);
        $this->assertTrue($result);

        $this->expected = 1;
        $this->actual = $Member->getRank();
        $this->verify();
    }


    public function testSaveWithException()
    {
        $Member = new Member(); // 空のインスタンスなので例外になる
        $result = $this->app['eccube.repository.member']->save($Member);
        $this->assertFalse($result);
    }

    public function testDelete()
    {
        $result = $this->app['eccube.repository.member']->delete($this->Member);
        $this->assertTrue($result);

        $this->expected = 1;
        $this->actual = $this->Member->getDelFlg();
        $this->verify();
    }

    public function testDeleteWithException()
    {
        $Member = new Member(); // 空のインスタンスなので例外になる
        $result = $this->app['eccube.repository.member']->delete($Member);
        $this->assertFalse($result);
    }

    public function testCreateSalt()
    {
        $result = $this->app['eccube.repository.member']->createSalt(5);

        $this->expected = 5;
        $this->actual = strlen(pack('H*', ($result))); // PHP5.4以降なら hex2bin が使える
        $this->verify();
    }

    public function testEncryptPassword()
    {
        $Members = $this->app['eccube.repository.member']->findAll();
        $Member = $this->app['eccube.repository.member']->loadUserByUsername('member-2');
        $this->expected = $Member->getPassword();
        $Member->setPassword('password');

        $this->actual = $this->app['eccube.repository.member']->encryptPassword($Member);
        $this->verify();
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
