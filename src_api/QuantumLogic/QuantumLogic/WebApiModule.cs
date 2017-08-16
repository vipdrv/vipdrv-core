using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Validation;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Data;
using QuantumLogic.WebApi.Authorization;
using QuantumLogic.WebApi.Authorization.PermissionCheckers;
using QuantumLogic.WebApi.Policy;
using QuantumLogic.WebApi.Validation;
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
        {
            services.AddScoped<IQLSession, QLSession>();
            services.AddScoped<IQLPermissionChecker, NullQLPermissionChecker>();

            #region Policy registration

            services.Add(ServiceDescriptor.Scoped(typeof(IEntityPolicy<,>), typeof(NullEntityPolicy<,>)));

            #endregion

            #region Validation services registration

            services.Add(ServiceDescriptor.Scoped(typeof(IEntityValidationService<,>), typeof(NullEntityValidationService<,>)));
            //services.AddScoped<IEntityValidationService<Entity, PrimaryKey>, EntityValidationService<Entity, PrimaryKey>>();

            #endregion
        }
    }
}
