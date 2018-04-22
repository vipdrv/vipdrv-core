using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Validation;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Data;
using QuantumLogic.WebApi.Authorization;
using QuantumLogic.WebApi.Authorization.PermissionCheckers;
using QuantumLogic.WebApi.Policy.Main;
using QuantumLogic.WebApi.Policy.Widget;
using QuantumLogic.WebApi.Validation.Main;
using QuantumLogic.WebApi.Validation.Widget;
using System;
using System.IdentityModel.Tokens.Jwt;

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
            services.AddScoped<IQLSession, HttpContextSession>();
            services.AddScoped<IQLPermissionChecker, QLPermissionChecker>();
            services.AddTransient<JwtSecurityTokenHandler, JwtSecurityTokenHandler>();

            #region Policy registration

            //services.Add(ServiceDescriptor.Scoped(typeof(IEntityPolicy<,>), typeof(NullEntityPolicy<,>)));

            services.AddScoped<IEntityPolicy<Invitation, int>, InvitationPolicy>();
            services.AddScoped<IInvitationPolicy, InvitationPolicy>();
            services.AddScoped<IEntityPolicy<User, int>, UserPolicy>();
            services.AddScoped<IUserPolicy, UserPolicy>();
            services.AddScoped<IEntityPolicy<Role, int>, RolePolicy>();
            services.AddScoped<IRolePolicy, RolePolicy>();

            services.AddScoped<IEntityPolicy<Beverage, int>, BeveragePolicy>();
            services.AddScoped<IEntityExtendedPolicy<Beverage, int>, BeveragePolicy>();
            services.AddScoped<IBeveragePolicy, BeveragePolicy>();
            services.AddScoped<IEntityPolicy<Expert, int>, ExpertPolicy>();
            services.AddScoped<IEntityExtendedPolicy<Expert, int>, ExpertPolicy>();
            services.AddScoped<IExpertPolicy, ExpertPolicy>();
            services.AddScoped<IEntityPolicy<Lead, int>, LeadPolicy>();
            services.AddScoped<ILeadPolicy, LeadPolicy>();
            services.AddScoped<IEntityPolicy<Route, int>, RoutePolicy>();
            services.AddScoped<IEntityExtendedPolicy<Route, int>, RoutePolicy>();
            services.AddScoped<IRoutePolicy, RoutePolicy>();
            services.AddScoped<IEntityPolicy<Site, int>, SitePolicy>();
            services.AddScoped<ISitePolicy, SitePolicy>();
            services.AddScoped<IEntityPolicy<Step, int>, StepPolicy>();
            services.AddScoped<IStepPolicy, StepPolicy>();
            services.AddScoped<IEntityPolicy<Vehicle, int>, VehiclePolicy>();
            services.AddScoped<IVehiclePolicy, VehiclePolicy>();

            #endregion

            #region Validation services registration

            //services.Add(ServiceDescriptor.Scoped(typeof(IEntityValidationService<,>), typeof(NullEntityValidationService<,>)));

            services.AddScoped<IEntityValidationService<Invitation, int>, InvitationValidationService>();
            services.AddScoped<IInvitationValidationService, InvitationValidationService>();
            services.AddScoped<IEntityValidationService<User, int>, UserValidationService>();
            services.AddScoped<IUserValidationService, UserValidationService>();
            services.AddScoped<IEntityValidationService<Role, int>, RoleValidationService>();
            services.AddScoped<IRoleValidationService, RoleValidationService>();

            services.AddScoped<IEntityValidationService<Beverage, int>, BeverageValidationService>();
            services.AddScoped<IEntityExtendedValidationService<Beverage, int>, BeverageValidationService>();
            services.AddScoped<IBeverageValidationService, BeverageValidationService>();
            services.AddScoped<IEntityValidationService<Expert, int>, ExpertValidationService>();
            services.AddScoped<IEntityExtendedValidationService<Expert, int>, ExpertValidationService>();
            services.AddScoped<IExpertValidationService, ExpertValidationService>();
            services.AddScoped<IEntityValidationService<Lead, int>, LeadValidationService>();
            services.AddScoped<ILeadValidationService, LeadValidationService>();
            services.AddScoped<IEntityValidationService<Route, int>, RouteValidationService>();
            services.AddScoped<IEntityExtendedValidationService<Route, int>, RouteValidationService>();
            services.AddScoped<IRouteValidationService, RouteValidationService>();
            services.AddScoped<IEntityValidationService<Site, int>, SiteValidationService>();
            services.AddScoped<ISiteValidationService, SiteValidationService>();
            services.AddScoped<IEntityValidationService<Step, int>, StepValidationService>();
            services.AddScoped<IStepValidationService, StepValidationService>();
            services.AddScoped<IEntityValidationService<Vehicle, int>, VehicleValidationService>();
            services.AddScoped<IVehicleValidationService, VehicleValidationService>();

            #endregion
        }
    }
}
