---
name: formtype
description: EC-CUBE 4.4 のフォーム（FormType）を実装・改修するときの規約。「フォームを作って」「FormTypeを追加して」「入力項目を足して」「バリデーションを設定して」「検索フォームを作って」「既存フォームに項目を追加して」などと言われたとき、または src/Eccube/Form・app/Customize/Form 配下を作成・編集するときに使用する。
---

# EC-CUBE 4.4 FormType 実装規約

FormType を実装・改修する際は、必ず次のドキュメントを読み込み、その規約に従うこと。

**規約本文: [`docs/rules/formtype.md`](../../../docs/rules/formtype.md)**

要点（詳細は本文参照）:
- `Symfony\Component\Form\AbstractType` を継承。`buildForm()` / `configureOptions()` / `getBlockPrefix()` を実装。
- **`getBlockPrefix(): string` の戻り値型宣言は必須**。
- 動的制御は `addEventListener(FormEvents::PRE_SET_DATA / POST_SUBMIT, ...)`。バリデーションは `Assert\*` 制約。
- エンティティ連動フォームは `configureOptions()` で `data_class` を設定。既存 Type は再利用。
- **管理画面検索フォームで CSRF を無効化しない**。
- 既存フォームへの項目追加は **`FormTypeExtension`**（`app/Customize/Form/Extension/`、`getExtendedTypes()`）で行う。
- ライセンスヘッダ・型宣言必須。
