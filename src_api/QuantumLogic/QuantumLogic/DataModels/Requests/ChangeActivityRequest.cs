namespace QuantumLogic.WebApi.DataModels.Requests
{
    public class ChangeActivityRequest : PatchBoolPropertyRequest
    {
        public bool Value { get; set; }
    }
}
