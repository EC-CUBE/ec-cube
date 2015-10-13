<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use org\bovigo\vfs\vfsStream;

/**
 * BlockRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class BlockRepositoryTest extends EccubeTestCase
{
    protected $DeviceType;
    private $block_id;

    public function setUp()
    {
        parent::setUp();
        $this->removeBlock();
        $this->DeviceType = $this->app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        for ($i = 0; $i < 10; $i++) {
            $Block = new Block();
            $Block
                ->setName('block-'.$i)
                ->setFileName('block/block-'.$i)
                ->setLogicFlg(1)
                ->setDeletableFlg(0)
                ->setDeviceType($this->DeviceType);
            $this->app['orm.em']->persist($Block);
            $this->app['orm.em']->flush(); // ここで flush しないと, MySQL で ID が取得できない
            $this->block_id = $Block->getId();
        }
    }

    protected function removeBlock()
    {
        $Blocks = $this->app['eccube.repository.block']->findAll();
        foreach ($Blocks as $Block) {
            $this->app['orm.em']->remove($Block);
        }
        $this->app['orm.em']->flush();
    }

    public function testGetList()
    {
        $Blocks = $this->app['eccube.repository.block']->getList($this->DeviceType);

        $this->assertNotNull($Blocks);
        $this->expected = 10;
        $this->actual = count($Blocks);
        $this->verify();
    }

    public function testGetBlock()
    {
        $Block = $this->app['eccube.repository.block']->getBlock($this->block_id, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');
    }

    public function testFindOrCreate()
    {
        // TODO findOrCreate(array $condition) にするべき
        // https://github.com/EC-CUBE/ec-cube/issues/922
        $Block = $this->app['eccube.repository.block']->findOrCreate($this->block_id, $this->DeviceType);

        $this->assertNotNull($Block);
        $this->expected = $this->block_id;
        $this->actual = $Block->getId();
        $this->verify('ブロックIDは'.$this->expected.'ではありません');

        $Block = $this->app['eccube.repository.block']->findOrCreate(null, $this->DeviceType);
        $this->assertNotNull($Block);
        $this->assertTrue($Block instanceof \Eccube\Entity\Block);
        $this->assertNull($Block->getId());

        $Block = $this->app['eccube.repository.block']->findOrCreate(999999, $this->DeviceType);
        $this->assertNull($Block); // XXX block_id = 999999 の新たなインスタンスを返してほしいが不可能.
    }

    public function testGetWriteTemplatePath()
    {
        $this->expected = $this->app['config']['block_realdir'];
        // XXX 引数は使用していない. $app['config']['block_realdir'] のパスを返しているだけ
        $this->actual = $this->app['eccube.repository.block']->getWriteTemplatePath();
        $this->verify();
    }

    public function testGetReadTemplateFile()
    {
        $fileName = 'example_block';
        $root = vfsStream::setup('rootDir');
        vfsStream::newDirectory('default');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['block_realdir'] = vfsStream::url('rootDir');
        $config['block_default_realdir'] = vfsStream::url('rootDir/default');
        $this->app['config'] = $config;

        file_put_contents($this->app['config']['block_realdir'].'/'.$fileName.'.twig', 'test');

        // XXX 引数 isUser は使用していない
        $data = $this->app['eccube.repository.block']->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }

    public function testGetReadTemplateFileWithDefault()
    {
        $fileName = 'example_block';
        $root = vfsStream::setup('rootDir');
        mkdir(vfsStream::url('rootDir').'/default', 0777, true);

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['block_realdir'] = vfsStream::url('rootDir');
        $config['block_default_realdir'] = vfsStream::url('rootDir').'/default';
        $this->app['config'] = $config;

        file_put_contents($this->app['config']['block_default_realdir'].'/'.$fileName.'.twig', 'test');

        // XXX 引数 isUser は使用していない
        $data = $this->app['eccube.repository.block']->getReadTemplateFile($fileName);
        // XXX 実装上は, tpl_data しか使っていない. 配列を返す意味がない
        $this->actual = $data['tpl_data'];
        $this->expected = 'test';
        $this->verify();
    }
}
