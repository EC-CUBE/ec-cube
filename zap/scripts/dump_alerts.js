var control
if (!control) control = Java.type("org.parosproxy.paros.control.Control").getSingleton()

var Alert = Java.type("org.parosproxy.paros.core.scanner.Alert");
var Stats = Java.type("org.zaproxy.zap.utils.Stats");
var Collectors = Java.type("java.util.stream.Collectors");

var extAscan = control.getExtensionLoader().getExtension(org.zaproxy.zap.extension.ascan.ExtensionActiveScan.NAME);
var ascanIds = extAscan.getPolicyManager().getDefaultScanPolicy().getPluginFactory().getAllPlugin().stream()
    .map(function(p) { return p.id })
    .collect(Collectors.toList());

var extPscan = control.getExtensionLoader().getExtension(org.zaproxy.zap.extension.pscan.ExtensionPassiveScan.NAME);
var pscanIds = extPscan.getPluginPassiveScanners().stream()
    .map(function(p) { return p.pluginId })
    .collect(Collectors.toList());

var extAlert = control.getExtensionLoader().getExtension(org.zaproxy.zap.extension.alert.ExtensionAlert.NAME);
var alerts = extAlert.getAllAlerts().stream().map(function(a) {
    return {
        "ruleid": a.pluginId,
        "url": a.uri,
        "param": a.param,
        "attack": a.attack,
        "evidence": a.evidence,
        "risk": a.risk,
        "method": a.method,
        "confidence": a.confidence,
        "type": ascanIds.contains(a.pluginId) ? 'Active' : (pscanIds.contains(a.pluginId) ? 'Passive' : null)
    }
}).collect(Collectors.toList());

var json = Java.type("net.sf.json.JSONArray").fromObject(alerts).toString()
java.nio.file.Files.write(
    java.nio.file.Paths.get('/tmp/alerts.json'),
    java.util.Arrays.asList(json),
    java.nio.charset.Charset.forName('UTF-8')
)