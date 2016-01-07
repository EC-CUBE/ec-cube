<?php

namespace Eccube\Tests\Util;

use Eccube\Util\Str;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Str test cases.
 *
 * @author Kentaro Ohkouchi
 */
class StrTest extends \PHPUnit_Framework_TestCase
{
    private $actual;
    private $expected;

    public function testRandom()
    {
        $this->expected = 16;
        $result = Str::random();
        $this->actual = strlen($result);
        // デフォルトは16桁
        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{16}/', $result) === 1);
    }

    public function testRandomWithParams()
    {
        $this->expected = 5;
        $result = Str::random($this->expected);
        $this->actual = strlen($result);

        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{'.$this->expected.'}/', $result) === 1);
    }

    public function testRandomException()
    {
        $this->expected = 'Unable to generate random string.';
        try {
            $result = Str::random(0);
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->actual = $e->getMessage();
        }
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testQuickRandom()
    {
        $this->expected = 16;
        $result = Str::quickRandom();
        $this->actual = strlen($result);
        // デフォルトは16桁
        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{16}/', $result) === 1);
    }

    public function testQuickRandomWithParams()
    {
        $this->expected = 5;
        $result = Str::QuickRandom($this->expected);
        $this->actual = strlen($result);

        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{'.$this->expected.'}/', $result) === 1);
    }


    public function testConvertLineFeed()
    {

        $this->expected = "aaaa\n";

        $param = "aaaa\r\n";
        $this->actual = Str::convertLineFeed($param);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\r";
        $this->actual = Str::convertLineFeed($param);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\n";
        $this->actual = Str::convertLineFeed($param);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testConvertLineFeedWithCrlf()
    {
        $this->expected = "aaaa\r\n";
        $lf = "\r\n";

        $param = "aaaa\n";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\r";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\r\n";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testConvertLineFeedWithMultiline()
    {
        $this->expected = "aaaa\nbbbb\ncccc\n";
        $lf = "\n";

        $param = "aaaa\nbbbb\ncccc\n";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\rbbbb\rcccc\r";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);

        $param = "aaaa\r\nbbbb\r\ncccc\r\n";
        $this->actual = Str::convertLineFeed($param, $lf);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testConvertLineFeedWithEmpty()
    {
        $this->expected = '';
        $this->actual = Str::convertLineFeed($this->expected);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testCharacterEncodingWithSJIS()
    {
        // 「京」は SJIS と EUC を誤認しない文字
        $text = mb_convert_encoding('京', 'SJIS', 'UTF-8');
        $this->expected = 'SJIS';
        $this->actual = Str::characterEncoding($text);
        $this->assertEquals($this->expected, $this->actual);

        // 検出順序を変更してみる
        $this->expected = 'SJIS-win';
        $this->actual = Str::characterEncoding($text, array('SJIS-win', 'UTF-8', 'SJIS', 'EUC-JP', 'ASCII', 'JIS'));
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testCharacterEncodingWithEuc()
    {
        // 「京」は SJIS と EUC を誤認しない文字
        $text = mb_convert_encoding('京', 'euc-jp', 'UTF-8');
        $this->expected = 'EUC-JP';
        $this->actual = Str::characterEncoding($text);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testCharacterEncodingWithUTF8()
    {
        // 「〠」は UTF-8 固有の文字
        // see http://www.qlosawa.sakura.ne.jp/language/binyu.html
        $text = mb_convert_encoding('〠', 'UTF-8', 'UTF-8');
        $this->expected = 'UTF-8';
        $this->actual = Str::characterEncoding($text);
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testCharacterEncodingWithNone()
    {
        // 「〠」は UTF-8 固有の文字
        $text = mb_convert_encoding('〠', 'UTF-8', 'UTF-8');
        $this->actual = Str::characterEncoding($text, array('SJIS-win', 'eucJP-win', 'ASCII')); // UTF-8 は検出しない
        $this->assertNull($this->actual);
    }

    public function testEllipsis()
    {
        $value = '一弍三4567890あいうえお';
        $this->expected = '一弍三4567890...';
        $this->actual = Str::ellipsis($value, 10, '...');
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testEllipsisWithShort()
    {
        $value = '一弍三';
        $this->expected = '一弍三';
        $this->actual = Str::ellipsis($value, 10, '...');
        $this->assertEquals($this->expected, $this->actual);
    }

    public function testTimeAgo()
    {
        $elapsedTime = new \DateTime();
        $date = $elapsedTime;
        $this->expected = '0秒前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT59S'));
        $this->expected = '59秒前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT60S'));
        $this->expected = '1分前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT59M59S'));
        $this->expected = '59分前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT59M60S'));
        $this->expected = '1時間前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT23H59M59S'));
        $this->expected = '23時間前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('PT23H59M60S'));
        $this->expected = '1日前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('P31DT23H59M59S'));
        $this->expected = '31日前';
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('P31DT23H59M60S'));
        $this->expected = date('Y/m/d', strtotime('- 32 days'));
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        $elapsedTime = new \DateTime();
        $date = $elapsedTime->sub(new \DateInterval('P1Y'));
        $this->expected = date('Y/m/d', strtotime('- 1 years'));
        $this->actual = Str::timeAgo($date);
        $this->assertEquals($this->expected, $this->actual);

        // 日付書式を引数に
        $this->expected = date('Y/m/d', strtotime('- 1 years'));
        $this->actual = Str::timeAgo(date('Y/m/d', strtotime('- 1 years')));
        $this->assertEquals($this->expected, $this->actual);

        // 引数が空
        $this->actual = Str::timeAgo('');
        $this->assertEmpty($this->actual);
    }

    public function testIsBlank()
    {
        $text = '';
        $this->actual = Str::isBlank($text);
        $this->assertTrue($this->actual);

        $text = null;
        $this->actual = Str::isBlank($text);
        $this->assertTrue($this->actual);

        $text = 0;
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = '1';
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = '      ';
        $this->actual = Str::isBlank($text);
        $this->assertTrue($this->actual);

        // $greedy = true のテスト
        $text = '　';
        $this->actual = Str::isBlank($text, true);
        $this->assertTrue($this->actual);

        // $greedy = true のテスト
        $text = '　a　';
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual, true);

        // $greedy = true のテスト
        $text = "　\n\t　";
        $this->actual = Str::isBlank($text, true);
        $this->assertTrue($this->actual);

        // $greedy = true のテスト
        $text = " \t　\n\r\x0B\0"; // 全ての空白文字
        $this->actual = Str::isBlank($text, true);
        $this->assertTrue($this->actual);
    }

    public function testIsNotBlank()
    {
        $text = '';
        $this->actual = Str::isNotBlank($text);
        $this->assertFalse($this->actual);

        $text = null;
        $this->actual = Str::isNotBlank($text);
        $this->assertFalse($this->actual);

        $text = 1;
        $this->actual = Str::isNotBlank($text);
        $this->assertTrue($this->actual);

        $text = '1';
        $this->actual = Str::isNotBlank($text);
        $this->assertTrue($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithObject()
    {
        $text = new \stdClass();
        $this->actual = Str::isBlank($text);
        // E_USER_DEPRECATED がスローされるのでテストできないが false になるはず
        $this->assertFalse($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArray()
    {
        $text = array();
        $this->actual = Str::isBlank($text);
        // E_USER_DEPRECATED がスローされるのでテストできないが true になるはず
        $this->assertTrue($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArrayGreedy()
    {
        // $greedy = true のテスト
        $text = array(array('aa' => array('aa' => '')));
        $this->actual = Str::isBlank($text, true);
        // E_USER_DEPRECATED がスローされるのでテストできないが true になるはず
        $this->assertTrue($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArrayGreedy2()
    {
        // $greedy = true のテスト
        $text = array();
        $this->actual = Str::isBlank($text, true);
        // E_USER_DEPRECATED がスローされるのでテストできないが true になるはず
        $this->assertTrue($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArrayGreedy3()
    {
        // $greedy = true のテスト
        $text = array(array('aa' => array('aa' => 'a')));
        $this->actual = Str::isBlank($text, true);
        // E_USER_DEPRECATED がスローされるのでテストできないが false になるはず
        $this->assertFalse($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsNotBlankWithArray()
    {
        $text = array();
        $this->actual = Str::isNotBlank($text);
        // E_USER_DEPRECATED がスローされるのでテストできないが false になるはず
        $this->assertFalse($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArrayCollectionEmpty()
    {
        $value = new ArrayCollection();
        $this->actual = Str::isBlank($value);
        // E_USER_DEPRECATED がスローされるのでテストできないが true になるはず
        $this->assertTrue($this->actual);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testIsBlankWithArrayCollectionNotEmpty()
    {
        $value = new ArrayCollection(array('a'));
        $this->actual = Str::isBlank($value);
        // E_USER_DEPRECATED がスローされるのでテストできないが false になるはず
        $this->assertFalse($this->actual);
    }

    public function testIsBlankWithNotGreedy()
    {
        // greedy = false のテスト
        $text = '      ';
        $this->actual = Str::isBlank($text);
        $this->assertTrue($this->actual);

        $text = '　';
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = '　a　';
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = "　\n\t　";
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = " \t　\n\r\x0B\0"; // 全ての空白文字
        $this->actual = Str::isBlank($text);
        $this->assertFalse($this->actual);

        $text = " \t\n\r\x0B\0"; // ASCII の空白文字
        $this->actual = Str::isBlank($text);
        $this->assertTrue($this->actual);
    }

    public function testTrimAll()
    {
        $text = '     a　';
        $this->expected = 'a';
        $this->actual = Str::trimAll($text);
        $this->assertEquals($this->expected, $this->actual);

        $text = '     a　a　';
        $this->expected = 'a　a';
        $this->actual = Str::trimAll($text);
        $this->assertEquals($this->expected, $this->actual);

        $text = '';
        $this->actual = Str::trimAll($text);
        $this->assertNotNull($this->actual);
        $this->assertEmpty($this->actual);

        $text = null;
        $this->actual = Str::trimAll($text);
        $this->assertNull($this->actual);

        $text = 0;
        $this->expected = 0;
        $this->actual = Str::trimAll($text);
        $this->assertTrue($this->expected === $this->actual);

        $text = '0';
        $this->expected = '0';
        $this->actual = Str::trimAll($text);
        $this->assertTrue($this->expected === $this->actual);

        $text = " 0\n0\r\n\t";
        $this->expected = "0\n0";
        $this->actual = Str::trimAll($text);
        $this->assertTrue($this->expected === $this->actual);
    }
}
