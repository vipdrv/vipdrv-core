using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public interface ILeadDomainService : IEntityDomainService<Lead, int>
    {
        Task<string> ExportDataToExcelAsync(string fileName, string worksheetsName, TimeSpan timeZoneOffset, Expression<Func<Lead, bool>> filter = null, string sorting = null, int skip = 0, int take = 0);
    }
}
