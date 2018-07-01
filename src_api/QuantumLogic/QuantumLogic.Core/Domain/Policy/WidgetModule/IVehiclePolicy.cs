using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using System;
using System.Linq.Expressions;

namespace QuantumLogic.Core.Domain.Policy.WidgetModule
{
    public interface IVehiclePolicy : IEntityPolicy<Vehicle, int>
    {
        Expression<Func<Vehicle, bool>> GetRetrieveAllExpression();
        /// <summary>Is used to check operation "Import" access via policy</summary>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        void PolicyImport(Site site);
    }
}
