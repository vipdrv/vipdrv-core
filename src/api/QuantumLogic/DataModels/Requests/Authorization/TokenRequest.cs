namespace QuantumLogic.WebApi.DataModels.Requests.Authorization
{
    public class TokenRequest
    {
        public string Login { get; set; }
        public string Password { get; set; }
        public string GrantType { get; set; }
    }
}
