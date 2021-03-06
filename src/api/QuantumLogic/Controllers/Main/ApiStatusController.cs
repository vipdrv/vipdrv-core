﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Hosting;
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
        public IHostingEnvironment Env { get; }
        public ApplicationInfoConfiguration ApplicationInfoConfiguration { get; private set; }

        public ApiStatusController(IOptions<ApplicationInfoConfiguration> applicationInfoConfigurationOption, IHostingEnvironment env)
        {
            Env = env;
            ApplicationInfoConfiguration = applicationInfoConfigurationOption.Value;
        }

        [HttpGet("")]
        public ApplicationStatusDto ApiStatus()
        {
            return new ApplicationStatusDto(GetConfiguration(),
                ApplicationInfoConfiguration.BuildCounterMask,
                ApplicationInfoConfiguration.Name,
                ApplicationInfoConfiguration.Version,
                Env.EnvironmentName);
        }

        protected string GetConfiguration()
        {
            var configuration = "Release";
#if DEBUG
            configuration = "Debug";
#endif
            return configuration;
        }
    }
}
