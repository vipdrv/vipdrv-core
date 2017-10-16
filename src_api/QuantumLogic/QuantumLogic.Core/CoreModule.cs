using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.Services.Main.Invitations;
using QuantumLogic.Core.Domain.Services.Main.Users;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.Core.Domain.Services.Widget.Experts;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.Services.Widget.Routes;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.ContentManager.Providers;
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
            services.AddScoped<IContentManager, BlobProvider>();

            #region Domain services

            #region Main
            
            services.AddScoped<IEntityDomainService<Invitation, int>, InvitationDomainService>();
            services.AddScoped<IInvitationDomainService, InvitationDomainService>();
            services.AddScoped<IEntityDomainService<User, int>, UserDomainService>();
            services.AddScoped<IUserDomainService, UserDomainService>();

            #endregion

            #region Widget

            services.AddScoped<IEntityDomainService<Beverage, int>, BeverageDomainService>();
            services.AddScoped<IEntityExtendedDomainService<Beverage, int>, BeverageDomainService>();
            services.AddScoped<IBeverageDomainService, BeverageDomainService>();
            services.AddScoped<IEntityDomainService<Expert, int>, ExpertDomainService>();
            services.AddScoped<IEntityExtendedDomainService<Expert, int>, ExpertDomainService>();
            services.AddScoped<IExpertDomainService, ExpertDomainService>();
            services.AddScoped<IEntityDomainService<Lead, int>, LeadDomainService>();
            services.AddScoped<ILeadDomainService, LeadDomainService>();
            services.AddScoped<IEntityDomainService<Route, int>, RouteDomainService>();
            services.AddScoped<IEntityExtendedDomainService<Route, int>, RouteDomainService>();
            services.AddScoped<IRouteDomainService, RouteDomainService>();
            services.AddScoped<IEntityDomainService<Site, int>, SiteDomainService>();
            services.AddScoped<ISiteDomainService, SiteDomainService>();

            #endregion

            #endregion
        }
    }
}
