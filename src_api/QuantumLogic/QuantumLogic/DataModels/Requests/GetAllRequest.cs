using System;

namespace QuantumLogic.WebApi.DataModels.Requests
{
    public class GetAllRequest
    {
        public string Sorting { get; set; }

#warning ad-hock that have to be removed after policy implementation
        [Obsolete]
        public int? UserId { get; set; }
    }
}
