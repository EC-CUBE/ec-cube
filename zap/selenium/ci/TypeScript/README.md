# Automated security tests with OWASP ZAP

OWASP ZAP のアクティブスキャンを自動化するプログラムです。
OWASP ZAP を使用したペネトレーションテストを自動化する手段として、 [OWASP ZAP Full Scan
](https://github.com/marketplace/actions/owasp-zap-full-scan) がありますが、EC-CUBE の場合は日本語入力が必須であったり、[特殊な遷移パターン](https://doc4.ec-cube.net/penetration-testing/testing/attention#%E7%89%B9%E6%AE%8A%E3%81%AA%E9%81%B7%E7%A7%BB%E3%83%91%E3%82%BF%E3%83%BC%E3%83%B3)があるため、十分にスキャンできません。
この対策として、Playwright と連携させて自動化します。

## スキャンの流れ

以下のような流れでスキャンを実行します。

1. OWASP ZAP の API を使用して、コンテキストや自動ログインを設定する
2. Playwright で OWASP ZAP の Proxy を通してクロールする
3. クロールしたページに対してアクティブスキャンを実行する
4. OWASP ZAP のセッションを保存する

High 以上のアラートが出た場合はテストが失敗します。
アラートが誤検知の場合は alertfilter に追加して、アラートを抑制する必要があります。

### ローカル環境での実行方法

```shell
# COMPOSE_FILE 環境変数を使用して docker-compose コマンドでロードする Yaml ファイルを設定します
export COMPOSE_FILE=docker-compose.yml:docker-compose.pgsql.yml:docker-compose.dev.yml:docker-compose.owaspzap.yml:docker-compose.owaspzap.daemon.yml

## docker-compose を使用して EC-CUBE をインストールします
cd path/to/ec-cube
docker-compose up -d
docker-compose exec -T ec-cube bin/console doctrine:schema:create --env=dev
docker-compose exec -T ec-cube bin/console eccube:fixtures:load --env=dev

## テスト用のダミーデータを生成します
docker-compose exec -T ec-cube bin/console eccube:fixtures:generate --products=5 --customers=1 --orders=5
docker-compose exec -T ec-cube bin/console doctrine:query:sql "UPDATE dtb_customer SET email = 'zap_user@example.com' WHERE id = 1;"

## 環境変数 APP_ENV=prod に設定します
sed -i.bak 's!APP_ENV: "dev"!APP_ENV: "prod"!g' docker-compose.yml

## ec-cube コンテナを再起動し、設定を反映します。
docker-compose up -d ec-cube

## yarn でテストを実行します。
cd zap/selenium/ci/TypeScript
yarn install && yarn playwright install # (初回のみ)
yarn playwright test

## (Optional) 個別にテストする場合は、テストのファイル名を指定してください。
yarn playwright test test/front_guest/contact.test.ts
```

####  実行中に OWASP ZAP を操作したい場合

実行中に OWASP ZAP を操作したい場合は [OWASP ZAP API](https://www.zaproxy.org/docs/api/) を直接コールします。
[QuickStart の手順4](https://doc4.ec-cube.net/penetration-testing/quick_start) にある、プロキシーの設定をした後に、 Firefox で http://zap/UI へアクセスするとブラウザから API をコールできます。
また、後述する OWASP ZAP を GUI モードで起動することで、 API を使用しなくても GUI で操作が可能です。

#### スキャンの結果を確認する

*事前に [スタンドアローン版の OWASP ZAP](https://www.zaproxy.org/download/) をダウンロードし、インストールしておきます。*

スキャン結果は `path/to/ec-cube/zap/sessions` に保存されます。
スタンドアローン版の OWASP ZAP を起動し、 **ファイル→セッションデータファイルを開く** より、この中のセッションデータを開くことでスキャン結果を確認できます。
セッションデータはテストごとに初期化されるため、確認する場合は個別にテストを実行してください。

#### GUI でのテストの確認

##### Chrome を GUI モードで起動する

以下のように playwright に `--headed` オプションを付与することで Chrome が実際に起動し、実行状況を確認できます。

``` shell
yarn playwright test test/front_guest/contact.test.ts --headed
```

また、 [`page.pause()`](https://playwright.dev/docs/api/class-page#page-pause)をテストコードに埋め込めばステップ実行も可能です。

##### OWASP ZAP を GUI モードで起動する

上記 `COMPOSE_FILE` 環境変数の `docker-compose.owaspzap.daemon.yml` を `docker-compose.owaspzap.yml` とすることで、 OWASP ZAP を GUI モードで起動できます。
OWASP ZAP の Docker コンテナ起動後、Firefox 以外のブラウザで http://localhost:8081/zap/ へアクセスすると、OWASP ZAP の管理画面が表示されます。
**ツール→オプション→API** を開き、 Addresses permitted to use the API の**追加**をクリック、以下の内容を登録することで、yarn のテストが実行できます。

- **Address:** `.*`
- **Regex:** ON
- **有効:** ON

## GitHub Actions のワークフロー

このテストは GitHub Actions で毎週1回実行します。
GitHub Actions のワークフローが完了すると、 OWASP ZAP のセッションデータがアップロードされ、GitHub Actions の Artifacts からダウンロードできます。
これをローカル環境の OWASP ZAP で開くことで、アラートの内容などを確認することができます。

## 参考

- EC-CUBE開発者向けドキュメントの [EC-CUBE Penetration Testing with OWASP ZAP](https://doc4.ec-cube.net/penetration-testing) も併わせてお読みください
- [脆弱性対応勉強会Expansion 第05回(OWASP ZAP&EC-CUBE)発表資料](https://zenn.dev/nanasess/articles/vulnstudy05-owasp-and-eccube)
- [Driving OWASP ZAP with Selenium](https://owasp.org/www-chapter-london/assets/slides/OWASPLondon-OWASP-ZAP-Selenium-20180830-PDF.pdf)
