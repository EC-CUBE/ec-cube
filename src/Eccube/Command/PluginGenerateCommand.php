<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PluginGenerateCommand extends Command
{
    protected static $defaultName = 'eccube:plugin:generate';

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->addArgument('name', InputOption::VALUE_REQUIRED, 'plugin name')
            ->addArgument('code', InputOption::VALUE_REQUIRED, 'plugin code')
            ->addArgument('ver', InputOption::VALUE_REQUIRED, 'plugin version')
            ->setDescription('Generate plugin skeleton.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->fs = new Filesystem();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('name') && null !== $input->getArgument('code') && null !== $input->getArgument('ver')) {
            return;
        }

        $this->io->title('EC-CUBE Plugin Generator Interactive Wizard');

        // Plugin name.
        $name = $input->getArgument('name');
        if (null !== $name) {
            $this->io->text(' > <info>name</info>: '.$name);
        } else {
            $name = $this->io->ask('name', 'EC-CUBE Sample Plugin');
            $input->setArgument('name', $name);
        }

        // Plugin code.
        $code = $input->getArgument('code');
        if (null !== $code) {
            $this->io->text(' > <info>code</info>: '.$code);
        } else {
            $code = $this->io->ask('code', 'Sample', [$this, 'validateCode']);
            $input->setArgument('code', $code);
        }

        // Plugin version.
        $version = $input->getArgument('ver');
        if (null !== $version) {
            $this->io->text(' > <info>ver</info>: '.$version);
        } else {
            $version = $this->io->ask('ver', '1.0.0', [$this, 'validateVersion']);
            $input->setArgument('ver', $version);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $code = $input->getArgument('code');
        $version = $input->getArgument('ver');

        $this->validateCode($code);
        $this->validateVersion($version);

        $pluginDir = $this->container->getParameter('kernel.project_dir').'/app/Plugin/'.$code;

        $this->createDirectories($pluginDir);
        $this->createConfig($pluginDir, $name, $code, $version);
        $this->createEvent($pluginDir, $code);
        $this->createMessages($pluginDir);
        $this->createNav($pluginDir, $code);
        $this->createTwigBlock($pluginDir, $code);
        $this->createConfigController($pluginDir, $code);

        $this->io->success(sprintf('Plugin was successfully created: %s %s %s', $name, $code, $version));
    }

    public function validateCode($code)
    {
        if (empty($code)) {
            throw new InvalidArgumentException('The code can not be empty.');
        }
        if (strlen($code) > 255) {
            throw new InvalidArgumentException('The code can enter up to 255 characters');
        }
        if (1 !== preg_match('/^\w+$/', $code)) {
            throw new InvalidArgumentException('The code [a-zA-Z_] is available.');
        }

        $pluginDir = $this->container->getParameter('kernel.project_dir').'/app/Plugin/'.$code;
        if (file_exists($pluginDir)) {
            throw new InvalidArgumentException('Plugin directory exists.');
        }

        return $code;
    }

    public function validateVersion($version)
    {
        // TODO
        return $version;
    }

    /**
     * @param string $pluginDir
     */
    protected function createDirectories($pluginDir)
    {
        $dirs = [
            'Controller/Admin',
            'Entity',
            'Repository',
            'Form/Type',
            'Form/Extension',
            'Resource/doctrine',
            'Resource/locale',
            'Resource/template/admin',
        ];

        foreach ($dirs as $dir) {
            $this->fs->mkdir($pluginDir.'/'.$dir);
        }
    }

    /**
     * @param string $pluginDir
     */
    protected function createConfig($pluginDir, $name, $code, $version)
    {
        $source = <<<EOL
{
  "name": "ec-cube/$code",
  "version": "$version",
  "description": "$name",
  "type": "eccube-plugin",
  "extra": {
    "code": "$code"
  }
}
EOL;

        $this->fs->dumpFile($pluginDir.'/composer.json', $source);
    }

    /**
     * @param string $pluginDir
     */
    protected function createMessages($pluginDir)
    {
        $this->fs->dumpFile($pluginDir.'/Resource/locale/messages.ja.yaml', '');
        $this->fs->dumpFile($pluginDir.'/Resource/locale/validators.ja.yaml', '');
    }

    /**
     * @param string $pluginDir
     */
    protected function createTwigBlock($pluginDir, $code)
    {
        $source = <<<EOL
<?php

namespace Plugin\\${code};

use Eccube\\Common\\EccubeTwigBlock;

class TwigBlock implements EccubeTwigBlock
{
    /**
     * @return array
     */
    public static function getTwigBlock()
    {
        return [];
    }
}

EOL;
        $this->fs->dumpFile($pluginDir.'/TwigBlock.php', $source);
    }

    /**
     * @param string $pluginDir
     */
    protected function createNav($pluginDir, $code)
    {
        $source = <<<EOL
<?php

namespace Plugin\\${code};

use Eccube\\Common\\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [];
    }
}

EOL;
        $this->fs->dumpFile($pluginDir.'/Nav.php', $source);
    }

    /**
     * @param string $pluginDir
     */
    protected function createEvent($pluginDir, $code)
    {
        $source = <<<EOL
<?php

namespace Plugin\\${code};

use Symfony\\Component\\EventDispatcher\\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [];
    }
}

EOL;
        $this->fs->dumpFile($pluginDir.'/Event.php', $source);
    }

    /**
     * @param string $pluginDir
     */
    protected function createConfigController($pluginDir, $code)
    {
        $snakecased = Container::underscore($code);

        $source = <<<EOL
<?php

namespace Plugin\\${code}\\Controller\\Admin;

use Eccube\\Controller\\AbstractController;
use Plugin\\${code}\\Form\\Type\\Admin\\ConfigType;
use Plugin\\${code}\\Repository\\ConfigRepository;
use Sensio\\Bundle\\FrameworkExtraBundle\\Configuration\\Template;
use Symfony\\Component\\HttpFoundation\\Request;
use Symfony\\Component\\Routing\\Annotation\\Route;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected \$configRepository;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository \$configRepository
     */
    public function __construct(ConfigRepository \$configRepository)
    {
        \$this->configRepository = \$configRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/${snakecased}/config", name="${snakecased}_admin_config")
     * @Template("@${code}/admin/config.twig")
     */
    public function index(Request \$request)
    {
        \$Config = \$this->configRepository->get();
        \$form = \$this->createForm(ConfigType::class, \$Config);
        \$form->handleRequest(\$request);

        if (\$form->isSubmitted() && \$form->isValid()) {
            \$Config = \$form->getData();
            \$this->entityManager->persist(\$Config);
            \$this->entityManager->flush(\$Config);
            \$this->addSuccess('登録しました。', 'admin');

            return \$this->redirectToRoute('${snakecased}_admin_config');
        }

        return [
            'form' => \$form->createView(),
        ];
    }
}

EOL;

        $this->fs->dumpFile($pluginDir.'/Controller/Admin/ConfigController.php', $source);

        $source = <<<EOL
<?php

namespace Plugin\\${code}\\Entity;

use Doctrine\\ORM\\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="plg_${snakecased}_config")
 * @ORM\Entity(repositoryClass="Plugin\\${code}\\Repository\\ConfigRepository")
 */
class Config
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private \$id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private \$name;

    /**
     * @return int
     */
    public function getId()
    {
        return \$this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return \$this->name;
    }

    /**
     * @param string \$name
     *
     * @return \$this;
     */
    public function setName(\$name)
    {
        \$this->name = \$name;

        return \$this;
    }
}

EOL;

        $this->fs->dumpFile($pluginDir.'/Entity/Config.php', $source);

        $source = <<<EOL
<?php

namespace Plugin\\${code}\\Repository;

use Eccube\\Repository\\AbstractRepository;
use Plugin\\${code}\\Entity\\Config;
use Symfony\\Bridge\\Doctrine\\RegistryInterface;

/**
 * ConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConfigRepository extends AbstractRepository
{
    /**
     * ConfigRepository constructor.
     *
     * @param RegistryInterface \$registry
     */
    public function __construct(RegistryInterface \$registry)
    {
        parent::__construct(\$registry, Config::class);
    }

    /**
     * @param int \$id
     *
     * @return null|Config
     */
    public function get(\$id = 1)
    {
        return \$this->find(\$id);
    }
}

EOL;

        $this->fs->dumpFile($pluginDir.'/Repository/ConfigRepository.php', $source);

        $source = <<<EOL
<?php

namespace Plugin\\${code}\\Form\\Type\\Admin;

use Plugin\\${code}\\Entity\\Config;
use Symfony\\Component\\Form\\AbstractType;
use Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType;
use Symfony\\Component\\Form\\FormBuilderInterface;
use Symfony\\Component\\OptionsResolver\\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {
        \$builder->add('name', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 255]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver \$resolver)
    {
        \$resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}

EOL;

        $this->fs->dumpFile($pluginDir.'/Form/Type/Admin/ConfigType.php', $source);

        $source = <<<EOL
{% extends '@admin/default_frame.twig' %}

{% set menus = ['store', 'plugin', 'plugin_list'] %}

{% block title %}${code}{% endblock %}
{% block sub_title %}プラグイン一覧{% endblock %}

{% form_theme form '@admin/Form/bootstrap_4_horizontal_layout.html.twig' %}

{% block stylesheet %}{% endblock stylesheet %}

{% block javascript %}{% endblock javascript %}

{% block main %}
    <form role="form" method="post">

        {{ form_widget(form._token) }}

        <div class="c-contentsArea__cols">
            <div class="c-contentsArea__primaryCol">
                <div class="c-primaryCol">
                    <div class="card rounded border-0 mb-4">
                        <div class="card-header"><span>設定</span></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3"><span>名前</span><span
                                            class="badge badge-primary ml-1">必須</span></div>
                                <div class="col mb-2">
                                    {{ form_widget(form.name) }}
                                    {{ form_errors(form.name) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="c-conversionArea">
            <div class="c-conversionArea__container">
                <div class="row justify-content-between align-items-center">
                    <div class="col-6">
                        <div class="c-conversionArea__leftBlockItem">
                            <a class="c-beseLink"
                               href="{{ url('admin_store_plugin') }}">
                                <i class="fa fa-backward" aria-hidden="true"></i>
                                <span>プラグイン一覧</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row align-items-center justify-content-end">
                            <div class="col-auto">
                                <button class="btn btn-ec-conversion px-5"
                                        type="submit">登録</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
{% endblock %}

EOL;
        $this->fs->dumpFile($pluginDir.'/Resource/template/admin/config.twig', $source);
    }
}
