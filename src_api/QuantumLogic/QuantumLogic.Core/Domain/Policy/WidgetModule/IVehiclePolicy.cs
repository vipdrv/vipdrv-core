using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;
using System.Linq.Expressions;

namespace QuantumLogic.Core.Domain.Policy.WidgetModule
{
    public interface IVehiclePolicy : IEntityPolicy<Vehicle, int>
    {
        Expression<Func<Vehicle, bool>> GetRetrieveAllExpression();
    }
}
