var control
if (!control) control = Java.type("org.parosproxy.paros.control.Control").getSingleton()

var Alert = Java.type("org.parosproxy.paros.core.scanner.Alert");
var Stats = Java.type("org.zaproxy.zap.utils.Stats");

// Path Traversal (6) の「URL 末尾の文字列をファイル名として送る」チェックは、比較用に送る
// 存在しないファイル名 (38 文字) が入力欄の文字数上限でバリデーションエラーになるだけで High を出す。
// このチェックは根拠 (evidence) を持たず、/etc/passwd 等を読み取れた本来の検知は根拠を持つため、
// 根拠の無いものは判定から外す (レポートには残る)
var PATH_TRAVERSAL = 6;
function isFilenameOnlyPathTraversal(a) {
    return a.pluginId == PATH_TRAVERSAL && (a.evidence == null || String(a.evidence).trim() === '');
}

var extAlert = control.getExtensionLoader().getExtension(org.zaproxy.zap.extension.alert.ExtensionAlert.NAME);
var highAlerts = extAlert.getAllAlerts().stream().filter(function(a) {
    return a.risk >= Alert.RISK_HIGH && a.confidence != Alert.CONFIDENCE_FALSE_POSITIVE;
}).collect(java.util.stream.Collectors.toList());

var excluded = highAlerts.stream().filter(isFilenameOnlyPathTraversal).count();
if (excluded > 0) {
    print("Excluded " + excluded + " Path Traversal alert(s) without evidence from the high risk count");
}

highAlerts.stream().filter(function(a) {
    return !isFilenameOnlyPathTraversal(a);
}).forEach(function(a) {
    print("globalalertfilter.filters.filter\\(\\).ruleid=" + a.pluginId);
    print("globalalertfilter.filters.filter\\(\\).newrisk=-1");
    print("globalalertfilter.filters.filter\\(\\).url=" + a.uri);
    print("globalalertfilter.filters.filter\\(\\).urlregex=false");
    print("globalalertfilter.filters.filter\\(\\).param=" + a.param);
    print("globalalertfilter.filters.filter\\(\\).paramregex=false");
    print("globalalertfilter.filters.filter\\(\\).attack=" + a.attack);
    print("globalalertfilter.filters.filter\\(\\).attackregex=false");
    print("globalalertfilter.filters.filter\\(\\).evidence=" + a.evidence);
    print("globalalertfilter.filters.filter\\(\\).evidenceregex=false");
    print("globalalertfilter.filters.filter\\(\\).enabled=true");
    Stats.incCounter("stats.scan.high.alerts");
});
