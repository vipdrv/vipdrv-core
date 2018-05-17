using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Inventory.InventoryProviders
{
    public abstract class BaseInventoryProvider
    {
        protected string ComposeVehicle(VehicleConditions condition, string make, string model, int year)
        {
            string vehicleCondition = "";
            if (condition == VehicleConditions.New)
            {
                vehicleCondition = "New";
            }
            else if ((condition == VehicleConditions.Used))
            {
                vehicleCondition = "Used";
            }

            return $"{vehicleCondition} {make} {model} {year}";
        }
    }
}
