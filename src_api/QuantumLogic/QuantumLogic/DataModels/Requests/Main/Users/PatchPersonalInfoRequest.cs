namespace QuantumLogic.WebApi.DataModels.Requests.Main.Users
{
    public class PatchPersonalInfoRequest
    {
        public string FirstName { get; set; }
        public string SecondName { get; set; }

        public string Email { get; set; }
        public string PhoneNumber { get; set; }
    }
}
