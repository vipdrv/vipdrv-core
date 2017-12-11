namespace QuantumLogic.WebApi.DataModels.Requests
{
    public class SwapOrdersRequest<TEntityKey>
    {
        public TEntityKey Key1 { get; set; }
        public TEntityKey Key2 { get; set; }
    }
}
