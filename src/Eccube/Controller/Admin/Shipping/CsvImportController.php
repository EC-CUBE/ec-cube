<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License'csvimport.text.error.format'
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        '商品ID'
    ];

    /**
     * @var array
     */
    protected $descriptions = [
        '項目は未決定'
    ];

    /**
     * @var array
     */
    protected $columns = [
        'id'
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
                $fs->remove($this->eccubeConfig['eccube_csv_temp_realdir'] . '/' . $this->fileName);
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
     *
     * @return boolean
     */
    protected function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }
}