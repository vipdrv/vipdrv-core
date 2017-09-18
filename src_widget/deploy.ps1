$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"

$msdeployArguments = '-verb:sync',
		'-source:contentPath="C:\Data\Sources\Quantum\src_widget\build"',
		'-dest:contentPath="dev-widget-quantumlogic",wmsvc=dev-widget-quantumlogic.scm.azurewebsites.net:443/msdeploy.axd?site=dev-widget-quantumlogic,userName=$dev-widget-quantumlogic,password=uJ6AbPS13eml3mJQDvT1uJn4oMJL2sAfmvPzLFJdlHqmNKuntCzKGEpECz3b',
		'-skip:Directory="node_modules"',
		'-AllowUntrusted'
		
& $msdeploy $msdeployArguments
