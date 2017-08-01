using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Web;
using System.Web.Http;
using Newtonsoft.Json.Linq;

namespace QuantumLogic.WebApi.Controllers
{
    public class SystemStatusController : ApiController
    {
        [Route("")]
        [HttpGet]
        public JObject Status(HttpRequestMessage req)
        {
            var response = new JObject();
            response["11"] = 11;
            response["22"] = 22;
            response["kiev_time"] = DateTime.UtcNow.AddHours(2);
            return response;
        }
    }
}