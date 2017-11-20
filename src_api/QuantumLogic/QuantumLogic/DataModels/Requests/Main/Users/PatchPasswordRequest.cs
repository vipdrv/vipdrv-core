namespace QuantumLogic.WebApi.DataModels.Requests.Main.Users
{
    public class PatchPasswordRequest
    {
        public string OldPassword { get; set; }
        public string NewPassword { get; set; }
    }
}
