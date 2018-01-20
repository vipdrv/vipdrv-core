using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Options;
using Newtonsoft.Json.Linq;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.WebApi.Configurations;
using QuantumLogic.WebApi.DataModels.Dtos.Main.ApiStatus;

namespace QuantumLogic.WebApi.Controllers.Main
{
    public class ApiStatusController : Controller
    {
        public ApplicationInfoConfiguration ApplicationInfoConfiguration { get; private set; }
        protected IBeverageDomainService BeverageDomainService;

        public ApiStatusController(IOptions<ApplicationInfoConfiguration> applicationInfoConfigurationOption,
            IBeverageDomainService beverageDomainService)
        {
            ApplicationInfoConfiguration = applicationInfoConfigurationOption.Value;
            BeverageDomainService = beverageDomainService;
        }

        [HttpGet("")]
        public ApplicationStatusDto ApiStatus()
        {
#warning This method get called every 5 minutes from remote scheduler and retrieve Beverage entity from Database to prevent API idle
            RetrieveSingleBeverage();
            return new ApplicationStatusDto(GetConfiguration(),
                ApplicationInfoConfiguration.BuildCounterMask,
                ApplicationInfoConfiguration.Name,
                ApplicationInfoConfiguration.Version);
        }

        protected string GetConfiguration()
        {
            var configuration = "Release";
#if DEBUG
            configuration = "Debug";
#endif
            return configuration;
        }

        protected string RetrieveSingleBeverage()
        {
            return BeverageDomainService.RetrieveAllAsync(null, null, 0, 1).Result.FirstOrDefault().Name;
        }
    }
}
