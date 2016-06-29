<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * CustomerRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerRepositoryTest extends EccubeTestCase
{

    protected $email;

    public function setUp()
    {
        parent::setUp();
        $this->email = 'customer@example.com';
        $this->Customer = $this->createCustomer($this->email);
    }

    public function testNewCustomer()
    {
        // TODO https://github.com/EC-CUBE/ec-cube/issues/870
        $Customer = $this->app['eccube.repository.customer']->newCustomer();

        $this->expected = 1;
        $this->actual = $Customer->getStatus()->getId();
        $this->verify();
    }

    public function testLoadUserByUsername()
    {
        $this->actual = $this->Customer;
        $this->expected = $this->app['eccube.repository.customer']->loadUserByUsername($this->email);
        $this->verify();
    }

    public function testLoadUserByUsernameWithException()
    {
        $username = 'aaaaa';
        try {
            $Customer = $this->app['eccube.repository.customer']->loadUserByUsername($username);
            $this->fail();
        } catch (UsernameNotFoundException $e) {
            $this->expected = sprintf('Username "%s" does not exist.', $username);
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    /**
     * loadUserByUsernameb内のgetNullOrSingleResultが正しい値を返却するかを確認する
     * ※getNullOrSingleResultは「NonUniqueResultException」をスローするが >
     * > 同一IDのデーターを投入→取得した際にエラーがでないか確認を行う
     * 投入データーは、同一レコード2件
     * 2件の同一データ取得時に、setMaxResult(1)で「NonUniqueResultException」をスローせず >
     * > 値が一件(Order句がないため順位不同)とれる事が成功テストケース
     */
    public function testLoadUserByUsernameSetSameRecord()
    {
        $email1 = 'same@example.com';
        $email2 = 'same@example.com';
        $Customer1 = $this->createCustomer($email1);
        $Customer2 = $this->createCustomer($email2);
        $GetCustomer1 = $this->app['eccube.repository.customer']->loadUserByUsername($email1);
        $this->expected = $GetCustomer1->getEmail();
        $this->actual = $Customer1->getEmail();
        $this->verify();
    }

    public function testRefreshUser()
    {
        $this->expected = $this->Customer;
        $this->actual = $this->app['eccube.repository.customer']->refreshUser($this->Customer);
        $this->verify();
    }

    public function testRefreshUserWithException()
    {
        try {
            $Customer = $this->app['eccube.repository.customer']->refreshUser(new DummyCustomer());
            $this->fail();
        } catch (UnsupportedUserException $e) {
            $this->expected = 'Instances of "Eccube\Tests\Repository\DummyCustomer" are not supported.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSupportedClass()
    {
        $this->assertTrue($this->app['eccube.repository.customer']->supportsClass(get_class($this->Customer)));
    }

    public function testCreateSalt()
    {
        $result = $this->app['eccube.repository.customer']->createSalt(5);

        $this->expected = 5;
        $this->actual = strlen(pack('H*', ($result))); // PHP5.4以降なら hex2bin が使える
        $this->verify();
    }

    public function testEncryptPassword()
    {
        $Customer = $this->app['eccube.repository.customer']->loadUserByUsername($this->email);
        $this->expected = $Customer->getPassword();

        $Customer->setPassword('password');
        $this->actual = $this->app['eccube.repository.customer']->encryptPassword($this->app, $Customer);
        $this->verify();
    }

    public function testGetNonActiveCustomerBySecretKey()
    {
        $this->expected = $this->Customer->getSecretKey();
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($Status);
        $this->app['orm.em']->flush();

        $Customer = $this->app['eccube.repository.customer']->getNonActiveCustomerBySecretKey($this->expected);
        $this->actual = $Customer->getSecretKey();
        $this->verify('secretは'.$this->expected.'ではありません');
    }

    public function testGetNonActiveCustomerBySecretKeyWithException()
    {
        $secret = $this->Customer->getSecretKey();

        try {
            // CustomerStatus::ACTIVE なので取得できないはず
            $Customer = $this->app['eccube.repository.customer']->getNonActiveCustomerBySecretKey($secret);
            $this->fail();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->expected = 'No result was found for query although at least one row was expected.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testGetActiveCustomerByEmail()
    {
        // XXX loadUserByUsername() と同じ役割？
        $this->actual = $this->Customer;
        $this->expected = $this->app['eccube.repository.customer']->getActiveCustomerByEmail($this->email);
        $this->verify();
    }

    public function testGetActiveCustomerByResetKey()
    {
        $expire = '+'.$this->app['config']['customer_reset_expire'].' min';
        $reset_key = $this->app['eccube.repository.customer']->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->app['orm.em']->flush();

        $Customer = $this->app['eccube.repository.customer']->getActiveCustomerByResetKey($reset_key);

        $this->assertNotNull($Customer);
    }

    public function testGetActiveCustomerByResetKeyWithException()
    {
        $expire = '-'.$this->app['config']['customer_reset_expire'].' min';
        $reset_key = $this->app['eccube.repository.customer']->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->app['orm.em']->flush();

        try {
            $Customer = $this->app['eccube.repository.customer']->getActiveCustomerByResetKey($reset_key);
            $this->fail();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->expected = 'No result was found for query although at least one row was expected.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testUpdateBuyData()
    {
        $Order = $this->createOrder($this->Customer);

        $OrderStatus = $this->app['eccube.repository.master.order_status']->find($this->app['config']['order_new']);

        $Order->setOrderStatus($OrderStatus);
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        $this->actual = 1;
        $this->app['eccube.repository.customer']->updateBuyData($this->app, $this->Customer, $this->app['config']['order_new']);
        $this->expected = $this->Customer->getBuyTimes();
        $this->verify();


        $OrderStatus = $this->app['eccube.repository.master.order_status']->find($this->app['config']['order_cancel']);

        $Order->setOrderStatus($OrderStatus);
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        $this->actual = 0;
        $this->app['eccube.repository.customer']->updateBuyData($this->app, $this->Customer, $this->app['config']['order_cancel']);
        $this->expected = $this->Customer->getBuyTimes();
        $this->verify();

    }
}

class DummyCustomer implements UserInterface
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
