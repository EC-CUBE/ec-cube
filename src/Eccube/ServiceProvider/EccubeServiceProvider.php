<?php

namespace Eccube\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class EccubeServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param BaseApplication $app An Application instance
     */
    public function register(BaseApplication $app)
    {
        // Service
        $app['eccube.service.system'] = $app->share(function() use ($app) {
            return new \Eccube\Service\SystemService($app);
        });
        $app['view'] = $app->share(function() use ($app) {
            return new \Eccube\Service\ViewService($app);
        });
        $app['eccube.service.cart'] = $app->share(function() use ($app) {
            return new \Eccube\Service\CartService($app);
        });
        $app['eccube.service.tax_rule'] = $app->share(function() use ($app) {
            return new \Eccube\Service\TaxRuleService($app);
        });

        // Entity
        $app['eccube.entity.cart'] = function() use ($app) {
            return new \Eccube\Entity\Cart($app);
        };

        // Repository
        $app['eccube.repository.customer'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Customer');
        });
        $app['eccube.repository.member'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Member');
        });
        $app['eccube.repository.product'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Product');
        });
        $app['eccube.repository.base_info'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\BaseInfo');
        });
        $app['eccube.repository.tax_rule'] = $app->share(function() use ($app) {
            $taxRuleRepository = $app['orm.em']->getRepository('Eccube\Entity\TaxRule');
            $taxRuleRepository->setApp($app);

            return $taxRuleRepository;
        });
        $app['eccube.repository.master.constant'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Constant');
        });

        // em
        $app['orm.em'] = $app->share($app->extend('orm.em', function (\Doctrine\ORM\EntityManager $em, \Silex\Application $app) {
            // tax_rule
            $taxRuleRepository = $em->getRepository('Eccube\Entity\TaxRule');
            $taxRuleRepository->setApp($app);
            $taxRuleService = new \Eccube\Service\TaxRuleService($taxRuleRepository);
            $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($taxRuleService));

            // save
            $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\SaveEventSubscriber());

            // 
            $config = $em->getConfiguration();
            $config->addFilter("soft_delete", "\Eccube\Doctrine\Filter\SoftDeleteFilter");
            $em->getFilters()->enable('soft_delete');

            return $em;
        }));

        // Form\Type
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();

            return $extensions;
        }));
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Eccube\Form\Type\NameType();
            $types[] = new \Eccube\Form\Type\TelType();
            $types[] = new \Eccube\Form\Type\PrefType();
            $types[] = new \Eccube\Form\Type\ZipType();
            $types[] = new \Eccube\Form\Type\AddressType();
            $types[] = new \Eccube\Form\Type\SexType();
            $types[] = new \Eccube\Form\Type\JobType();
            $types[] = new \Eccube\Form\Type\ReminderType();
            $types[] = new \Eccube\Form\Type\MailMagazineType();

            $types[] = new \Eccube\Form\Type\CustomerType($app);
            $types[] = new \Eccube\Form\Type\AddCartType($app['config']);
            $types[] = new \Eccube\Form\Type\CustomerLoginType($app['session']);
            $types[] = new \Eccube\Form\Type\ContactType($app['config']);
            $types[] = new \Eccube\Form\Type\PointType($app);
            $types[] = new \Eccube\Form\Type\InstallType($app);

            return $types;
        }));

        // PEAR
        $app['smarty'] = function () {
            return new \Smarty();
        };
        $app['mobile.detect'] = function () {
            return new \Mobile_Detect();
        };
        $app['pear.archive.tar'] = $app->protect(function ($p_tarname, $p_compress = null) {
            return new \Archive_Tar($p_tarname, $p_compress);
        });
        $app['pear.cache.lite'] = $app->protect(function ($options = array()) {
            return new \Cache_Lite($options);
        });
        $app['pear.calendar.month.weekdays'] = $app->protect(function ($y, $m, $firstDay=null) {
            return new \Calendar_Month_Weekdays($y, $m, $firstDay);
        });
        $app['pear.http.request'] = $app->protect(function ($url = '', $params = array()) {
            return new \HTTP_Request($url, $params);
        });
        $app['pear.mail'] = $app->protect(function ($driver, $params = array()) {
            return \Mail::factory($driver, $params);
        });
        $app['pear.net.user_agent.mobile'] = $app->protect(function ($userAgent = null) {
            return \Net_UserAgent_Mobile::singleton($userAgent);
        });
        $app['pear.net.url'] = $app->protect(function ($url = null, $useBrackets = true) {
            return new \Net_URL($url, $useBrackets);
        });
        $app['pear.services.json'] = $app->protect(function ($use = 0) {
            return new \Services_JSON($use);
        });
        $app['pear.text.password'] = $app->protect(function ($length = 10, $type = 'pronounceable', $chars = '') {
            return \Text_Password::create($length, $type, $chars);
        });
        $app['pear.xml.serializer'] = $app->protect(function ($options = null) {
            return new \XML_Serializer($options);
        });

        // framework
        $app['eccube.cart_session'] = $app->protect(function ($cartKey = 'cart') {
            return new \Eccube\Framework\CartSession($cartKey);
        });
        $app['eccube.customer'] = function () {
            return new \Eccube\Framework\Customer();
        };
        $app['eccube.customer_list'] = $app->protect(function ($array, $mode = '') {
            return new \Eccube\Framework\CustomerList($array, $mode);
        });
        $app['eccube.cookie'] = $app->protect(function ($day = COOKIE_EXPIRE) {
            return new \Eccube\Framework\Cookie($day);
        });
        $app['eccube.check_error'] = $app->protect(function ($array = '') {
            return new \Eccube\Framework\CheckError($array);
        });
        $app['eccube.date'] = $app->protect(function ($start_year = '', $end_year = '') {
            return new \Eccube\Framework\Date($start_year, $end_year);
        });
        $app['eccube.display'] = $app->protect(function ($hasPrevURL = true) {
            return new \Eccube\Framework\Display($hasPrevURL);
        });
        $app['eccube.form_param'] = function () {
            return new \Eccube\Framework\FormParam();
        };
        $app['eccube.page_navi'] = $app->protect(function ($now_page, $all_row, $page_row, $func_name, $navi_max = NAVI_PMAX, $urlParam = '', $display_number = true) {
            return new \Eccube\Framework\PageNavi($now_page, $all_row, $page_row, $func_name, $navi_max, $urlParam, $display_number);
        });
        $app['eccube.product'] = $app->protect(function () {
            return new \Eccube\Framework\Product();
        });
        $app['eccube.response'] = $app->protect(function () {
            return new \Eccube\Framework\Response();
        });
        $app['eccube.query'] = $app->protect(function ($dsn = '', $force_run = false, $new = false) {
            return \Eccube\Framework\Query::getSingletonInstance($dsn, $force_run, $new);
        });
        $app['eccube.site_session'] = $app->share(function () {
            return new \Eccube\Framework\SiteSession();
        });
        $app['eccube.sendmail'] = $app->protect(function () {
            return new \Eccube\Framework\Sendmail();
        });

        // db
        $app['eccube.db.factory'] = $app->protect(function ($db_type = DB_TYPE) {
            return \Eccube\Framework\DB\DBFactory::getInstance($db_type);
        });
        $app['eccube.db.master_data'] = $app->share(function () {
            return new \Eccube\Framework\DB\MasterData();
        });

        // graph
        $app['eccube.graph.bar'] = $app->protect(function ($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = LINE_LEFT, $top = LINE_TOP, $area_width = LINE_AREA_WIDTH, $area_height = LINE_AREA_HEIGHT) {
            return new \Eccube\Framework\Graph\BarGraph($bgw, $bgh, $left, $top, $area_width, $area_height);
        });
        $app['eccube.graph.line'] = $app->protect(function ($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = LINE_LEFT, $top = LINE_TOP, $area_width = LINE_AREA_WIDTH, $area_height = LINE_AREA_HEIGHT) {
            return new \Eccube\Framework\Graph\LineGraph($bgw, $bgh, $left, $top, $area_width, $area_height);
        });
        $app['eccube.graph.pie'] = $app->protect(function ($bgw = BG_WIDTH, $bgh = BG_HEIGHT, $left = PIE_LEFT, $top = PIE_TOP) {
            return new \Eccube\Framework\Graph\PieGraph($bgw, $bgh, $left, $top);
        });

        // helper
        $app['eccube.helper.address'] = $app->share(function () {
            return new \Eccube\Framework\Helper\AddressHelper();
        });
        $app['eccube.helper.best_products'] = $app->share(function () {
            return new \Eccube\Framework\Helper\BestProductsHelper();
        });
        $app['eccube.helper.bloc'] = $app->protect(function ($devide_type_id = DEVICE_TYPE_PC) {
            return new \Eccube\Framework\Helper\BlocHelper($devide_type_id);
        });
        $app['eccube.helper.category'] = $app->protect(function ($count_check = false) {
            return new \Eccube\Framework\Helper\CategoryHelper($count_check);
        });
        $app['eccube.helper.csv'] = function () {
            return new \Eccube\Framework\Helper\CsvHelper();
        };
        $app['eccube.helper.customer'] = $app->share(function () {
            return new \Eccube\Framework\Helper\CustomerHelper();
        });
        $app['eccube.helper.db'] = $app->share(function () {
            return new \Eccube\Framework\Helper\DbHelper();
        });
        $app['eccube.helper.delivery'] = $app->share(function () {
            return new \Eccube\Framework\Helper\DeliveryHelper();
        });
        $app['eccube.helper.file_manager'] = $app->share(function () {
            return new \Eccube\Framework\Helper\FileManagerHelper();
        });
        $app['eccube.helper.fpdi'] = $app->protect(function ($orientation = 'P', $unit = 'mm', $size = 'A4') {
            return new \Eccube\Framework\Helper\FpdiHelper($orientation, $unit, $size);
        });
        $app['eccube.helper.holiday'] = $app->share(function () {
            return new \Eccube\Framework\Helper\HolidayHelper();
        });
        $app['eccube.helper.kiyaku'] = $app->share(function () {
            return new \Eccube\Framework\Helper\KiyakuHelper();
        });
        $app['eccube.helper.mail'] = $app->share(function () {
            return new \Eccube\Framework\Helper\MailHelper();
        });
        $app['eccube.helper.mailtemplate'] = $app->share(function () {
            return new \Eccube\Framework\Helper\MailtemplateHelper();
        });
        $app['eccube.helper.maker'] = $app->share(function () {
            return new \Eccube\Framework\Helper\MakerHelper();
        });
        $app['eccube.helper.mobile'] = $app->share(function () {
            return new \Eccube\Framework\Helper\MobileHelper();
        });
        $app['eccube.helper.news'] = $app->share(function () {
            return new \Eccube\Framework\Helper\NewsHelper();
        });
        $app['eccube.helper.page_layout'] = $app->share(function () {
            return new \Eccube\Framework\Helper\PageLayoutHelper();
        });
        $app['eccube.helper.payment'] = $app->share(function () {
            return new \Eccube\Framework\Helper\PaymentHelper();
        });
        $app['eccube.helper.plugin'] = function () {
            $plugin_activate_flg = PLUGIN_ACTIVATE_FLAG;
            return \Eccube\Framework\Helper\PluginHelper::getSingletonInstance($plugin_activate_flg);
        };
        $app['eccube.helper.purchase'] = $app->share(function () {
            return new \Eccube\Framework\Helper\PurchaseHelper();
        });
        $app['eccube.helper.session'] = $app->share(function () {
            return new \Eccube\Framework\Helper\SessionHelper();
        });
        $app['eccube.helper.tax_rule'] = $app->share(function () {
            return new \Eccube\Framework\Helper\TaxRuleHelper();
        });
        $app['eccube.helper.transform'] = $app->protect(function ($source) {
            return new \Eccube\Framework\Helper\TransformHelper($source);
        });

        // util
        $app['eccube.util.utils'] = $app->share(function () {
            return new \Eccube\Framework\Util\Utils();
        });
        $app['eccube.util.gc_utils'] = $app->share(function () {
            return new \Eccube\Framework\Util\GcUtils();
        });

        // smarty
        $app['smarty'] = $app->extend('smarty', function ($smarty) {
            /* @var $DbHelper \Eccube\Framework\Helper\DbHelper */
            $DbHelper = Application::alias('eccube.helper.db');
            /* @var $Utils \Eccube\Framework\Util\Utils */
            $Utils = Application::alias('eccube.util.utils');
            /* @var $GcUtils \Eccube\Framework\Util\GcUtils */
            $GcUtils = Application::alias('eccube.util.gc_utils');

            $smarty->left_delimiter = '<!--{';
            $smarty->right_delimiter = '}-->';
            $smarty->plugins_dir = array(
                realpath(__DIR__ . '/../../smarty_extends'),
                realpath(__DIR__ . '/../../../vendor/smarty/smarty/libs/plugins'),
            );
            $smarty->register_modifier('sfDispDBDate', array($Utils, 'sfDispDBDate'));
            $smarty->register_modifier('sfGetErrorColor', array($Utils, 'sfGetErrorColor'));
            $smarty->register_modifier('sfTrim', array($Utils, 'sfTrim'));
            $smarty->register_modifier('sfCalcIncTax', array($DbHelper, 'calcIncTax'));
            $smarty->register_modifier('sfPrePoint', array($Utils, 'sfPrePoint'));
            $smarty->register_modifier('sfGetChecked', array($Utils, 'sfGetChecked'));
            $smarty->register_modifier('sfTrimURL', array($Utils, 'sfTrimURL'));
            $smarty->register_modifier('sfMultiply', array($Utils, 'sfMultiply'));
            $smarty->register_modifier('sfRmDupSlash', array($Utils, 'sfRmDupSlash'));
            $smarty->register_modifier('sfCutString', array($Utils, 'sfCutString'));
            $smarty->register_modifier('sfMbConvertEncoding', array($Utils, 'sfMbConvertEncoding'));
            $smarty->register_modifier('sfGetEnabled', array($Utils, 'sfGetEnabled'));
            $smarty->register_modifier('sfNoImageMainList', array($Utils, 'sfNoImageMainList'));
            // XXX register_function で登録すると if で使用できないのではないか？
            $smarty->register_function('sfIsHTTPS', array($Utils, 'sfIsHTTPS'));
            $smarty->register_function('sfSetErrorStyle', array($Utils, 'sfSetErrorStyle'));
            $smarty->register_function('printXMLDeclaration', array($GcUtils, 'printXMLDeclaration'));
            $smarty->default_modifiers = array('script_escape');

            $smarty->force_compile = SMARTY_FORCE_COMPILE_MODE === true;

            return $smarty;
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(BaseApplication $app)
    {
    }
}
