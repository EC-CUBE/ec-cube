<?php

namespace Eccube;

class RouteMap
{
    public function getMap() {
        return array(
            'index' => array(
                'method' => 'GET|POST',
                'dir' => '',
                'class' => 'Index',
                'action' => 'process',
                'template' => 'index',
            ),
            'products/list' => array(
                'dir' => 'products',
                'class' => 'ProductsList',
                'action' => 'process',
                'template' => 'products_list',
            ),
            'products/detail' => array(
                'dir' => 'products',
                'class' => 'Detail',
                'action' => 'process',
                'template' => 'products_detail',
            ),
            'admin/index' => array(
                'dir' => '',
                'class' => 'Index',
                'action' => 'process',
                'template' => 'admin_index',
            ),
            'admin/home' => array(
                'dir' => '',
                'class' => 'Home',
                'action' => 'process',
                'template' => 'admin_home',
            ),
            'admin/products/class' => array(
                'dir' => 'products',
                'class' => 'class',
                'action' => 'action',
                'ssl' => true,
                'template' => 'admin_product_class',
            ),
        );
    }

}