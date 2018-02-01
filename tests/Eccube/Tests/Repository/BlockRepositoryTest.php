<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\ReflectionUtil;
use org\bovigo\vfs\vfsStream;

/**
 * BlockRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class BlockRepositoryTest extends EccubeTestCase
{
    /**
     * @var  DeviceType
     */
    protected $DeviceType;

    /**
     * @var  string
     */
    private $block_id;

    /**
     * @var  BlockRepository
     */
    protected $blockRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->blockRepository = $this->container->get(BlockRepository::class);
        $this->removeBlock();
        $this->DeviceType = $this->container->get(DeviceTypeRepository::class)
            ->find(DeviceType::DEVICE_TYPE_PC);

        for ($i = 0; $i < 10; $i++) {
            $Block = new Block();
            $Block
                ->setName('block-'.$i)
                ->setFileName('block/block-'.$i)
                ->setUseController(true)
                ->setDeletable(false)
                ->setDeviceType($this->DeviceType);
            $this->entityManager->persist($Block);
            $this->entityManager->flush(); // ここで flush しないと, MySQL で ID が取得できない
            $this->block_id = $Block->getId();
        }
    }

    protected function removeBlock()
    {
        $Blocks = $this->blockRepository->findAll();
        foreach ($Blocks as $Block) {
            $this->entityManager->remove($Block);
        }
        $this->entityManager->flush();
    }

    public function testGetList()
    {
        $Blocks = $this->blockRepository->getList($this->DeviceType);

        $this->assertNotNull($Blocks);
        $this->expected = 10;
        $this->actual = count($Blocks);
        $this->verify();
    }

    public function testGetBlock()
    {
        $Block = $this->blockRepository->getBlock($this->block_id, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');
    }

    public function testFindOrCreate()
    {
        // TODO findOrCreate(array $condition) にするべき
        // https://github.com/EC-CUBE/ec-cube/issues/922
        $Block = $this->blockRepository->findOrCreate($this->block_id, $this->DeviceType);

        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');

        $Block = $this->blockRepository->findOrCreate(null, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->assertTrue($Block instanceof Block);
        $this->assertNull($Block->getId());

        $Block = $this->blockRepository->findOrCreate(999999, $this->DeviceType);
        $this->assertNull($Block); // XXX block_id = 999999 の新たなインスタンスを返してほしいが不可能.
    }

    public function testGetWriteTemplatePath()
    {
        $this->expected = $this->eccubeConfig['block_realdir'];
        // XXX 引数は使用していない. $app['config']['block_realdir'] のパスを返しているだけ
        $this->actual = $this->blockRepository->getWriteTemplatePath();
        $this->verify();
    }

    public function testGetReadTemplateFile()
    {
        $fileName = 'example_block';
        $root = vfsStream::setup('rootDir');
        $root->addChild(vfsStream::newDirectory('default'));

        file_put_contents(vfsStream::url('rootDir').'/'.$fileName.'.twig', 'test');

        ReflectionUtil::setValue($this->blockRepository, 'eccubeConfig', [
            'block_realdir' =>  vfsStream::url('rootDir'),
            'block_default_realdir' => vfsStream::url('rootDir/default'),
        ]);

        // XXX 引数 isUser は使用していない
        $data = $this->blockRepository->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }

    public function testGetReadTemplateFileWithDefault()
    {
        $fileName = 'example_block';
        $root = vfsStream::setup('rootDir');
        $root->addChild(vfsStream::newDirectory('default'));

        file_put_contents(vfsStream::url('rootDir/default').'/'.$fileName.'.twig', 'test');

        ReflectionUtil::setValue($this->blockRepository, 'eccubeConfig', [
            'block_realdir' =>  vfsStream::url('rootDir'),
            'block_default_realdir' => vfsStream::url('rootDir/default'),
        ]);

        // XXX 引数 isUser は使用していない
        $data = $this->blockRepository->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }
}
