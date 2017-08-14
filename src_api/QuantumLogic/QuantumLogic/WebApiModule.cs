using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Core.Utils.RegisterConfigurationsServices;
using QuantumLogic.Data;
using QuantumLogic.WebApi.Configurations;
using System;

namespace QuantumLogic.WebApi
{
    [DependsOn(typeof(CoreModule), typeof(DataModule))]
    public class WebApiModule : Module
    {
        #region Fields

        private IConfigurationRoot _configuration;

        #endregion

        #region Ctors

        public WebApiModule(IConfigurationRoot configuration)
        {
            _configuration = configuration;
        }

        #endregion

        protected override void Configure(IServiceProvider services)
        { }

        protected override void ConfigureServices(IServiceCollection services)
        {
            RegisterConfigurationsService.Register<QuantumLogicConfiguration>(_configuration, services);
        }
    }
}
