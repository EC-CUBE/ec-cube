# 機能の一覧

利用者（購入者・店舗運営者・開発者）から見た EC-CUBE の機能を、機能 ID で一覧したもの（Issue #7225）。
結合試験項目書（EC-CUBE/eccube-specification）の観点表は、この機能 ID を参照する。

- 📖 一覧（人間向け）: [README.html](./README.html)
- 🗂 正本: [features.yaml](./features.yaml)（`README.html` は生成物なので直接編集しない）

## 更新のしかた

機能を追加・変更する PR では、同じ PR で `features.yaml` を更新し、`README.html` を作り直す。

```bash
php .github/bin/feature-list.php           # README.html を生成
php .github/bin/feature-list.php --check   # 検証のみ（CI の docs-check と同じ）
```

- 機能 ID は `接頭辞-グループ番号 2 桁-連番 2 桁`（例: `AD-20-01`）。グループは `features.yaml` 先頭の `groups` に定義する（`FR` フロント / `AD` 管理画面 / `DV` 開発者・運用者）。10 刻みで振ってあり、間に足すときは `AD-25` のような間の番号を使う
- 新しい機能は、そのグループの最大の番号 +1 で行を足し、`since` と `prs` を書く
- 既存の機能を変えたら、その行の `prs` に、リリースするバージョンをキーにして PR 番号を足す
- 機能を廃止しても行は消さず `removed` を付ける。機能 ID は再利用しない
- 不具合修正だけの PR は `prs` に書かない
