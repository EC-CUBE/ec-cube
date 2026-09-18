import org.parosproxy.paros.control.Control
import org.parosproxy.paros.model.Model
import org.zaproxy.zap.extension.users.ExtensionUserManagement
import org.zaproxy.zap.extension.forceduser.ExtensionForcedUser

// 1. ユーザー管理と強制ユーザーの拡張機能を取得
def extUserMgmt = Control.getSingleton().getExtensionLoader().getExtension(ExtensionUserManagement.class)
def forceUser = Control.getSingleton().getExtensionLoader().getExtension(ExtensionForcedUser.class)

// 2. すべてのコンテキストから 'admin' ユーザーを探す
def admin = null
Model.getSingleton().getSession().getContexts().each { context ->
    def users = extUserMgmt.getContextUserAuthManager(context.getIndex()).getUsers()
    def foundUser = users.find { it.getName() == 'admin' }
    if (foundUser != null) {
        admin = foundUser
    }
}

// 3. 'admin' が見つかったら Force User モードを有効化
if (admin != null) {
    forceUser.setForcedUser(admin.getContextId(), admin)
    forceUser.setForcedUserModeEnabled(true)
    println('Force user mode enabled: admin')
} else {
    println('Warning: User "admin" not found in any context.')
}