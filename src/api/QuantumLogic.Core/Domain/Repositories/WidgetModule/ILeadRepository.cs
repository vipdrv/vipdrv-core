using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Repositories.WidgetModule
{
    public interface ILeadRepository : IQLRepository<Lead, int>
    {
        Task NullifyBeverageRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder);
        Task NullifyExpertRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder);
        Task NullifyRouteRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder);
    }
}
