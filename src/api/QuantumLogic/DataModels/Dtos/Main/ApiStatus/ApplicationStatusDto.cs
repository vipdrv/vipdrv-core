using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.Extensions.Options;
using QuantumLogic.WebApi.Configurations;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.ApiStatus
{
    public class ApplicationStatusDto
    {
        public string Name { get; private set; }
        public string Version { get; private set; }
        public string Build { get; private set; }
        public string Configuration { get; private set; }
        public string Environment { get; }

        public ApplicationStatusDto(string configuration, 
            string build, 
            string name,
            string version, 
            string environment)
        {
            Configuration = configuration;
            Build = build;
            Name = name;
            Version = version;
            Environment = environment;
        }
    }
}
