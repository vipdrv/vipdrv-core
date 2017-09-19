$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"

$msdeployAdminArguments = '-verb:sync',
		'-source:contentPath="C:\Data\Sources\Quantum\src_admin\dist"','-dest:contentPath="dev-admin-quantumlogic",wmsvc=dev-admin-quantumlogic.scm.azurewebsites.net:443/msdeploy.axd?site=dev-admin-quantumlogic,userName=$dev-admin-quantumlogic,password=yXg9MoxkHXKEC1sHqYnXPrxfhbsahGWWkeYzgaTnoXcSLrY7PkRrfaEwG57F',
		'-skip:Directory="node_modules"',
		'-AllowUntrusted'

$msdeployWebConfigArguments = '-verb:sync',
		'-source:contentPath="C:\Data\Sources\Quantum\src_admin\web.config"','-dest:contentPath="dev-admin-quantumlogic\web.config",wmsvc=dev-admin-quantumlogic.scm.azurewebsites.net:443/msdeploy.axd?site=dev-admin-quantumlogic,userName=$dev-admin-quantumlogic,password=yXg9MoxkHXKEC1sHqYnXPrxfhbsahGWWkeYzgaTnoXcSLrY7PkRrfaEwG57F',
		'-AllowUntrusted'
		
& $msdeploy $msdeployAdminArguments
& $msdeploy $msdeployWebConfigArguments
