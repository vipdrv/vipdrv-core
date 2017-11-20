namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Leads
{
    public class LeadGetAllRequest : GetAllRequest
    {
        public string RecievedDateTime { get; set; }
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string Site { get; set; }
        public string Email { get; set; }
        public string Phone { get; set; }
        public string Expert { get; set; }
        public string Route { get; set; }
        public string Beverage { get; set; }
    }
}
