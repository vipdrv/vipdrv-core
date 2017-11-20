using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json.Linq;
using QuantumLogic.WebApi.DataModels.Dtos.Main.ApiStatus;

namespace QuantumLogic.WebApi.Controllers.Main
{
    public class ApiStatusController : Controller
    {
        [HttpGet("")]
        public ApiStatusDto ApiStatus(int id)
        {
            var apiStatus = new ApiStatus(Assembly.GetEntryAssembly());
            return apiStatus.GetApiStatusDto();
        }
    }

    public class ApiStatus
    {
        private readonly Assembly _assembly;
        
        public ApiStatus(Assembly assembly)
        {
            _assembly = assembly;
        }

        public string GetAssemblyVersion()
        {
            return _assembly
                .GetCustomAttribute<AssemblyInformationalVersionAttribute>()
                .InformationalVersion;
        }

        public string GetConfiguration()
        {
            var configuration = "Release";
#if DEBUG
            configuration = "Debug";
#endif
            return configuration;
        }

        public ApiStatusDto GetApiStatusDto()
        {
            return new ApiStatusDto(GetAssemblyVersion(), GetConfiguration());
        }
    }
}
