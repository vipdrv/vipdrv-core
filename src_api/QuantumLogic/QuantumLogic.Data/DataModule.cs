using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core;
using QuantumLogic.Core.Domain.Repositories;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Utils.Modules;
using QuantumLogic.Core.Utils.Modules.Attributes;
using QuantumLogic.Data.EFContext;
using QuantumLogic.Data.EFUnitOfWork;
using QuantumLogic.Data.Repositories;
using System;

namespace QuantumLogic.Data
{
    [DependsOn(typeof(CoreModule))]
    public class DataModule : Module
    {
        protected override void Configure(IServiceProvider services)
        { }

        protected override void ConfigureServices(IServiceCollection services)
        {
            services.AddScoped<DbContextManager>();
            services.AddTransient<DbContext, QuantumLogicDbContext>();
            services.AddTransient<IQLUnitOfWork, QuantumLogicUnitOfWork>();
            services.AddTransient<IQLUnitOfWorkManager, QuantumLogicUnitOfWorkManager>();

            #region Repositories

            services.Add(ServiceDescriptor.Transient(typeof(IQLRepository<,>), typeof(EFRepository<,>)));
            //services.AddTransient<IRepository<Entity, long>, EntityRepository>();

            #endregion
        }
    }
}
