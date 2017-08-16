using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.Core.Utils.Modules.Attributes;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;

namespace QuantumLogic.Core.Utils.Modules
{
    public abstract class Module
    {
        protected List<Module> Modules { get; private set; }

        #region Ctors

        public Module()
        {
            Load();
        }

        #endregion

        public void Run(IServiceProvider provider)
        {
            Modules.ForEach(r => r.Configure(provider));
        }

        public void RegisterServices(IServiceCollection serviceCollection)
        {
            Modules.ForEach(r => r.ConfigureServices(serviceCollection));
        }

        #region Helpers

        protected abstract void ConfigureServices(IServiceCollection services);

        protected abstract void Configure(IServiceProvider services);

        protected void Load()
        {
            try
            {
                Type thisType = GetType();
                List<Type> types = GetModules(thisType)
                    .Distinct()
                    .OrderBy(r => r.GetTypeInfo().GetCustomAttribute<OrderAttribute>()?.Order ?? 0)
                    .ToList();
                List<Module> modules = types.Select(r => (Module)Activator.CreateInstance(r)).Concat(Enumerable.Repeat(this, 1)).ToList();
                Modules = modules;
            }
            catch (Exception ex)
            {
                throw new Exception($"Load module {GetType().Name} fault.", ex);
            }
        }

        private Type[] GetModules(Type module)
        {
            List<Type> dependsOn = module.GetTypeInfo().GetCustomAttribute<DependsOnAttribute>()?.DependsOnModuleTypes?.ToList();
            if (dependsOn == null)
            {
                return new Type[0];
            }
            List<Type> chieldDepends = dependsOn.SelectMany(r => GetModules(r)).ToList();
            return dependsOn.Concat(chieldDepends).ToArray();
        }

        #endregion
    }
}
