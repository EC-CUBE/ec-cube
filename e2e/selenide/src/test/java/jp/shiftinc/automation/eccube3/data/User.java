package jp.shiftinc.automation.eccube3.data;

import br.com.six2six.fixturefactory.Fixture;
import br.com.six2six.fixturefactory.Rule;
import jp.shiftinc.automation.data.mail.MailConfiguration;
import lombok.Data;
import org.yaml.snakeyaml.Yaml;

import java.util.List;
import java.util.stream.Collectors;

/**
 * Created by tamagawa on 2016/12/12.
 */
@Data
public class User {

    private String lastName;
    private String firstName;
    private String lastNameKana;
    private String firstNameKana;
    private String zip1;
    private String zip2;
    private String prefecture;
    private String town;
    private String address;
    private String tel1;
    private String tel2;
    private String tel3;
    private String fax1;
    private String fax2;
    private String fax3;
    private String email1;
    private String email2;
    private String password;
    private String birthYear;
    private String birthMonth;
    private String birthDate;
    private String sex;
    private String job;
    private MailConfiguration mailAccount;

    // ------------------------------------------------
    // Fluentに使えるメソッド（※@Accessorアノテーションでも作れるが、YAMLからの読み込みと相性が悪くなるため手で書いている。
    // 実際にはどちらか一個を使えば良さそう）
    public User lastName(String lastName) {
        this.lastName = lastName;
        return this;
    }

    public User firstName(String firstName) {
        this.firstName = firstName;
        return this;
    }
    public User lastNameKana(String lastNameKana) {
        this.lastNameKana = lastNameKana;
        return this;
    }

    public User firstNameKana(String firstNameKana) {
        this.firstNameKana = firstNameKana;
        return this;
    }

    public User email2(String email2) {
        this.email2 = email2;
        return this;
    }

    public static User fromYaml(String path) {
        User user = (new Yaml()).loadAs(User.class.getResourceAsStream(path), User.class);
        return user.uniqueEmail();
    }

    @SuppressWarnings("unchecked")
    public static List<User> listFromYaml(String path) {
        List<User> userList = (new Yaml()).loadAs(User.class.getResourceAsStream(path), List.class);
        return userList.stream()
                .map(User::uniqueEmail)
                .collect(Collectors.toList());
    }

    /**
     * メールアドレス内に記載された特定の文字列を一意な文字列に置換します。
     * ※コピーではなく、元のオブジェクトを書き換えます。
     *
     * @return メールアドレス置換済みのUserオブジェクト
     */
    private User uniqueEmail() {
        String uuid = String.valueOf(System.nanoTime());
        email1 = email1.replaceAll("%UUID%", uuid);
        email2 = email2.replaceAll("%UUID%", uuid);
        return this;
    }

    // ------------------------------------------------
    // 全部Yamlに書くのが面倒な場合

    static {
        Fixture.of(User.class).addTemplate("valid", new Rule() {
            {
                // うまくランダムに出来なかったものは、とりあえずデフォルト値（全部デフォルト値でも良さそう）
                add("lastName", regex("[あ-ん]{2}"));
                add("firstName", regex("[あ-ん]{2}"));
                add("lastNameKana", regex("[ア-ン]{2}"));
                add("firstNameKana", regex("[ア-ン]{2}"));
                add("zip1", regex("\\d{3}"));
                add("zip2", regex("\\d{4}"));
                add("prefecture", "東京都");
                add("town", "港区麻布台");
                add("address", "2-4-5");
                add("tel1", regex("\\d{2}"));
                add("tel2", regex("\\d{4}"));
                add("tel3", regex("\\d{4}"));
                add("fax1", regex("\\d{2}"));
                add("fax2", regex("\\d{4}"));
                add("fax3", regex("\\d{4}"));
                add("email1", "dummy+%UUID%@example.com");
                add("email2", "dummy+%UUID%@example.com");
                add("password", regex("\\w{8}"));
                add("birthYear", "2000");
                add("birthMonth", "01");
                add("birthDate", "31");
                add("sex", regex("男性"));
                add("job", "コンピューター関連技術職");
            }
        });
    }

    public static User getFixture() {
        User user = Fixture.from(User.class).gimme("valid");
        return user.uniqueEmail();
    }
}
