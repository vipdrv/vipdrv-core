using System.Threading.Tasks;

namespace QuantumLogic.Core.Shared.Providers
{
    public interface IAsyncProvider<T>
    {
        Task<T> ProvideAsync();
    }
}
