using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.Services.Main;
using QuantumLogic.Core.Utils.Modules;
using System;

namespace QuantumLogic.Core
{
    public class CoreModule : Module
    {
        protected override void Configure(IServiceProvider services)
        { }

        protected override void ConfigureServices(IServiceCollection services)
        {
            services.AddScoped<IDomainContext, DomainContext>();
            
            services.AddScoped<IEntityDomainService<User, int>, UserDomainService>();
            services.AddScoped<IUserDomainService, UserDomainService>();
        }
    }
}
