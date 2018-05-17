using System;
using System.Collections.Generic;
using System.Text;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Inventory.Data;

namespace QuantumLogic.Core.Utils.Inventory
{
    public interface IInventoryService
    {
        /// <summary>
        /// Is used to download and parse vehicles data from Feed files on FTP server 
        /// </summary>
        /// <param name="dealersFeeds">List of sites with FTP details</param>
        /// <returns>Returns list of vehicles parsed from Feed files</returns>
        IList<Vehicle> GetVehiclesListFromFeedFiles(IList<SiteFeed> dealersFeeds);
    }
}
