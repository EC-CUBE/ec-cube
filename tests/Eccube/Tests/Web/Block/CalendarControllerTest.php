<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Web\Block;

use Carbon\Carbon;
use Eccube\Entity\Calendar;
use Eccube\Tests\Web\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

final class CalendarControllerTest extends AbstractWebTestCase
{
    protected function tearDown(): void
    {
        // Carbon::setTestNow() はプロセス全体に効くため, 後続のテストへ漏れないよう必ず戻す.
        // テストメソッドの末尾で戻すとアサーション失敗時に到達せず, 以降のテストを巻き込む.
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testRoutingCalendar()
    {
        $this->client->request(Request::METHOD_GET, '/block/calendar');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testThisMonthTitle()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->startOfMonth()->format('Y年n月');
        $this->actual = $crawler->filter('#this-month-title')->text();
        $this->verify();
    }

    public function testNextMonthTitle()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->startOfMonth()->addMonth()->format('Y年n月');
        $this->actual = $crawler->filter('#next-month-title')->text();
        $this->verify();
    }

    public function testTodayAndHolidayStyle()
    {
        $Calendar = new Calendar();
        $Calendar->setTitle('今日かつ定休日のパターン')
            ->setHoliday(new \DateTime(Carbon::now()->format('Y-m-d')));
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->format('j');
        $this->actual = $crawler->filter('#today-and-holiday')->text();
        $this->verify();
    }

    public function testHolidayStyle()
    {
        // 土日以外の日を取得
        $targetHoliday = Carbon::now()->addDay();

        if ($targetHoliday->isSaturday()) {
            if (!$targetHoliday->copy()->addDays(2)->isCurrentMonth()) {
                $targetHoliday = $targetHoliday->addDays(-2);
            } else {
                $targetHoliday = $targetHoliday->addDays(2);
            }
        } elseif ($targetHoliday->isSunday()) {
            if (!$targetHoliday->copy()->addDay()->isCurrentMonth()) {
                $targetHoliday = $targetHoliday->addDays(-2);
            } else {
                $targetHoliday = $targetHoliday->addDays(1);
            }
        }

        $Calendar = new Calendar();
        $Calendar->setTitle('今日ではない定休日のパターン')
            ->setHoliday(new \DateTime($targetHoliday->format('Y-m-d')));
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));
        $this->expected = $targetHoliday->format('j');
        $this->actual = $crawler->filter(($targetHoliday->isCurrentMonth() ? '#this-month-holiday-' : '#next-month-holiday-').$this->expected)->text();
        $this->verify();
    }

    #[DataProvider('provideTodayForWeekendHolidaysStyle')]
    public function testWeekendHolidaysStyle(string $today): void
    {
        Carbon::setTestNow($today);

        // 当月の最初の土曜日・日曜日を取得
        $saturday = Carbon::now()->firstOfMonth(Carbon::SATURDAY);
        $sunday = Carbon::now()->firstOfMonth(Carbon::SUNDAY);

        // 期待値と CSS セレクタを同じ変数から組み立てるため, 日付の取得自体が誤っていても
        // アサーションは通ってしまう. 取得結果が本当に土日で当月かをここで担保する.
        $this->assertTrue($saturday->isSaturday(), '当月の最初の土曜日が取得できていない');
        $this->assertTrue($sunday->isSunday(), '当月の最初の日曜日が取得できていない');
        $this->assertLessThanOrEqual(7, (int) $saturday->format('j'), '最初の土曜日は 7 日以内のはず');
        $this->assertLessThanOrEqual(7, (int) $sunday->format('j'), '最初の日曜日は 7 日以内のはず');

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));

        // 土曜日の確認
        $this->expected = $saturday->format('j');
        $this->actual = $crawler->filter($this->weekendCellSelector($saturday))->text();
        $this->verify();

        // 日曜日の確認
        $this->expected = $sunday->format('j');
        $this->actual = $crawler->filter($this->weekendCellSelector($sunday))->text();
        $this->verify();
    }

    /**
     * 土日のセルを指す CSS セレクタを返す.
     *
     * 対象日が今日の場合, テンプレートは #this-month-holiday-X より #today を優先するため ID が変わる.
     * (このテストでは定休日を登録していないので #today-and-holiday にはならない)
     *
     * @see src/Eccube/Resource/template/default/Block/calendar.twig
     */
    private function weekendCellSelector(Carbon $day): string
    {
        return $day->isSameDay(Carbon::now())
            ? '#today'
            : '#this-month-holiday-'.$day->format('j');
    }

    /**
     * 月初の曜日と, 今日が土日と重なるかの組み合わせを網羅する.
     *
     * @return array<string, array{string}>
     */
    public static function provideTodayForWeekendHolidaysStyle(): array
    {
        return [
            '月初が日曜・今日は平日' => ['2025-06-03'],
            '月初が日曜・今日が第一日曜(月初と同日)' => ['2025-06-01'],
            '月初が日曜・今日が第一土曜' => ['2025-06-07'],
            '月初が土曜・今日が第一土曜(月初と同日)' => ['2025-11-01'],
            '月初が土曜・今日が第一日曜' => ['2025-11-02'],
            '月初が水曜・今日は平日' => ['2025-10-15'],
            '月初が日曜で28日まで・今日は平日' => ['2026-02-04'],
        ];
    }

    public function testTodayStyle()
    {
        $today = new \DateTime();

        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('block_calendar'));
        $this->expected = $today->format('j');
        $this->actual = $crawler->filter('#today')->text();
        $this->verify();
    }
}
