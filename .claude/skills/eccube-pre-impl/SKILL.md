---
name: eccube-pre-impl
description: 新規機能の設計・実装を始める直前のチェックリスト。「〜を作りたい」「〜機能を追加したい」「実装する前に確認して」「設計から相談したい」「どう作ればいいか相談したい」「Issue に対応して」「#NNNN を対応して」「この Issue をやって」などと言われたとき、既存コードの調査より先に該当レイヤの規約 Skill を読むよう誘導する。設計フェーズと実装フェーズの橋渡し用。
---

# 実装前チェックリスト（EC-CUBE 4.4）

**対象**: 新規機能・カスタマイズ・プラグイン開発の設計〜実装着手前
**前提**: レイヤ別規約は `.claude/skills/eccube-*/SKILL.md` にある

> 目的: 設計相談から入る依頼でも、**既存コードの調査より先に** 該当レイヤの規約を読み、
> 制約・罠・実装パターンを把握してから設計・実装に進む。

## 基本ルール

1. **この Skill を読んだら、下表から該当 Skill を特定し、すぐ Read する**（調査・grep より先）
2. 複数レイヤにまたがる場合は **該当 Skill をすべて** 読む
3. コア・`app/Customize`・プラグインのいずれでも同じ Skill が適用される
4. 設計案を出す前に、読んだ Skill の制約を設計に反映する

## レイヤ別 Skill 対応表

| 触るもの・依頼の例 | 読む Skill |
|-------------------|-----------|
| 画面・ルーティング・アクション | `eccube-controller` |
| 業務ロジック・Service 切り出し | `eccube-service` |
| エンティティ・テーブル・カラム追加 | `eccube-entity` |
| カラム追加の要否判断・型変更・マスタ投入 | `eccube-migration`（entity とセット） |
| 検索・一覧・クエリ | `eccube-repository` |
| 入力フォーム・バリデーション | `eccube-formtype` |
| テンプレート・Twig 拡張 | `eccube-twig-template` |
| イベント・フック・差し込み | `eccube-event-subscriber` |
| 認可・CSRF・セキュリティ点検 | `eccube-security` |
| プラグイン全体・PluginManager | `eccube-plugin` |
| `app/Customize` での拡張 | `eccube-customize`（＋触るレイヤの Skill） |
| 受注処理・送料・在庫・ポイント | `eccube-purchase-flow` |
| メール送信・テンプレート | `eccube-mail` |
| CSV 入出力 | `eccube-csv` |
| コンソールコマンド・バッチ | `eccube-command` |
| PHPUnit テスト | `eccube-phpunit` |
| E2E（Playwright） | `eccube-e2e` |

## 典型パターン（複数 Skill を読む）

| やりたいこと | 読む Skill（順不同） |
|-------------|-------------------|
| 会員に項目を追加 | `eccube-entity` → `eccube-migration` → `eccube-formtype` → `eccube-controller` → `eccube-twig-template` |
| プラグインに管理画面を追加 | `eccube-plugin` → `eccube-controller` → `eccube-formtype` → `eccube-twig-template` |
| 商品検索を拡張 | `eccube-repository` → `eccube-controller` → `eccube-formtype` |
| 注文確定時に処理を追加 | `eccube-purchase-flow` → `eccube-event-subscriber` |
| 管理画面 CSV 出力 | `eccube-csv` → `eccube-controller` |

## 実装着手前の確認（読んだあと）

- [ ] 触るレイヤの Skill を Read した（Skill tool または Read tool のログが残っている）
- [ ] 設計案に Skill の制約（責務分離・認可・マイグレーション要否など）を反映した
- [ ] セキュリティに触れるなら `eccube-security` も読んだ
- [ ] 実装完了後は `eccube-review-responsibility` で自己レビューする

## よくある間違い

- 既存コードを grep してから Skill を読む — 設計相談型の依頼では Skill が自動発火せず、規約が届かない
- entity だけ読んで migration を読まない — カラム追加が schema:update で足りるか判断を誤る
- プラグイン開発で `eccube-plugin` を読まない — 配置・ライフサイクルの罠を踏む

## 実行・確認方法

設計相談から始まったセッションで、最初の tool call が Skill/Read（該当 SKILL.md）であることを確認する。
「まず既存実装を確認します」だけで終わっていれば、この Skill の手順が守られていない。
