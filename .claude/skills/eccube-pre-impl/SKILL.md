---
name: eccube-pre-impl
description: 新規機能の設計・実装を始める直前のチェックリスト。「〜を作りたい」「〜機能を追加したい」「実装する前に確認して」「設計から相談したい」「どう作ればいいか相談したい」「Issue に対応して」「#NNNN を対応して」「この Issue をやって」などと言われたとき、既存コードの調査より先に該当レイヤの規約 Skill を読むよう誘導する。設計フェーズと実装フェーズの橋渡し用。
---

# 実装時の注意（EC-CUBE 4.4・全レイヤ）

**対象**: 新規機能・カスタマイズ・プラグイン開発の設計〜実装着手前
**使い方**: **既存コードの調査・grep より先にこのファイルを読む。** 該当レイヤの節だけ見ればよい。

> レイヤごとに Skill を分けて索引から辿らせる作りでは、入口が発火しても
> **半数が個別 Skill を読まずに回答を終える**ため規約が届かなかった。
> このファイルは全レイヤの注意を 1 か所に集約している（実測: 規約の到達が 3 倍）。

## 0. まず置き場を決める

- 本体へのコントリビュート（Issue 対応・機能追加・バグ修正）なら **`src/Eccube/`** を改修する。
- `app/Customize/` は**店舗のプロジェクト固有コード用**。本体の機能追加には使わない。
- 判別できない依頼なら実装を始める前に確認する（**間違えると成果物がまるごと無駄になる**）。
- 実装したら該当レイヤのテストを書く（`tests/Eccube/Tests/`）。

## 0-1. 全レイヤ共通

- 引数・戻り値に型宣言を付ける（PHPStan level 6）。PHP ファイル先頭のライセンスヘッダは必須。
- Entity のマッピングは PHP8 属性 `#[ORM\...]`。ルーティングは `#[Route]` 属性。
- 実装後: `vendor/bin/phpstan analyse src` と `vendor/bin/php-cs-fixer fix` を通す。
- 実装が一区切りついたら `eccube-review-responsibility` で責務分離・セキュリティを横断点検する。

---

## セキュリティ（認証・認可・CSRF・IDOR）

**触るとき**: 認可・CSRF・IDOR・セキュリティ点検

- ❌ 管理アクションを `%eccube_admin_route%` 配下**以外**に置く → ✅ 配下に置き admin firewall の保護下にする
- ❌ フォームを介さない POST/DELETE/Ajax で CSRF 未検証 → ✅ `$this->isTokenValid()` を呼ぶ（GET 以外）
- ❌ Ajax 専用アクションで XHR 以外も受け付ける → ✅ CSRF 検証に加え **`$request->isXmlHttpRequest()`** を併用し XHR に限定する
- ❌ フロントで `{id}` から取得したエンティティを所有権チェックせず編集/削除（**IDOR**）
- ❌ パスワード変更・退会など重要操作を `IS_AUTHENTICATED_REMEMBERED` で許可
- ❌ 独自 Voter で「対象外」を `ACCESS_DENIED` で返す → ✅ 対象外は `ACCESS_ABSTAIN`（unanimous 戦略で誤拒否を防ぐ）
- ❌ 自前でパスワードをハッシュ/平文比較 → ✅ `PasswordHasher` 経由に統一
- ❌ ユーザー入力を Twig で `|raw` 出力 → ✅ エスケープを効かせる（Skill `eccube-twig-template`）
- ❌ ファイル操作を伴う管理ルートを新設して `eccube_restrict_file_upload` を考慮しない → ✅ 遮断対象（`eccube_restrict_file_upload_urls`）に含めるべきか検討する
- ❌ ユーザー指定のパスをそのまま読み書き（**ディレクトリトラバーサル**）→ ✅ `..` を拒否し `realpath()` で解決、許可ベース配下かを検証する（`FileController::checkDir()` が手本）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-security/SKILL.md`

## コントローラ

**触るとき**: 画面・ルーティング・アクション

- ❌ コントローラ内に金額/在庫/送料の計算ロジック → ✅ Service に移し、コントローラは結果を受け取るだけ
- ❌ アクション内で `$em->persist()`/`$em->flush()` を直書きして業務処理 → ✅ Service のメソッドに集約
- ❌ 複数アクションに同じ処理をコピペ → ✅ Service の 1 メソッドに共通化
- ❌ 具象クラス型ヒントで密結合 → ✅ インターフェース型ヒント＋コンストラクタ DI
- ❌ 削除/Ajax 等の状態変更でトークン未検証 → ✅ `$this->isTokenValid()` を呼ぶ（GET 以外）
- ❌ 戻り値を捨てた `isTokenValid();` を「CSRF 未検証」と誤読 → ✅ 無効時は例外を投げるので bare 呼び出しで検証は成立する。`if (!isTokenValid())` の false 分岐はデッドコード
- ❌ `#[Template]` 付きアクションが常に再描画されると前提する → ✅ engage するのは配列を返したときだけ。Response/Redirect を返すパスでは描画されない
- ❌ `executePurchaseFlow()` を複数回呼んで 2 回目以降の `FlowResult` を無視する → ✅ 毎回 `hasError()`/`hasWarning()` を同じに分岐させる

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-controller/SKILL.md`

## サービス

**触るとき**: 業務ロジック・Service 切り出し

- ❌ Service が Controller を `use` する → ✅ 依存は一方向（Controller → Service）
- ❌ 1 つの Service に無関係な処理を寄せ集める → ✅ 単一責任で分割
- ❌ `Request` を Service に渡す → ✅ 必要な値だけを引数で渡す
- ❌ ループ内で毎回 `flush()` → ✅ まとめて `flush()`（トランザクション境界を意識）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-service/SKILL.md`

## Entity

**触るとき**: エンティティ・テーブル・カラム追加

- ❌ カラムを足したので ALTER マイグレーションを書く → ✅ 属性を足すだけ（`schema:update` が反映）。マイグレーションは INSERT・型変更等に限る
- ❌ 在庫引当・採番・ポイント付与などの受注処理をエンティティに書く → ✅ PurchaseFlow / Service へ。エンティティは自身の状態から導く計算/判定まで
- ❌ 金額 getter の戻り値を int/float 扱い → ✅ DECIMAL は `?string`（getter は `string`）。型宣言・代入も合わせる
- ❌ 金額を float で四則演算（丸め誤差）→ ✅ `bcmath`（`bcadd` / `bcmul` / `bccomp`、スケール 2）で計算する
- ❌ `create_date` / `update_date` を自前の `#[ORM\PrePersist]` でセット → ✅ `SaveEventSubscriber` が自動セットするので二重実装
- ❌ 他エンティティへの関連で親削除時の挙動を未決定 → ✅ FK は既定で削除を止めるので、未指定だと親の削除が FK 違反で失敗する。`onDelete` を指定するか Service 側で後始末する（コアは大半が後者）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-entity/SKILL.md`

## Repository

**触るとき**: 検索・一覧・クエリ

- ❌ 生 SQL の文字列連結・値の直挿し → ✅ QueryBuilder ＋ `setParameter()` バインド
- ❌ Repository に業務ロジックを書く → ✅ データアクセスに徹し、ロジックは Service
- ❌ `ServiceEntityRepository` を直接継承 → ✅ `AbstractRepository<T>` を継承
- ❌ オーバーライドで親と異なるシグネチャ → ✅ 親シグネチャを厳守
- ❌ 画面表示の一覧・関連取得を無制限に全件取得（件数が際限なく増え得る）→ ✅ ページング（Paginator 用に QueryBuilder を返す）か上限を設ける
- ❌ join 先への絞り込みを EXISTS 部分クエリへ移すとき、その別名に掛かっていた既存の制約を引き継がない → ✅ 同じ制約を EXISTS 内に再掲し、集計・出力側の母集団と一致させる
- ❌ 1 対多の範囲絞り込みで下限・上限を独立した EXISTS 2 本に分ける（別々の子行が満たせばヒットしてしまう）→ ✅ 同一の子行に両条件を要求するなら EXISTS 1 本にまとめる

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-repository/SKILL.md`

## FormType

**触るとき**: 入力フォーム・バリデーション

- ❌ `getBlockPrefix()` の戻り値型を省略 → ✅ `: string` を付ける
- ❌ 既存フォームをコアで直接改変 → ✅ `FormTypeExtension`（app/Customize）で拡張
- ❌ 管理画面検索フォームで CSRF 無効化 → ✅ CSRF 保護を保つ
- ❌ 具象クラス依存 → ✅ コンストラクタ DI ＋ 必要なサービスの注入
- ❌ 既存フォームに二重送信防止/楽観ロック用の unmapped hidden を足し、サーバー側で値未送信を即エラー扱い → ✅ 値が空/未送信なら判定をスキップ（プログラム的 POST・既存テスト・外部連携を壊さない後方互換を保つ）
- ❌ 共通 FormType(RepeatedPasswordType 等)を子で使い `options.constraints` を渡す（親が定義した制約が全置換され消える） → ✅ 親の制約一式も再掲して付与する

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-formtype/SKILL.md`

## Twig / テンプレート

**触るとき**: テンプレート・Twig 拡張

- ❌ ユーザー入力・DB 値に `{{ value|raw }}` → ✅ `|raw` を外す。HTML が必要なら出力前にサニタイズ
- ❌ JS の中に `{{ value }}`（HTML エスケープのみ）→ ✅ `{{ value|escape('js') }}`
- ❌ `is_safe => ['html']` を付けた関数内で外部入力を未エスケープ連結 → ✅ `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- ❌ HTML を返すフィルタに `is_safe` を付け忘れ → ✅ 付ける（さもないと二重エスケープで `&lt;` 等が表示される）
- ❌ 上書きを `app/template/` 直下に置く / `@admin` 名前空間を付け忘れる → ✅ 正しいサブディレクトリ・名前空間に置く
- ❌ **管理画面テンプレートだから安全**と油断して `|raw` する → ✅ admin 配下も XSS シンク（過去の XSS 修正は管理画面テンプレートに多い）。DB/入力由来の値は admin でも必ずエスケープする
- ❌ テンプレートイベントにエンティティ永続化など業務処理を書く → ✅ 見た目調整のみ。業務は対応するコントローライベントへ
- ❌ inline `<script>` に素の `json_encode` で埋める → ✅ `</script>` で XSS。`|json_encode_safe`（JSON-LD は `|json_ld`）を使う。属性値には不可

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-twig-template/SKILL.md`

## イベント（Subscriber）

**触るとき**: イベント・フック・差し込み

- ❌ `getSubscribedEvents()` を非 static で定義 → ✅ `public static function` にする（さもないと登録されない）
- ❌ イベント名を文字列直書き（`'front.product.index.initialize'`）→ ✅ `EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE` 定数
- ❌ autoconfigure 済みなのに services.yaml で手動登録 → ✅ 登録しない（二重発火を防ぐ）
- ❌ EventArgs の第1引数に値を直接渡す → ✅ `['key' => $value]` の連想配列で渡し `getArgument('key')` で取る
- ❌ 優先度を「小さいほど先」と誤解 → ✅ **大きい数値が先**
- ❌ Kernel イベントでサブリクエストを除外し忘れる → ✅ `if (!$event->isMainRequest()) return;`
- ❌ テンプレートイベント／Doctrine イベントに業務ロジックを書き込む → ✅ Service へ委譲し、リスナーは薄く保つ

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-event-subscriber/SKILL.md`

## マイグレーション

**触るとき**: カラム追加の要否判断・型変更・マスタ投入

- ❌ カラムを足したので `ALTER TABLE ... ADD COLUMN` のマイグレーションを書く
- ❌ `doctrine:migrations:diff` で Entity 差分から ALTER を自動生成する
- ❌ マイグレーションでテーブルを"新規定義"してスキーマの源泉にする → ✅ 源泉は Entity 属性。
- ❌ INSERT・構造変更でガードなし → 再実行や環境差で失敗。✅ 存在チェックで冪等にする。
- ❌ `down()` 未実装 → ロールバック不能。✅ `up()`/`down()` を対で実装。

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-migration/SKILL.md`

## 受注処理（PurchaseFlow）

**触るとき**: 受注処理・送料・在庫・ポイント

- ❌ 在庫引当・採番・ポイント付与・送料/値引き計算をコントローラや汎用 Service に直書き → ✅ 該当 Processor/Validator を拡張する
- ❌ 検証なのに ItemHolderPreprocessor、明細付与なのに Validator、と取り違える → ✅ パイプライン表で役割に合うコンポーネントを選ぶ
- ❌ `execute()` の override・try-catch・`ProcessResult` の `new` → ✅ `validate()` だけ override し `InvalidItemException` を投げる
- ❌ ItemValidator で購入を止めようとする → ✅ 常に warning。中断は `ItemHolderValidator` / `PostValidator` の error で
- ❌ Preprocessor で明細を追加しっぱなし（再実行で多重化） → ✅ `setProcessorName(self::class)` で印を付け、毎回削除→再追加で冪等にする
- ❌ 値引きで合計金額を超える明細を作る → ✅ 利用可能額まで丸めるかスキップし `ProcessResult::warn()` を返す
- ❌ 金額を float / `+`・`*` で計算 → ✅ `bcadd`/`bcsub`/`bcmul`/`bccomp` を使う
- ❌ `Cart` でも `getShippings()` / `getCustomer()` を呼ぶ → ✅ `instanceof Order` でガード（Cart には Shipping もポイントも無い）
- ❌ PurchaseProcessor の `rollback()` を実装し忘れる → ✅ `prepare()` の逆操作（在庫戻し等）を必ず実装する
- ❌ 属性で実行順を制御する／YAML タグと属性を両方付ける → ✅ 順序は YAML タグの `priority`（降順）。登録はどちらか一方（既定はコア=YAML / プラグイン=属性、順序が要るならプラグインも YAML）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-purchase-flow/SKILL.md`

## プラグイン

**触るとき**: プラグイン全体・PluginManager

- ❌ 雛形を手で一から作る → ✅ `bin/console eccube:plugin:generate <name> <code> <ver>` で骨組みを生成し、不要分を削る
- ❌ `composer.json` に `extra.code` が無い → ✅ 必須。無いと install で失敗
- ❌ PluginCode に `-` を使う → ✅ `^\w+$`（英数字・アンダースコアのみ）
- ❌ install しただけで動くと思う → ✅ install 直後は無効。`eccube:plugin:enable --code=...` で有効化
- ❌ エンティティトレイトに `#[EntityExtension(Target::class)]` を付け忘れ → ✅ 付けないとプロキシに乗らない
- ❌ トレイト追加後にプロキシ再生成を忘れる → ✅ `bin/console eccube:generate:proxies`
- ❌ プロジェクト固有の 1 回限りの改変をプラグイン化 → ✅ それは `app/Customize/`。着脱・再配布するものだけプラグイン
- ❌ `app/Customize`（`Eccube\` を直接拡張）と `app/Plugin`（`Plugin\{Code}\` 独立名前空間）の名前空間を混同 → ✅ 置き場所で名前空間を使い分ける

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-plugin/SKILL.md`

## カスタマイズ（app/Customize）

**触るとき**: `app/Customize` での拡張

- ❌ コア（`src/Eccube/`）を直接書き換える → ✅ `app/Customize/` で拡張・上書きし、アップグレード安全にする
- ❌ プロジェクト固有の 1 回限りの改変をプラグイン化 → ✅ それは `app/Customize/`。着脱・再配布するものだけ `app/Plugin/`
- ❌ 名前空間を `Plugin\{Code}\` と混同 → ✅ Customize は **`Customize\` ＝ `app/Customize/`**
- ❌ エンティティ拡張の trait に `#[EntityExtension(対象::class)]` を付け忘れ → ✅ 付けないと proxy に乗らずカラムが認識されない
- ❌ trait 追加後に proxy 再生成を忘れる → ✅ `bin/console eccube:generate:proxies`
- ❌ カラム追加に ALTER マイグレーションを書く → ✅ 属性が源泉。`schema:update --force` が反映（マイグレーションは INSERT・型変更等に限る。Skill `eccube-migration`）
- ❌ 既存フォームを直接改変 → ✅ `AbstractTypeExtension` ＋ `getExtendedTypes()`（`app/Customize/Form/Extension/`）で拡張
- ❌ サービスを `#[AsDecorator]` で包む（コアの作法と不一致） → ✅ `services.yaml` で `decorates` ＋ `@.inner` 委譲
- ❌ テンプレートを上書こうとしてコア原本側を編集 → ✅ `app/template/` に同じ相対パスで同名ファイルを置く（app 側が優先）
- ❌ 上書きパスのテーマ名を間違える → ✅ フロントは `app/template/{ECCUBE_TEMPLATE_CODE}/`（既定 `default`）、管理画面は `app/template/admin/`

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-customize/SKILL.md`

## メール

**触るとき**: メール送信・テンプレート

- ❌ コントローラで `MailerInterface` を直接呼んで `Email` を組み立てる → ✅ `MailService` の送信メソッドに集約する
- ❌ 差出人・返信先をハードコードする → ✅ `BaseInfo` の `email01`（From/Bcc）/ `email03`（ReplyTo）/ `email04`（ReturnPath）を使う
- ❌ 宛先に生の文字列を `->to($email)` で渡す → ✅ `convertRFCViolatingEmail($email)` を通す
- ❌ 件名・本文を PHP 内で文字列連結する → ✅ `MailTemplate` ＋ Twig（`render()`）で組み立てる
- ❌ プレーンテキストメール twig を素で書く / 通常の HTML エスケープをかける → ✅ `{% autoescape 'safe_textmail' %}` で囲む
- ❌ HTML メール用に送信メソッドへ分岐を足す → ✅ 同名 `*.html.twig` を置けば `getHtmlTemplate()` が自動で multipart 化する
- ❌ 送信失敗で例外を投げて受注処理を止める → ✅ `TransportExceptionInterface` を catch して `log_critical` で記録（既存の方針に合わせる）
- ❌ `MailService` 内で `flush()`／会員系メールを `MailHistory` に残す → ✅ persist まで。履歴の関連は `Order` と `Creator` のみ
- ❌ コアの `Resource/template/default/Mail/*.twig` を直接書き換える → ✅ `app/template/<コード>/Mail/` で上書きする
- ❌ 送信前のイベント dispatch を省く → ✅ プラグインの差し替え口として `EccubeEvents::MAIL_*` を必ず発火する

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-mail/SKILL.md`

## CSV 入出力

**触るとき**: CSV 入出力

- ❌ `fgetcsv` / `fputcsv` を直書きする → ✅ `CsvImportService` / `CsvExportService::fputcsv()` に乗る（数式の無害化も効く）
- ❌ 文字コード・区切り文字をハードコードする → ✅ `EccubeConfig` の `eccube_csv_export_*` / `eccube_csv_import_*` を使う
- ❌ エクスポートで全件を配列に貯めて一括出力する → ✅ `StreamedResponse` ＋ `exportData()` のページング（100 件ずつ `em->clear()`）で逐次出力
- ❌ 出力項目をコントローラに `if` で羅列する → ✅ `dtb_csv` 定義（`field_name` / `sort_no` / `enabled`）で表現し `getData()` に引かせる
- ❌ インポートで自前エンコーディング判定や全行読み込みをする → ✅ `CsvImportService`（stream filter 自動適用・Iterator）に任せ 1 行ずつ処理
- ❌ インポートをトランザクション無しで `flush()`／一時ファイルを残す → ✅ `beginTransaction`〜`commit`/`rollback` で囲み `removeUploadedFile()` する
- ❌ 出力項目追加のためにコアの export 処理を改変する → ✅ `ADMIN_*_CSV_EXPORT*` イベントを購読して `ExportCsvRow` に列追加
- ❌ `mtb_csv_type` へ `discriminator_type` なしで INSERT する → ✅ STI なので必ず指定する（Skill `eccube-migration`）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-csv/SKILL.md`

## コンソールコマンド

**触るとき**: コンソールコマンド・バッチ

- ❌ `execute()` に業務的な計算・判定・複数 Repository 横断処理を直書き → ✅ Service／Repository へ委譲し、コマンドは入出力と終了コードに徹する
- ❌ コンストラクタで `parent::__construct()` を呼び忘れる → ✅ コマンドでは必須（呼ばないと実行時エラー）
- ❌ サービス定義に手書きで `console.command` タグを足す → ✅ `#[AsCommand]` ＋ `autoconfigure` 任せ（手動登録不要）
- ❌ `execute()` の戻り値を書かない／`void` にする → ✅ `int` を返す（正常 `0`、異常は非 0）
- ❌ ループ内で毎回 `flush()` してバッチが遅い → ✅ バッチサイズごとにまとめて `flush()`、端数も最後に flush
- ❌ 「Symfony Scheduler で定期実行」と推測で書く → ✅ コアに機構は無い。OS の cron から `bin/console` を叩く前提で冪等に作る
- ❌ コマンド名を独自の命名で付ける → ✅ `eccube:` 接頭辞のコロン区切り（既存コマンドに倣う）

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-command/SKILL.md`

## PHPUnit テスト

**触るとき**: PHPUnit テスト

- ❌ `new Client()` など HTTP クライアントの自前生成 → ✅ 親クラスの `$this->client`。
- ❌ URL の文字列直書き（`'/products/list'`）→ ✅ `$this->generateUrl('product_list')`。
- ❌ Entity の手組み → ✅ `createXxx()` フィクスチャヘルパ。
- ❌ 支払方法のテストで `find(1)` 等の ID 前提 → ✅ `Generator::createPayment()` で sort_no・利用条件を明示し `assertSame()` で固定する。
- ❌ ステータス値のハードコーディング（`if ($status == 1)`）→ ✅ 定数（例: `OrderStatus::NEW`）を使う。
- ❌ 回帰テストがゲートになるか確かめない → ✅ 修正を外して落ちるか実測する。`failOnWarning` が無いので Warning では落ちず、戻り値を assert する。
- ❌ テストのプロパティを未宣言で代入／非 nullable で宣言 → ✅ nullable で宣言（`cleanUpProperties()` の null 代入で `TypeError` になる）
- ❌ HTML パートを持たないメールに `assertEmailHtmlBodyNotContains()` → ✅ `assertNull($Message->getHtmlBody())`。前者は必ず通る空振りになる。

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-phpunit/SKILL.md`

## E2E（Playwright）

**触るとき**: E2E（Playwright）

- ❌ `waitForTimeout(固定ms)` で同期を取る → ✅ web-first assertion で「状態」を待つ。外部 JS 由来の待機だけ例外とし、理由をコメントに書く。
- ❌ `front-*` spec で管理者ログイン済みを前提にする → ✅ front は未認証 state。spec 内でログインするか admin 経由で会員作成する。
- ❌ 固定件数で assert（`検索結果：1件が該当`）→ ✅ 正規表現（`/検索結果：\d+件が該当/`）。retry 時の重複データに強くする（`admin-product.spec.ts` 修正例）。
- ❌ セレクタが複数要素にマッチ（strict mode violation）→ ✅ `.first()` か `data-*` 属性で一意化する。
- ❌ テスト境界で管理者セッションが切れて 401 → ✅ `ensureAdminLoggedIn()` 等で再ログインしてから操作（`admin-basicinfo.spec.ts` 修正例）。
- ❌ retry でプラグイン/データが残留し再失敗 → ✅ `beforeEach`/`afterEach` で cleanup（無効化 → 削除 → ディレクトリ削除）。
- ❌ パスワードを見た目の文字数で作る → ✅ NFKC 正規化後で 15 文字以上か数える（min15。`[...str.normalize('NFKC')].length` で確認。#6488）。
- ❌ 新規 spec を作ったのに CI で実行されない → ✅ `e2e-test.yml` の `suite:` 配列にファイル名（`.spec.ts` 抜き）を追加する。

> 実装パターン・コード例・実行方法: `.claude/skills/eccube-e2e/SKILL.md`

---

## 典型パターン（複数の節にまたがるもの）

| やりたいこと | 読む節 |
|-------------|-------|
| 会員に項目を追加 | Entity → マイグレーション → FormType → コントローラ → Twig |
| プラグインに管理画面を追加 | プラグイン → コントローラ → セキュリティ → FormType → Twig |
| 商品検索を拡張 | Repository → コントローラ → FormType |
| 注文確定時に処理を追加 | 受注処理 → イベント |
| 管理画面 CSV 出力 | CSV 入出力 → コントローラ → セキュリティ |

## 実行・確認方法

設計相談から始まったセッションで、最初の tool call がこの SKILL.md の読み込みであることを確認する。
「まず既存実装を確認します」だけで終わっていれば、この Skill の手順が守られていない。
