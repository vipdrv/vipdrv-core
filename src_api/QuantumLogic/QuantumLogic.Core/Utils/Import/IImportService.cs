using QuantumLogic.Core.Utils.Import.DataModels;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Import
{
    public interface IImportService<T>
    {
        Task<IImportResult<T>> ImportAsync(IImportSettings settings);
    }
}
