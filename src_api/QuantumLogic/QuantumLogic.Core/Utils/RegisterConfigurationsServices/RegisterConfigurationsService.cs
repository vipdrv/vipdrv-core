using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;

namespace QuantumLogic.Core.Utils.RegisterConfigurationsServices
{
    public static class RegisterConfigurationsService
    {
        public const string ConfigurationFileTrack = "Configuration";

        public static void Register<TConfiguration>(IConfiguration configuration, IServiceCollection serviceCollection)
        {
            TypeInfo ti = typeof(TConfiguration).GetTypeInfo();
            InnerRegister(ti, configuration, serviceCollection);
        }

        #region Helpers

        private static void InnerRegister(TypeInfo configurationType, IConfiguration configuration, IServiceCollection serviceCollection)
        {
            List<TypeInfo> children = configurationType.DeclaredProperties
                .Select(r => r.PropertyType.GetTypeInfo())
                .Where(r => r.IsClass && !(r == typeof(string).GetTypeInfo()) && !r.IsArray)
                .ToList();
            MethodInfo method = typeof(OptionsConfigurationServiceCollectionExtensions).GetTypeInfo()
                .GetDeclaredMethods(nameof(OptionsConfigurationServiceCollectionExtensions.Configure))
                .First(r =>
                    {
                        var p = r.GetParameters();
                        return p.Length == 2 && p[1].ParameterType == typeof(IConfiguration);
                    })
                .MakeGenericMethod(configurationType.AsType());
            method.Invoke(null, new object[2] { serviceCollection, configuration });
            children.ForEach(r => InnerRegister(r, configuration.GetSection(r.Name.Substring(0, r.Name.Length - ConfigurationFileTrack.Length)), serviceCollection));
        }

        #endregion
    }
}
