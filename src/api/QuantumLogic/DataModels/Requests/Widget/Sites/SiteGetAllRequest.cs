namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Sites
{
    public class SiteGetAllRequest : GetAllRequest
    {
        public long? Id { get; set; }
        public string Dealer { get; set; }
        public string Name { get; set; }
    }
}
