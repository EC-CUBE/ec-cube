<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service;

use Eccube\Service\CsvImportService;

/**
 * Copyright (C) 2012-2014 David de Boer <david@ddeboer.nl>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * CsvReaserTest より移植
 *
 * @see https://github.com/ddeboer/data-import/blob/master/tests/Reader/CsvReaderTest.php
 */
class CsvImportServiceTest extends AbstractServiceTestCase
{
    public function testReadCsvFileWithColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_column_headers.csv');
        $CsvImportService = new CsvImportService($file);
        $CsvImportService->setHeaderRowNumber(0);

        $this->assertEquals(['id', 'number', 'description'], $CsvImportService->getFields());

        foreach ($CsvImportService as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }

        $this->assertEquals(
            [
                'id' => 6,
                'number' => '456',
                'description' => 'Another description',
            ],
            $CsvImportService->getRow(2)
        );
    }

    public function testReadCsvFileWithoutColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_no_column_headers.csv');
        $CsvImportService = new CsvImportService($file);

        $this->assertEmpty($CsvImportService->getColumnHeaders());
    }

    public function testReadCsvFileWithManualColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_no_column_headers.csv');
        $CsvImportService = new CsvImportService($file);
        $CsvImportService->setColumnHeaders(['id', 'number', 'description']);

        foreach ($CsvImportService as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }
    }

    public function testReadCsvFileWithTrailingBlankLines()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_blank_lines.csv');

        $CsvImportService = new CsvImportService($file);
        $CsvImportService->setColumnHeaders(['id', 'number', 'description']);
        $blank_line = [0 => null];
        foreach ($CsvImportService as $row) {
            if ($row === $blank_line) {
                continue;
            }
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }
    }

    public function testCountWithoutHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_no_column_headers.csv');
        $CsvImportService = new CsvImportService($file);
        $this->assertEquals(3, $CsvImportService->count());
    }

    public function testCountWithHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_column_headers.csv');
        $CsvImportService = new CsvImportService($file);
        $CsvImportService->setHeaderRowNumber(0);
        $this->assertEquals(3, $CsvImportService->count(), 'Row count should not include header');
    }

    public function testCountDoesNotMoveFilePointer()
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/data_column_headers.csv');
        $CsvImportService = new CsvImportService($file);
        $CsvImportService->setHeaderRowNumber(0);

        $key_before_count = $CsvImportService->key();
        $CsvImportService->count();
        $key_after_count = $CsvImportService->key();

        $this->assertEquals($key_after_count, $key_before_count);
    }

    public function testLineBreaks()
    {
        $reader = $this->getReader('data_cr_breaks.csv');
        $this->assertCount(3, $reader);
    }

    public function testDuplicateHeadersMerge()
    {
        $reader = $this->getReader('data_column_headers_duplicates.csv');
        $reader->setHeaderRowNumber(0, CsvImportService::DUPLICATE_HEADERS_MERGE);
        $reader->rewind();
        $current = $reader->current();

        $this->assertCount(4, $reader->getColumnHeaders());

        $expected = [
            'id' => '50',
            'description' => ['First', 'Second', 'Third'],
            'details' => ['Details1', 'Details2'],
            'last' => 'Last one',
        ];
        $this->assertEquals($expected, $current);
    }

    protected function getReader($filename)
    {
        $file = new \SplFileObject(__DIR__.'/../../../Fixtures/'.$filename);

        return new CsvImportService($file);
    }
}
