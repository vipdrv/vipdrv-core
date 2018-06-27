# Build #'


# Deploy #
$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"

$sourceContentPath = "E:\Temp\publish"

$siteName = "prod-api-testdrive"
$password = "L1N8DwkFi25Bmogw1gMzcHmwsTEavZrGX7jNa11w8plCN5bor6PjtNCPALlJ"
$userName = "$" + "$siteName"
$wmsvc = "$siteName" + ".scm.azurewebsites.net:443/msdeploy.axd?site=" + "$siteName"

$msdeployArguments = '-verb:sync',
		"-source:contentPath=$sourceContentPath",
		"-dest:contentPath=$siteName,wmsvc=$wmsvc,userName=$userName,password=$password",
		'-AllowUntrusted'
		
& $msdeploy $msdeployArguments

