Tech = Java.type("org.zaproxy.zap.model.Tech");

// https://github.com/zaproxy/zaproxy/blob/main/zap/src/main/java/org/zaproxy/zap/model/Tech.java
excludes = [
    // Tech.Db,
    Tech.MySQL,
    // Tech.PostgreSQL,
    Tech.MsSQL,
    Tech.Oracle,
    Tech.SQLite,
    Tech.Access,
    Tech.Firebird,
    Tech.MaxDB,
    Tech.Sybase,
    Tech.Db2,
    Tech.HypersonicSQL,
    Tech.MongoDB,
    Tech.CouchDB,
    // Tech.Lang,
    Tech.ASP,
    Tech.C,
    Tech.JAVA,
    Tech.SPRING,
    // Tech.JAVASCRIPT,
    Tech.JSP_SERVLET,
    // Tech.PHP,
    Tech.PYTHON,
    Tech.RUBY,
    // Tech.XML,
    // Tech.OS,
    // Tech.Linux,
    Tech.MacOS,
    Tech.Windows,
    // Tech.SCM,
    // Tech.Git,
    Tech.SVN,
    // Tech.WS,
    // Tech.Apache,
    Tech.IIS,
    Tech.Tomcat,
];

session = Java.type("org.parosproxy.paros.model.Model").getSingleton().getSession();
session.getContexts().stream().forEach(function(ctx) {
    excludes.forEach(function(tech) { ctx.getTechSet().exclude(tech) });
});

session.getContexts().stream().forEach(function(ctx) {
    ctx.getTechSet().getIncludeTech().stream().forEach(function(t) { print(t.getName()) });
});
