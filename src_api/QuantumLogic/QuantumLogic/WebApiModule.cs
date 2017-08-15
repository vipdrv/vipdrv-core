using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Data;
using System;

namespace QuantumLogic.WebApi
{
    [DependsOn(typeof(CoreModule), typeof(DataModule))]
    public class WebApiModule : Module
    {
        #region Ctors

        public WebApiModule()
        { }

        #endregion

        protected override void Configure(IServiceProvider services)
        { }

        protected override void ConfigureServices(IServiceCollection services)
        { }
    }
}
