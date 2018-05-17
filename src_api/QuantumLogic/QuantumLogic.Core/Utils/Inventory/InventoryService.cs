using System;
using System.Collections.Generic;
using System.IO;
using System.Text;
using Csv;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Ftp;
using QuantumLogic.Core.Utils.Inventory.Data;

namespace QuantumLogic.Core.Utils.Inventory
{
    public class InventoryService : IInventoryService
    {
        protected IFtpService FtpService;
        protected string LocalPath;

        public InventoryService(IFtpService ftpService, string localPath)
        {
            FtpService = ftpService;
            LocalPath = localPath;
        }

        public IList<Vehicle> GetVehiclesListFromFeedFiles(IList<SiteFeed> dealersFeeds)
        {
            List<Vehicle> globalVehiclesList = new List<Vehicle>();
            foreach (var dealersFeed in dealersFeeds)
            {
                string lastModidiedFile = FtpService.LastModidiedFileInFolder(dealersFeed.FeedFolder);
                if (FtpService.DownloadFile(LocalPath, lastModidiedFile))
                {
                    IList<Vehicle> vehiclesFromCsv = dealersFeed.FeedProvider.ParseVehiclesFromCsv(File.ReadAllText(LocalPath), dealersFeed.SiteId);
                    globalVehiclesList.AddRange(vehiclesFromCsv);
                }
            }
            return globalVehiclesList;
        }
    }
}
