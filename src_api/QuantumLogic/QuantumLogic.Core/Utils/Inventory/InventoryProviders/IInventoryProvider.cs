using System.Collections.Generic;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Inventory.InventoryProviders
{
    public interface IInventoryProvider
    {
        /// <summary>
        /// Is used 
        /// </summary>
        /// <param name="fileLocation">CSV file location</param>
        /// <param name="siteId">Set specific SiteId fo every parsed vehicle</param>
        /// <returns>Returns list of vehicles parsed from Feed files</returns>
        IList<Vehicle> ParseVehiclesFromCsv(string fileLocation, int siteId);
    }
}
