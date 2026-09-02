---
name: eccube-mail
description: EC-CUBE 4.4 のメール送信（MailService・メールテンプレート・MailHistory）を実装・改修するときの規約。「メールを送って」「メール送信処理を追加して」「メールテンプレートを足して」「注文確定メールをカスタマイズして」「送信履歴を残して」などと言われたとき、または src/Eccube/Service/MailService・メール用 twig を作成・編集するときに使用する。
---

# メール送信規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Service/MailService.php`, `app/Customize/Service/**`, メール用 twig（`Resource/template/**/Mail/`, `app/template/**/Mail/`）
**前提**: Symfony 7.4 / PHP 8.2+ / Symfony Mailer（`symfony/mailer`）/ Twig 3.x

> 目的: メール送信は「件名・本文の組み立て（Twig）」「送信（Mailer）」「送信履歴（MailHistory）」「差出人設定（BaseInfo）」が
> 一体で動く。これを Service に集約し、コントローラやテンプレートに送信ロジックを散らさないこと。
> Skill `eccube-service`（業務ロジックの置き場所）と対で使う。

## 対象 / 前提（構成要素）

| 要素 | 実体 | 役割 |
|---|---|---|
| `MailService` | `src/Eccube/Service/MailService.php` | メール送信の入口。`send〜Mail()` の各公開メソッドを持つ |
| `MailTemplate` | Entity `dtb_mail_template`（STI, `discriminator_type`） | 件名（`mail_subject`）と Twig ファイル名（`file_name`）を保持 |
| `MailHistory` | Entity `dtb_mail_history`（STI, `discriminator_type`） | 送信済みメールの件名・本文・HTML 本文・送信日時・`Order` を記録 |
| メール用 twig | `Resource/template/<default|admin>/Mail/*.twig` | 本文テンプレート（プレーンテキスト）。`*.html.twig` があれば HTML メールにもなる |
| `BaseInfo` | Entity `dtb_base_info` | 差出人・返信先・ReturnPath（`email01`〜`email04`）と店名を保持 |
| トランスポート | `MailerInterface`（DSN は `%env(MAILER_DSN)%`） | 実際の送信。`app/config/eccube/packages/mailer.yaml` |

## 基本ルール

- **メール送信は必ず `MailService` 経由**。コントローラから `MailerInterface` を直接叩いて `Email` を組み立てない。
  既存の送信は `MailService::sendOrderMail()` / `sendShippingNotifyMail()` / `sendCustomerCompleteMail()` 等の公開メソッドに集約されている。
- **件名・本文は `MailTemplate` ＋ Twig から組み立てる**。テンプレート ID は `EccubeConfig`（`app/config/eccube/packages/eccube.yaml`）で固定されている。
  - 例: `eccube_order_mail_template_id: 1`（注文受付）, `eccube_shipping_notify_mail_template_id: 8`（出荷通知）等。
  - `MailService` は `$this->mailTemplateRepository->find($this->eccubeConfig['eccube_order_mail_template_id'])` で取得し、`$MailTemplate->getFileName()` を Twig に渡してレンダリングする。
- **差出人・返信先は `BaseInfo` を使う**（ハードコード禁止）。役割は固定:
  - `email01` … **From / Bcc**（送信元メールアドレス。多くの送信で自身を Bcc にも入れる）
  - `email02` … お問い合わせ系の From / Bcc / ReplyTo（`sendContactMail` で使用）
  - `email03` … **ReplyTo**（返信先）
  - `email04` … **ReturnPath**（送信エラー通知先）
- **宛先は `convertRFCViolatingEmail()` を通す**。RFC 違反アドレス（`eccube_rfc_email_check=false` のとき）の local part をクォートして `Address` を返す。生の文字列を `->to()` に渡さない。
- **送信失敗はログに記録して握りつぶす**。送信は `try { $this->mailer->send($message); } catch (TransportExceptionInterface $e) { log_critical($e->getMessage()); }` の形。送信失敗で受注処理全体を止めない設計（受注メール等）。
- **送信履歴（`MailHistory`）は受注に紐づくメールだけ記録する**。現状 `MailHistory` を作るのは `sendOrderMail` と `sendShippingNotifyMail` の 2 つ。会員系メールは履歴を残していない（`MailHistory` の関連は `Order` のみで会員に紐づける口がない）。
- **`MailHistory` は persist のみ、`flush()` は呼び出し側**。`MailService` は `$this->mailHistoryRepository->save($MailHistory)`（= `EntityManager::persist`）までで、`flush()` しない。呼び出し側（例: `ShoppingController` は `sendOrderMail` の直後に `$this->entityManager->flush()`）で確定させる。履歴を保存する新メソッドを足すときも flush は呼び出し側に委ねる。
- **テンプレートのカスタマイズはコア改変ではなく上書き**。`app/template/<コード>/Mail/order.twig` に置けばコアの `Resource/template/default/Mail/order.twig` を上書きできる（管理画面のメール設定でテンプレート本文を編集する運用もある）。
- **送信前に必ず `EccubeEvents::MAIL_*` を dispatch する**。プラグイン/カスタマイズが件名・本文・宛先を差し替えられるよう、`EventArgs` に `message` 等を載せて発火してから送る（後述）。

## 実装パターン

### 既存の送信メソッドの型（`MailService` 内）

`sendCustomerCompleteMail` 等はすべて同じ骨格。新しいメールを足すときもこの形に揃える。

```php
public function sendOrderMail(Order $Order): Email
{
    log_info('受注メール送信開始');

    // 1) テンプレート取得（ID は EccubeConfig で固定）
    $MailTemplate = $this->mailTemplateRepository->find(
        $this->eccubeConfig['eccube_order_mail_template_id']
    );

    // 2) 本文を Twig でレンダリング（テンプレートのファイル名を使う）
    $body = $this->twig->render($MailTemplate->getFileName(), [
        'Order' => $Order,
    ]);

    // 3) Email を組み立て。差出人は BaseInfo、宛先は convertRFCViolatingEmail を通す
    $message = (new Email())
        ->subject('['.$this->BaseInfo->getShopName().'] '.$MailTemplate->getMailSubject())
        ->from(new Address($this->BaseInfo->getEmail01(), $this->BaseInfo->getShopName()))
        ->to($this->convertRFCViolatingEmail($Order->getEmail()))
        ->bcc($this->BaseInfo->getEmail01())
        ->replyTo($this->BaseInfo->getEmail03())
        ->returnPath($this->BaseInfo->getEmail04());

    // 4) HTML テンプレート（*.html.twig）があれば multipart 化
    $htmlFileName = $this->getHtmlTemplate($MailTemplate->getFileName());
    if (!is_null($htmlFileName)) {
        $htmlBody = $this->twig->render($htmlFileName, ['Order' => $Order]);
        $message->text($body)->html($htmlBody);
    } else {
        $message->text($body);
    }

    // 5) 送信前にイベントを発火（プラグインの差し替え口）
    $event = new EventArgs([
        'message' => $message,
        'Order' => $Order,
        'MailTemplate' => $MailTemplate,
        'BaseInfo' => $this->BaseInfo,
    ]);
    $this->eventDispatcher->dispatch($event, EccubeEvents::MAIL_ORDER);

    // 6) 送信。失敗はログに記録して握りつぶす
    try {
        $this->mailer->send($message);
    } catch (TransportExceptionInterface $e) {
        log_critical($e->getMessage());
    }

    // 7) 受注メールは MailHistory に記録（persist のみ。flush は呼び出し側）
    $MailHistory = (new MailHistory())
        ->setMailSubject($message->getSubject())
        ->setMailBody($message->getTextBody())
        ->setOrder($Order)
        ->setSendDate(new \DateTime());
    if (!empty($message->getHtmlBody())) {
        $MailHistory->setMailHtmlBody($message->getHtmlBody());
    }
    $this->mailHistoryRepository->save($MailHistory);

    log_info('受注メール送信完了');

    return $message;
}
```

### HTML メールの規約（`getHtmlTemplate`）

HTML メールは別テンプレートを **命名規約で発見**する。プレーンテキスト `Mail/order.twig` に対し
`Mail/order.html.twig` が存在すれば自動的に HTML パートを付ける（`getHtmlTemplate()` が
`<basename>.html.<ext>` を組み立てて `$this->twig->getLoader()->exists()` で判定）。
HTML メールを足したいときは **同じディレクトリに `*.html.twig` を置くだけ**。コード側の分岐は不要。

### メール本文 twig の書き方

`Resource/template/default/Mail/*.twig` を踏襲する。

```twig
{% autoescape 'safe_textmail' %}
{{ Order.name01 }} {{ Order.name02 }} 様

ご注文番号：{{ Order.order_no }}
お支払い合計：{{ Order.payment_total|price }}
{% endautoescape %}
```

- **プレーンテキストメールは `{% autoescape 'safe_textmail' %}` で囲む**（`SafeTextmailEscaperExtension`）。通常の HTML エスケープはテキストメールに不要・有害なため専用エスケープ戦略を使う。
- 渡せる変数は `MailService` がレンダリング時に渡したもの（`Order` / `Customer` / `BaseInfo` / `data` 等）だけ。新しい変数を使うなら送信メソッド側の `$this->twig->render(..., [...])` に追加する。
- HTML メール（`*.html.twig`）はこの `autoescape` を使わず、通常の HTML として書く。

### イベント定数（`EccubeEvents`）

送信前に dispatch する `MAIL_*` 定数（`src/Eccube/Event/EccubeEvents.php`）:
`MAIL_ORDER` / `MAIL_SHIPPING_NOTIFY` / `MAIL_CONTACT` / `MAIL_CUSTOMER_CONFIRM` /
`MAIL_CUSTOMER_COMPLETE` / `MAIL_CUSTOMER_WITHDRAW` / `MAIL_ADMIN_CUSTOMER_CONFIRM` /
`MAIL_ADMIN_ORDER` / `MAIL_PASSWORD_RESET` / `MAIL_PASSWORD_RESET_COMPLETE` /
`MAIL_CUSTOMER_CHANGE_NOTIFY`。
購読してメールを差し替えるときは Skill `eccube-event-subscriber` を参照（`EventArgs` から `message` を取り出して件名・宛先・本文を変更）。

### プラグイン / カスタマイズから独自メールを送る

- **既存メールの差し替え**: 新規送信メソッドを足さず、対応する `MAIL_*` イベントを購読して `EventArgs` の `message` を加工する（件名・宛先・本文・添付の追加など）。これが第一選択。
- **独自メールの新規送信**: `app/Customize/Service/` または `Plugin\{Code}\Service\` に Service を作り、`MailerInterface` ＋ `Environment`（Twig）＋ `BaseInfoRepository` を DI して上記の型に倣う。
  - 本文 twig はプラグインなら `Plugin\{Code}` のテンプレートパス、カスタマイズなら `app/template/<コード>/Mail/` に置く。
  - 差出人は `BaseInfo` を使い、宛先は `convertRFCViolatingEmail()`（または同等の処理）を通す。
  - 受注に紐づくなら送信後に `MailHistory` を作って `Order` に関連付ける。

## よくある間違い

- ❌ コントローラで `MailerInterface` を直接呼んで `Email` を組み立てる → ✅ `MailService` の送信メソッドに集約する
- ❌ 差出人・返信先をハードコードする → ✅ `BaseInfo` の `email01`（From/Bcc）/ `email03`（ReplyTo）/ `email04`（ReturnPath）を使う
- ❌ 宛先に生の文字列を `->to($email)` で渡す → ✅ `convertRFCViolatingEmail($email)` を通す
- ❌ 件名・本文を PHP 内で文字列連結する → ✅ `MailTemplate` ＋ Twig（`render()`）で組み立てる
- ❌ プレーンテキストメール twig を素で書く / 通常の HTML エスケープをかける → ✅ `{% autoescape 'safe_textmail' %}` で囲む
- ❌ HTML メール用に送信メソッドへ分岐を足す → ✅ 同名 `*.html.twig` を置けば `getHtmlTemplate()` が自動で multipart 化する
- ❌ 送信失敗で例外を投げて受注処理を止める → ✅ `TransportExceptionInterface` を catch して `log_critical` で記録（既存の方針に合わせる）
- ❌ `MailService` 内で `flush()` する／会員系メールを `MailHistory` に残す → ✅ persist まで（`flush()` は呼び出し側）。関連は `Order` と `Creator`(Member) のみ
- ❌ コアの `Resource/template/default/Mail/*.twig` を直接書き換える → ✅ `app/template/<コード>/Mail/` で上書きする
- ❌ 送信前のイベント dispatch を省く → ✅ プラグインの差し替え口として `EccubeEvents::MAIL_*` を必ず発火する

## 実行・確認方法

QA ツール（PHPUnit / PHPStan / PHP-CS-Fixer / Rector）の実行方法は AGENTS.md「開発コマンド」を参照。

- 送信トランスポートは `MAILER_DSN`（`app/config/eccube/packages/mailer.yaml` の `%env(MAILER_DSN)%`）で決まる。デフォルトは `null://null`（送信しない）。
- `docker-compose.yml` には mailcatcher コンテナが含まれるため、開発環境では `MAILER_DSN` を mailcatcher（SMTP）に向ければ、送信メールをブラウザ UI で確認できる。
- 送信履歴は管理画面（受注詳細のメール履歴）および `dtb_mail_history` テーブルで確認する。

---

実装・改修後は、Skill `eccube-service`（業務ロジックの責務分離）・`eccube-event-subscriber`（差し替え）・
`eccube-twig-template`（テンプレートのエスケープ）と `eccube-review-responsibility` で点検すること。
