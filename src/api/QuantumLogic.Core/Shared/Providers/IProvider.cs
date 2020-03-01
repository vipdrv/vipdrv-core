namespace QuantumLogic.Core.Shared.Providers
{
    public interface IProvider<T>
    {
        T Provide();
    }
}
