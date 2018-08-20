using System.Threading.Tasks;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Domain.Services.Widget.WidgetEvents
{
    public interface IWidgetEventDomainService : IEntityDomainService<WidgetEvent, int>
    {
        Task ChangeIsResolvedAsync(int id, bool value);
    }
}
