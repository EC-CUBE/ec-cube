package jp.shiftinc.automation.eccube3.data;

import br.com.six2six.fixturefactory.Fixture;
import br.com.six2six.fixturefactory.Rule;
import lombok.Data;
import org.apache.commons.lang.RandomStringUtils;
import org.yaml.snakeyaml.Yaml;

import java.util.Random;
import java.util.UUID;

/**
 * Created by kenichiro_ota on 2015/12/15.
 */
@Data
public class Product {
    private String productName;
    private int productType;
    private String productDescriptionDetail;
    private int productStock;
    private int productCategory;
    private int sellingPrice;
    private String productCode;
    private int productStatus;

    static {
         Fixture.of(Product.class).addTemplate("valid",  new Rule() { {
             add("productName", regex("\\w{32}"));
             add("productType", random(Integer.class, range(1, 2)));
             add("productDescriptionDetail", regex("\\w{32}"));
             add("productStock", random(Integer.class, range(1, 999)));
             add("productCategory", random(Integer.class, range(1, 6)));
             add("sellingPrice", random(Integer.class, range(1, 9999)));
             add("productCode", regex("\\w{8}"));
             add("productStatus", 1);
        }}
        );
    }
    public static Product getFixture() {
       return Fixture.from(Product.class).gimme("valid");
    }

    /**
     * 指定されたパスにあるYAMLファイルから商品のデータを読み取ります。
     * テストの繰り返し実行に対応できるよう、商品コード・商品名・価格はYAMLで指定がない限りランダムな値を設定します。
     *
     * @param path  YAMLファイルのパス
     * @return  生成された商品のデータ
     */
    public static Product fromYaml(String path) {
        Product product = (Product) new Yaml().load(Product.class.getResourceAsStream(path));
        // 入っていないプロパティがあれば設定
        if (product.getProductName() == null) {
            product.setProductName(RandomStringUtils.randomAlphabetic(32));
        }
        if (product.getProductCode() == null) {
            product.setProductCode(RandomStringUtils.randomAlphabetic(8));
        }
        if (product.getSellingPrice() == 0) {
            product.setSellingPrice(new Random(System.currentTimeMillis()).nextInt(10000));
        }

        return product;
    }

    public Product descriptionDetail(String value) {
        setProductDescriptionDetail(value);
        return this;
    }
}
