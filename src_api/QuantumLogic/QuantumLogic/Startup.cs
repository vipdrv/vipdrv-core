using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.DotNet.PlatformAbstractions;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using QuantumLogic.Core.Utils.RegisterConfigurationsServices;
using QuantumLogic.WebApi.Configurations;
using QuantumLogic.WebApi.Configurations.Logging;
using Serilog;
using Serilog.Events;
using System;
using System.IO;

namespace QuantumLogic.WebApi
{
    public class Startup
    {
        #region Dependencies

        public IConfigurationRoot Configuration { get; private set; }
        public WebApiModule mainModule { get; private set; }

        #endregion

        #region Ctors

        public Startup(IHostingEnvironment env)
        {
            IConfigurationBuilder builder = new ConfigurationBuilder()
                .SetBasePath(env.ContentRootPath)
                .AddJsonFile("appsettings.json")
                .AddJsonFile($"appsettings.{env.EnvironmentName}.json", optional: true);
            builder.AddEnvironmentVariables();
            Configuration = builder.Build();
            mainModule = new WebApiModule();
        }

        #endregion

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
            RegisterConfigurationsService.Register<QuantumLogicConfiguration>(Configuration, services);
            mainModule.RegisterServices(services);
            services.AddCors(options =>
            {
                options.AddPolicy(
                    "CorsPolicy",
                    builder => builder
                        .AllowAnyOrigin()
                        .AllowAnyMethod()
                        .AllowAnyHeader()
                        .AllowCredentials());
            });
            services.AddMvc();
        }
        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IHostingEnvironment env, ILoggerFactory loggerFactory, IOptions<LoggingConfiguration> loggingConfiguration)
        {
            mainModule.Run(app.ApplicationServices);
            RegisterLogger(env, loggerFactory, loggingConfiguration.Value);
            app.Use(async (context, next) =>
            {
                // here all requests can be monitored 
                // context.Request 
                await next.Invoke();
            });
            app.UseCors("CorsPolicy");
            //app.UseIISPlatformHandler(options => options.AuthenticationDescriptions.Clear()); 
            app.UseMvc();
        }

        #region Logging

        /// <summary>
        /// Is used to register and configure all application logging
        /// </summary>
        /// <param name="env">environment</param>
        /// <param name="loggerFactory">logger factory</param>
        /// <param name="loggingConfiguration">applications logging configuration</param>
        private void RegisterLogger(IHostingEnvironment env, ILoggerFactory loggerFactory, LoggingConfiguration loggingConfiguration)
        {
            LoggerConfiguration loggerConfiguration = new LoggerConfiguration();
            LogEventLevel defaultLogLvl = (LogEventLevel)Enum.Parse(typeof(LogEventLevel), loggingConfiguration.DefaultLevel);
            loggerConfiguration.MinimumLevel.Is(defaultLogLvl);
            if (env.IsDevelopment())
            {
                loggerFactory.AddDebug(LogLevel.Trace);
            }
            RegisterSeqServerLogger(loggingConfiguration.SeqServer, loggerConfiguration);
            RegisterFileLogger(loggingConfiguration.FileLogger, loggerConfiguration);
            loggerFactory.AddSerilog(loggerConfiguration.CreateLogger());
        }
        
        /// <summary>
        /// Is used to configure and register file logger
        /// </summary>
        /// <param name="seqServerConfiguration">seq server logger configuration</param>
        /// <param name="loggerConfiguration">applications main (serilog) logger configuration</param>
        private static void RegisterSeqServerLogger(SeqServerConfiguration seqServerConfiguration, LoggerConfiguration loggerConfiguration)
        {
            if (seqServerConfiguration.Enabled)
            {
                LogEventLevel seqLogLvl = (LogEventLevel)Enum.Parse(typeof(LogEventLevel), seqServerConfiguration.Level);
                loggerConfiguration.WriteTo.Seq(seqServerConfiguration.Url, seqLogLvl);
            }
        }

        /// <summary>
        /// Is used to configure and register file logger
        /// </summary>
        /// <param name="fileLoggerConfiguration">file logger configuration</param>
        /// <param name="loggerConfiguration">applications main (serilog) logger configuration</param>
        private static void RegisterFileLogger(FileLoggerConfiguration fileLoggerConfiguration, LoggerConfiguration loggerConfiguration)
        {
            if (fileLoggerConfiguration.Enabled)
            {
                string path = fileLoggerConfiguration.IsGlobalPath ?
                    fileLoggerConfiguration.Path :
                    Path.Combine(ApplicationEnvironment.ApplicationBasePath, fileLoggerConfiguration.Path);
                LogEventLevel fileLogLvl = (LogEventLevel)Enum.Parse(typeof(LogEventLevel), fileLoggerConfiguration.Level);
                loggerConfiguration.WriteTo.RollingFile(path, fileLogLvl, fileSizeLimitBytes: fileLoggerConfiguration.FileSizeLimitBytes, retainedFileCountLimit: fileLoggerConfiguration.RetainedFileCountLimit);
            }
        }

        #endregion
    }
}
