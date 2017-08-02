$msdeploy = "C:\Program Files\IIS\Microsoft Web Deploy V3\msdeploy.exe"

$contentPath = "C:\Data\Sources\SandBox\elrondsoft-landing";

$msdeployArguments = '-verb:sync',
		'-source:contentPath="C:\Data\Sources\Quantum\src_landing"','-dest:contentPath="dev-landing-quantumlogic",wmsvc=dev-landing-quantumlogic.scm.azurewebsites.net:443/msdeploy.axd?site=dev-landing-quantumlogic,userName=$dev-landing-quantumlogic,password=eC7EBthilcCFama9bXqPkqComQbSmXBYvgDmZ29PEtZaZx9TCE8d2jjL1NNa',
		'-skip:Directory="node_modules"',
		# '-skip:Directory="uploads"',
		'-AllowUntrusted'
		
& $msdeploy $msdeployArguments

# -verb:sync 
# -source:contentPath="C:\Data\Sources\GhabbourAuto\src_web"  
# -dest:contentPath="dev-ghabbourauto",wmsvc=dev-ghabbourauto.scm.azurewebsites.net:443/msdeploy.axd?site=dev-ghabbourauto,userName=$dev-ghabbourauto,password=Hs8iw2SnWGneZRdjD7BGRKtJuSvhYG3xRqTJa04MStLgefRERfihfkA3Y5sC
# -skip:Directory="node_modules"
# -skip:Directory="uploads" -AllowUntrusted

