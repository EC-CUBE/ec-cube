var control
if (!control) control = Java.type("org.parosproxy.paros.control.Control").getSingleton()

var Alert = Java.type("org.parosproxy.paros.core.scanner.Alert");
var Stats = Java.type("org.zaproxy.zap.utils.Stats");

var extAlert = control.getExtensionLoader().getExtension(org.zaproxy.zap.extension.alert.ExtensionAlert.NAME);
var highRisks = extAlert.getAllAlerts().stream().filter(function(a) {
    return a.risk >= Alert.RISK_HIGH && a.confidence != Alert.CONFIDENCE_FALSE_POSITIVE;
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
