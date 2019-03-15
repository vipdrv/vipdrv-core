#=======================================================================#
# Replace                                                               #
#=======================================================================#

$appSettings = "C:\Data\Sources\VipDrive_API\src_api\QuantumLogic\QuantumLogic\appsettings.json";
(Get-Content $appSettings).replace("-build.counter-", "%build.counter%") | Set-Content $appSettings

$startupSettings = "C:\Data\Sources\VipDrive_API\src_api\QuantumLogic\QuantumLogic\Startup.cs";
(Get-Content $startupSettings).replace("appsettings.Development.json", "appsettings.Production.json") | Set-Content $startupSettings

#=======================================================================#
# Params                                                                #
#=======================================================================#

$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"
$appSources = "C:\Data\Sources\VipDrive_API\src_api\QuantumLogic\QuantumLogic\QuantumLogic.WebApi.csproj"
$sourceContentPath = "C:\Temp\Deploy\vipdrive-api-old"

#=======================================================================#
# Build                                                                 
#=======================================================================#

$arguments =  "$appSources", "-c:Release", "-o:$sourceContentPath"
& dotnet publish $arguments;

#=======================================================================#
# Deploy								
#=======================================================================#

$siteName = "prod-api-testdrive"
$password = "L1N8DwkFi25Bmogw1gMzcHmwsTEavZrGX7jNa11w8plCN5bor6PjtNCPALlJ"

$userName = "$" + "$siteName"
$wmsvc = "$siteName" + ".scm.azurewebsites.net:443/msdeploy.axd?site=" + "$siteName"

$msdeployArguments = '-verb:sync',
		"-source:contentPath=$sourceContentPath",
		"-dest:contentPath=$siteName,wmsvc=$wmsvc,userName=$userName,password=$password",
		'-AllowUntrusted'

 & $msdeploy $msdeployArguments;