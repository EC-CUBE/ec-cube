
    <h1>ご注文誠にありがとうございました。</h1>
    <p>お客様の取引IDは <!--{$order_id}--> です。</p>
    <p>振込票番号は <!--{$receipt_no}--> です。</p>
    <p>統合決済画面（PC用）は
       <a href="<!--{$haraikomi_url}-->"><!--{$haraikomi_url}--></a> です。</p>
    <p>統合決済画面（携帯用）は
       <a href="<!--{$mobile_url}-->"><!--{$mobile_url}--></a> です。</p>
    <p>支払金額は <!--{$amount}--> 円です。</p>
    <p>支払期限は <!--{$pay_limit}--> です。</p>
    <p>統合決済画面を印刷、もしくはケータイ決済番号を紙などに控えて全国のサークルK、サンクスにてお支払ください。</p>
    <p>ケータイ決済番号のみを紙などでお持ちの場合は、お客様から店員に提示の際にケータイ決済である旨、</p>
    <p>ケータイ決済番号を押す旨お伝え下さい。</p>
    
    以下のパラメータは用途によって使ってください
    <p>txn-version  :  <!--{$txn_version}--></p>
    <p>merch-txn    :  <!--{$merch_txn}--></p>
    <p>order-ctl-id :  <!--[$order_ctl_id}--></p>
    <p>MStatus      :  <!--{$MStatus}--></p>
    <p>MErrMsg      :  <!--{$MErrMsg}--></p>
    <p>aux-msg      :  <!--{$aux_msg}--></p>
    <p>receipt-no   :  <!--{$receipt_no}--></p>
    <p>action-code  :  <!--{$action_code}--></p>
    <p>ref-code     :  <!--{$ref_code}--></p>
    <p>MErrLoc      :  <!--{$MErrLoc}--></p>
    <p>err-code     :  <!--{$err_code}--></p>
    <p>cust-txn     :  <!--{$cust_txn}--></p>
    <p>order-id     :  <!--{$order_id}--></p>
    <p>amount       :  <!--{$amount}--></p>
    <p>pay-limit    :  <!--{$pay_limit}--></p>