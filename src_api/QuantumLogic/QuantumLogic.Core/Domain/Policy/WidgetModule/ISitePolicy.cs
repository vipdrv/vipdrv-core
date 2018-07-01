using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Linq;

namespace QuantumLogic.Core.Domain.Policy.WidgetModule
{
    public interface ISitePolicy : IEntityPolicy<Site, int>
    {
        /// <summary>Is used to modify query with policy rules</summary>
        /// <param name="query">query to modify</param>
        /// <returns>modified query by policy</returns>
        IQueryable<Site> ImportVehiclesAllFilter(IQueryable<Site> query);
        /// <summary>Is used to check operation "Import" access via policy</summary>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        void PolicyImportVehicles(Site entity);
        /// <summary>Is used to check operation "Import" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        bool CanImportVehicles(Site entity);
    }
}
