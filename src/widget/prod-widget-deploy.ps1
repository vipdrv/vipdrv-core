# Build #
Set-Location C:\Data\Sources\TestDrive\src_widget\
npm install
gulp --gulpfile "C:\Data\Sources\TestDrive\src_widget\gulpfile.js" build_dist:prod

# Deploy #
$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"

$sourceContentPath = "C:\Data\Sources\TestDrive\src_widget\build"

$siteName = "prod-widget-testdrive"
$password = "NYAid4pFqSP88DqFRZkBXlmSuyE5xNKksMlnc3T7ZwTGuHdtQ4dbvvdnLJeH"
$userName = "$" + "$siteName"
$wmsvc = "$siteName" + ".scm.azurewebsites.net:443/msdeploy.axd?site=" + "$siteName"

$msdeployArguments = '-verb:sync',
		"-source:contentPath=$sourceContentPath",
		"-dest:contentPath=$siteName,wmsvc=$wmsvc,userName=$userName,password=$password",
		'-skip:Directory="node_modules"',
		'-AllowUntrusted'
		
& $msdeploy $msdeployArguments

