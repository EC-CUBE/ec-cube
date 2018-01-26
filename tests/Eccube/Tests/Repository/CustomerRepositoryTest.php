<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * CustomerRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerRepositoryTest extends EccubeTestCase
{

    protected $email;
    protected $Customer;

    /**
     * @var CustomerRepository
     */
    protected $customerRepo;

    /**
     * @var OrderStatusRepository
     */
    protected $masterOrderStatusRepo;

    public function setUp()
    {
        parent::setUp();

        $this->email = 'customer@example.com';
        $this->Customer = $this->createCustomer($this->email);

        $this->customerRepo = $this->container->get(CustomerRepository::class);
        $this->masterOrderStatusRepo = $this->container->get(OrderStatusRepository::class);
    }

    public function testNewCustomer()
    {
        // TODO https://github.com/EC-CUBE/ec-cube/issues/870
        $Customer = $this->customerRepo->newCustomer();

        $this->expected = 1;
        $this->actual = $Customer->getStatus()->getId();
        $this->verify();
    }

    public function testLoadUserByUsername()
    {
        $this->actual = $this->Customer;
        $this->expected = $this->customerRepo->loadUserByUsername($this->email);
        $this->verify();
    }

    public function testLoadUserByUsernameWithException()
    {
        $username = 'aaaaa';
        try {
            $this->customerRepo->loadUserByUsername($username);
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
        $GetCustomer1 = $this->customerRepo->loadUserByUsername($email1);
        $this->expected = $GetCustomer1->getEmail();
        $this->actual = $Customer1->getEmail();
        $this->verify();
    }

    public function testRefreshUser()
    {
        $this->expected = $this->Customer;
        $this->actual = $this->customerRepo->refreshUser($this->Customer);
        $this->verify();
    }

    public function testRefreshUserWithException()
    {
        try {
            $this->customerRepo->refreshUser(new DummyCustomer());
            $this->fail();
        } catch (UnsupportedUserException $e) {
            $this->expected = 'Instances of "Eccube\Tests\Repository\DummyCustomer" are not supported.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }

    public function testSupportedClass()
    {
        $this->assertTrue($this->customerRepo->supportsClass(get_class($this->Customer)));
    }

    public function testGetProvisionalCustomerBySecretKey()
    {
        $this->expected = $this->Customer->getSecretKey();
        $Status = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $this->Customer->setStatus($Status);
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getProvisionalCustomerBySecretKey($this->expected);
        $this->actual = $Customer->getSecretKey();
        $this->verify('secretは'.$this->expected.'ではありません');
    }

    public function testGetProvisionalCustomerBySecretKeyWithException()
    {
        $secret = $this->Customer->getSecretKey();

        // CustomerStatus::REGULARなので取得できないはず
        $Customer = $this->customerRepo->getProvisionalCustomerBySecretKey($secret);
        $this->assertNull($Customer);
    }

    public function testGetRegularCustomerByEmail()
    {
        // XXX loadUserByUsername() と同じ役割？
        $this->actual = $this->Customer;
        $this->expected = $this->customerRepo->getRegularCustomerByEmail($this->email);
        $this->verify();
    }

    public function testGetRegularCustomerByResetKey()
    {
        $expire = '+'.$this->eccubeConfig['customer_reset_expire'].' min';
        $reset_key = $this->customerRepo->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getRegularCustomerByResetKey($reset_key);

        $this->assertNotNull($Customer);
    }

    public function testGetRegularCustomerByResetKeyWithException()
    {
        $expire = '-'.$this->eccubeConfig['customer_reset_expire'].' min';
        $reset_key = $this->customerRepo->getResetPassword();
        $this->Customer
            ->setResetKey($reset_key)
            ->setResetExpire(new \DateTime($expire));
        $this->entityManager->flush();

        $Customer = $this->customerRepo->getRegularCustomerByResetKey($reset_key);
        $this->assertNull($Customer);
    }

    public function testUpdateBuyData()
    {
        $Order = $this->createOrder($this->Customer);

        $OrderStatus = $this->masterOrderStatusRepo->find(OrderStatus::NEW);

        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        $this->actual = 1;
        $this->customerRepo->updateBuyData($this->Customer, OrderStatus::NEW);
        $this->expected = $this->Customer->getBuyTimes();
        $this->verify();


        $OrderStatus = $this->masterOrderStatusRepo->find(OrderStatus::CANCEL);

        $Order->setOrderStatus($OrderStatus);
        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        $this->actual = 0;
        $this->customerRepo->updateBuyData($this->Customer, OrderStatus::CANCEL);
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
