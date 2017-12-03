using System;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Leads
{
    public class LeadGetAllRequest : GetAllRequest
    {
        public int? SiteId { get; set; }
        public DateTime? RecievedDateTime { get; set; }
        public string FullName { get; set; }
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string Site { get; set; }
        public string Email { get; set; }
        public string Phone { get; set; }
        public string Expert { get; set; }
        public string Route { get; set; }
        public string Beverage { get; set; }
        public bool? IsReachedByManager { get; set; }
    }
}
