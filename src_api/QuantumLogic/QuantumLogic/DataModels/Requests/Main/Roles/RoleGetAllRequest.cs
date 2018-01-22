namespace QuantumLogic.WebApi.DataModels.Requests.Main.Roles
{
    public class RoleGetAllRequest : GetAllRequest
    {
        public string Name { get; set; }
        public bool? CanBeUsedForInvitation { get; set; }
    }
}
