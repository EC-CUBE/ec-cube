<?php


namespace Acme\Entity;


use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Eccube\Doctrine\Filter\ConditionalSQLFilter;
use Eccube\Entity\Product;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 【秘密の商品】という商品名は商品詳細画面のURLを知っているユーザだけがアクセスできるようにするSQLフィルター
 */
class SecretProductFilter extends SQLFilter implements ConditionalSQLFilter
{

    /**
     * SQLフィルターを適用するか判定
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Eccube\Application $app
     * @return bool
     */
    public static function isApplicable($request, $app)
    {
        $route = $request->attributes->get('_route');

        // 管理者に対しては常にフィルターを掛けない
        if ($app->isGranted('ROLE_ADMIN')) {
            return false;
        }

        // 商品詳細画面
        else if ($route === 'product_detail') {
            // ログインユーザに対してはフィルターを掛けない
            if ($app->isGranted('ROLE_USER')) {
                return false;
            }

            // 秘密の商品である場合はログインが必須
            $params = $request->attributes->get('_route_params');
            $Product = $app['eccube.repository.product']->get($params['id']);
            if ($Product && strpos($Product->getName(), '【秘密の商品】') === 0) {
                throw  new AccessDeniedException();
            }

            return false;
        }
        return true;

    }

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->reflClass->getName() === Product::class) {
            return $targetTableAlias.".name NOT LIKE '【秘密の商品】%'";
        }
        return '';
    }
}