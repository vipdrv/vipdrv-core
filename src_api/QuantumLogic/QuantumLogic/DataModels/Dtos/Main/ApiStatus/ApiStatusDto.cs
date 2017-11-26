using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.ApiStatus
{
    public class ApiStatusDto
    {
        public string Assembly { get; set; }
        public string Configuration { get; set; }
        public string BuildCounter { get; set; }

        public ApiStatusDto(string aseembly, string configuration)
        {
            Assembly = aseembly;
            Configuration = configuration;
            BuildCounter = "-build.counter-";

        }
    }
}
