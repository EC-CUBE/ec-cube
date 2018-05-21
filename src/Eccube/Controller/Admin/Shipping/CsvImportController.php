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

namespace Eccube\Controller\Admin\Shipping;

use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Eccube\Form\Type\Admin\CsvImportType;
use Symfony\Component\Filesystem\Filesystem;
use Eccube\Exception\CsvImportException;

class CsvImportController extends AbstractController
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $headers = [
        '商品ID',
    ];

    /**
     * @var array
     */
    protected $descriptions = [
        '項目は未決定',
    ];

    /**
     * @var array
     */
    protected $columns = [
        'id',
    ];

    /**
     * 商品登録CSVアップロード
     *
     * TODO: This function is not implemented yet
     *
     * @Route("/%eccube_admin_route%/shipping/shipping_csv_upload", name="admin_shipping_csv_import")
     * @Template("@admin/Shipping/csv_shipping.twig")
     */
    public function csvShipping()
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();
        $headers = [];

        try {
            $headers = $this->getMappedDescriptionHeaders();

            if (!empty($this->fileName)) {
                $fs = new Filesystem();
                $fs->remove($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$this->fileName);
            }
        } catch (\Exception $e) {
            // エラーが発生しても無視する
        }

        if ($this->hasErrors()) {
            if ($this->entityManager) {
                $this->entityManager->getConnection()->rollback();
            }
        }

        return [
            'form' => $form->createView(),
            'headers' => $headers,
            'errors' => $this->getErrors(),
        ];
    }

    /**
     * TODO: This function is not implemented yet
     *
     * @return array
     *
     * @throws CsvImportException
     */
    public function getMappedDescriptionHeaders()
    {
        if (count($this->headers) != count($this->descriptions)) {
            throw new CsvImportException('Incorrect mapping csv header & description');
        }

        return array_combine($this->headers, $this->descriptions);
    }

    /**
     * @return array
     */
    protected function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    protected function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }
}
