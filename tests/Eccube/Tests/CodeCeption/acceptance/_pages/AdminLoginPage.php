<?php

class AdminLoginPage
{
    // include url of current page
    public static $URL = '/admin';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    /**
     * @return AdminLoginPage
     */
    public static function of(AcceptanceTester $I)
    {
        return new static($I);
    }

    public function login($name, $password)
    {
        $I = $this->acceptanceTester;

        $I->amOnPage(self::$URL);
        $I->fillField('ID', $name);
        $I->fillField('PASSWORD', $password);
        $csrf_token = $I->grabValueFrom("Form#form1 input[name='_csrf_token']");
        $I->sendAjaxPostRequest('/admin/login_check',
            array('login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => $csrf_token));

        return $this;
    }
}