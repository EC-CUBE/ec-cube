<?php
namespace Plugin\ExamplePlugin\Controller;

use Eccube\Application;
use Eccube\Entity\Payment;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Shopping\OrderType;
use Plugin\ExamplePlugin\Service\Calculator\Strategy\ExamplePaymentStrategy;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints\Length;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/shopping")
 */
class ExampleController
{
    // see https://developer.paypal.com/docs/integration/direct/express-checkout/integration-jsv4/#integration-steps-new
    const PAYPAL_ENV = 'sandbox';
    const PAYPAL_SANDBOX_ID = '<XXXXX>';
    const PAYPAL_PRODUCTION_ID = '<XXXXX>';

    /**
     * 購入画面表示をオーバーライドする.
     *
     * テンプレートを独自のものに変更
     *
     * @Route("/", name="shopping")
     * @Template("ExamplePlugin/Resource/template/Shopping/index.twig")
     *
     * @param Application $app
     * @param Request $request
     * @return array
     */
    public function index(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping/checkToCart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注情報を初期化
        $response = $app->forward($app->path("shopping/initializeOrder"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 単価集計し, フォームを生成する
        $app->forwardChain($app->path("shopping/calculateOrder"))
            ->forwardChain($app->path("shopping/createForm"));

        // 受注のマイナスチェック
        $response = $app->forward($app->path("shopping/checkToMinusPrice"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 複数配送の場合、エラーメッセージを一度だけ表示
        $app->forward($app->path("shopping/handleMultipleErrors"));

        $Order = $app['request_scope']->get('Order');
        $form = $app['request_scope']->get(OrderType::class);

        // TODO Eccube\Service\Payment\PaymentMethod に記述できるようにした方がよさそう
        $usePayPal = false;
        if ($Payment = $form['Payment']->getData()) {
            if ($Payment instanceof Payment && $Payment->getMethod() == 'サンプルクレジットカード') {
                $usePayPal = true;
            }
        }
        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'usePayPal' => $usePayPal
        ];
    }

    /**
     * 独自の決済処理.
     *
     * Plugin\ExamplePlugin\Payment\Method\ExamplePaymentCreditCard から forward される処理
     *
     * @Route("/examplePayment", name="shopping/examplePayment")
     *
     * @param Application $app
     * @param Request $request
     * @return array
     */
    public function examplePayment(Application $app, Request $request)
    {
        // 決済サーバーと通信する等, 独自の処理を記述する
        // 空のレスポンスを返すことで処理を続行する
        // リダイレクトレスポンス等を返して、別のページへ遷移させることも可能
        return new Response();
    }
}
