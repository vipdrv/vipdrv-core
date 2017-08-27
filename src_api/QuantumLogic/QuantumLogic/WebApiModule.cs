using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Validation;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Data;
using QuantumLogic.WebApi.Authorization;
using QuantumLogic.WebApi.Authorization.PermissionCheckers;
using QuantumLogic.WebApi.Policy.Main;
using QuantumLogic.WebApi.Validation;
using QuantumLogic.WebApi.Validation.Main;
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

            //services.Add(ServiceDescriptor.Scoped(typeof(IEntityPolicy<,>), typeof(NullEntityPolicy<,>)));
            services.AddScoped<IEntityPolicy<User, int>, UserPolicy>();
            services.AddScoped<IUserPolicy, UserPolicy>();

            #endregion

            #region Validation services registration

            //services.Add(ServiceDescriptor.Scoped(typeof(IEntityValidationService<,>), typeof(NullEntityValidationService<,>)));
            services.AddScoped<IEntityValidationService<User, int>, UserValidationService>();
            services.AddScoped<IUserValidationService, UserValidationService>();

            #endregion
        }
    }
}
