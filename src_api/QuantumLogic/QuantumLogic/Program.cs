using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.Extensions.Configuration;
using System.IO;

namespace QuantumLogic.WebApi
{
    public class Program
    {
        public static void Main(string[] args)
        {
            IConfigurationRoot hostConfig = new ConfigurationBuilder()
                .SetBasePath(Directory.GetCurrentDirectory())
                .AddJsonFile("hosting.json", optional: true)
                .Build();
            IWebHost host = new WebHostBuilder()
                .UseKestrel()
                .UseContentRoot(Directory.GetCurrentDirectory())
                .UseIISIntegration()
                .UseConfiguration(hostConfig)
                .UseStartup<Startup>()
                .UseApplicationInsights()
                .Build();
            host.Run();
        }
    }
}
