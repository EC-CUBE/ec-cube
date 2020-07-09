<?php


namespace Eccube\Tests\Form\Type\Admin;


use Eccube\Form\Type\Admin\SearchProductType;
use Symfony\Component\Form\FormInterface;

class SearchProductTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchProductType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    /**
     * EC-CUBE 4.0.4 以前のバージョンで互換性を保つため yyyy-MM-dd のフォーマットもチェック
     *
     * @dataProvider dataFormDateProvider
     *
     * @param string $formName
     * @param string $formValue
     * @param bool $result
     */
    public function testDateSearch(string $formName, string $formValue, bool $result)
    {
        $formData = [
            $formName => $formValue,
        ];

        $this->form->submit($formData);
        $this->assertEquals($result, $this->form->isValid());
    }

    /**
     * Data provider date form test.
     *
     * @return array
     */
    public function dataFormDateProvider()
    {
        return [
            ['create_date_start', '2020-07-09', true],
            ['create_date_start', '2020-07-09 09:00', true],
            ['create_date_start', '2020-07-09 aa', false],
            ['update_date_start', '2020-07-09', true],
            ['update_date_start', '2020-07-09 09:00', true],
            ['update_date_start', '2020-07-09 aa', false],
            ['create_date_end', '2020-07-09', true],
            ['create_date_end', '2020-07-09 09:00', true],
            ['create_date_end', '2020-07-09 aa', false],
            ['update_date_end', '2020-07-09', true],
            ['update_date_end', '2020-07-09 09:00', true],
            ['update_date_end', '2020-07-09 aa', false],
        ];
    }
}
