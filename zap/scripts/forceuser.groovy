import org.zaproxy.addon.automation.ContextWrapper

def forceUser = org.parosproxy.paros.control.Control.getSingleton()
	.getExtensionLoader()
	.getExtension(org.zaproxy.zap.extension.forceduser.ExtensionForcedUser.class);

def admin = org.parosproxy.paros.model.Model.getSingleton()
	.getSession()
	.getContexts().collect { new ContextWrapper(it).getUser('admin') }
	.find()

if (admin != null) {
	forceUser.setForcedUser(admin.getContextId(), admin)
	forceUser.setForcedUserModeEnabled(true)
	println('Force user mode enabled: admin')
}
