package jp.shiftinc.automation.eccube3.data;

import br.com.six2six.fixturefactory.Fixture;
import br.com.six2six.fixturefactory.Rule;
import lombok.Data;

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
}
