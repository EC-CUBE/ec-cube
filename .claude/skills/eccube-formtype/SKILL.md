---
name: eccube-formtype
description: EC-CUBE 4.4 のフォーム（FormType）を実装・改修するときの規約。「フォームを作って」「FormTypeを追加して」「入力項目を足して」「バリデーションを設定して」「検索フォームを作って」「既存フォームに項目を追加して」などと言われたとき、または src/Eccube/Form・app/Customize/Form 配下を作成・編集するときに使用する。

---

# FormType 規約（EC-CUBE 4.4）

**対象**: `src/Eccube/Form/Type/**/*.php`, `app/Customize/Form/**/*.php`
**前提**: Symfony 7.4 Form / PHP 8.2+

## 基本ルール

- 名前空間 `Eccube\Form\Type`。`Symfony\Component\Form\AbstractType` を継承する。
- PHP ファイル先頭に EC-CUBE ライセンスヘッダ。型宣言を付ける（PHPStan level 6）。
- 依存はコンストラクタインジェクション（`EccubeConfig` 等）。

```php
namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ExampleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 255),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Example::class,
        ]);
    }

    public function getBlockPrefix(): string   // 戻り値型宣言は必須（Symfony 6+）
    {
        return 'example';
    }
}
```

## ポイント

- **`getBlockPrefix(): string` の戻り値型宣言は必須**（省略すると動かない）。
- フォームの動的制御は `$builder->addEventListener(FormEvents::PRE_SET_DATA, ...)` /
  `FormEvents::POST_SUBMIT` で行う。
- バリデーションは制約クラス（`Assert\NotBlank` 等）で付ける。`Range` で min/max 両方指定時は
  `notInRangeMessage` を使う。
- エンティティに紐づくフォームは `configureOptions()` で `data_class` を設定する。
- 既存の EC-CUBE 提供 Type（`AddCartType`・`AddressType`・`PriceType` 等）があれば再利用する。
- パスワード入力を扱う場合、対象エンティティに `plain_password` プロパティが必要。

## CSRF

- **管理画面の検索フォーム等で CSRF を無効化しない**。`configureOptions()` で
  `'csrf_protection' => true`（既定）を保ち、テンプレートでトークンを出力する。

## カスタマイズ（app/Customize）

- 既存フォームへのフィールド追加は **コアを書き換えず `FormTypeExtension`** で行い、
  `app/Customize/Form/Extension/` に置く（`getExtendedTypes()` で対象 Type を指定）。

## よくある間違い

- ❌ `getBlockPrefix()` の戻り値型を省略 → ✅ `: string` を付ける
- ❌ 既存フォームをコアで直接改変 → ✅ `FormTypeExtension`（app/Customize）で拡張
- ❌ 管理画面検索フォームで CSRF 無効化 → ✅ CSRF 保護を保つ
- ❌ 具象クラス依存 → ✅ コンストラクタ DI ＋ 必要なサービスの注入
- ❌ 既存フォームに二重送信防止/楽観ロック用の unmapped hidden を足し、サーバー側で値未送信を即エラー扱い → ✅ 値が空/未送信なら判定をスキップ（プログラム的 POST・既存テスト・外部連携を壊さない後方互換を保つ）
- ❌ 共通 FormType(RepeatedPasswordType 等)を子で使い `options.constraints` を渡す（親が定義した制約が全置換され消える） → ✅ 親の制約一式も再掲して付与する

## 実行・確認方法

```bash
# FormType の構成・オプション・拡張が効いているかを確認する（FQCN でも短縮名でもよい）
bin/console debug:form BlockType
bin/console cache:clear

# フォームのテストだけを回す
vendor/bin/phpunit tests/Eccube/Tests/Form/Type/Admin/BlockTypeTest.php
```

- 実装後の整形・型・静的解析・テストは **AGENTS.md「開発コマンド」** に従って実行する
  （PHP-CS-Fixer / PHPStan level 6 / PHPUnit）。
- `FormTypeExtension` で拡張した場合は、`debug:form` の出力に追加項目が現れることで登録を確認できる。

---

実装・改修後は、Skill `eccube-review-responsibility` で責務分離を点検すること。
