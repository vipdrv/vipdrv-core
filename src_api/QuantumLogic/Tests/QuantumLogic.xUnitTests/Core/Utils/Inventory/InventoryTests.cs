using System.Collections.Generic;
using System.Linq;
using NUnit.Framework;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Ftp;
using QuantumLogic.Core.Utils.Inventory;
using QuantumLogic.Core.Utils.Inventory.Data;
using QuantumLogic.Core.Utils.Inventory.InventoryProviders.Homenet;
using QuantumLogic.Core.Utils.Inventory.InventoryProviders.Truckworld;
using QuantumLogic.Core.Utils.Inventory.InventoryProviders.vAuto;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.xUnitTests.Core.Utils.Inventory
{
    [TestFixture]
    public class InventoryTests
    {
        private IList<SiteFeed> DevDealerFeed()
        {
            return new List<SiteFeed>()
            {
                new SiteFeed()
                {
                    SiteId = 28,
                    FeedFolder = "/SiteFeed/28-TruckWorld",
                    FeedProvider = new TruckworldInventoryProvider()
                }
            };
        }

        private IList<SiteFeed> ProdDealerFeed()
        {
            return new List<SiteFeed>()
            {
                new SiteFeed()
                {
                    SiteId = 28,
                    FeedFolder = "/SiteFeed/28-TruckWorld",
                    FeedProvider = new TruckworldInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 36,
                    FeedFolder = "/SiteFeed/36-MBRVC",
                    FeedProvider = new HomenetInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 47,
                    FeedFolder = "/SiteFeed/47-Downey Nissan",
                    FeedProvider = new VAutoInventoryProvider()
                }
            };
        }

        [Test]
        [Ignore("Read database usage")]
        public void ParseFeed__ShouldWork()
        {
            IList<SiteFeed> devDealerFeed = DevDealerFeed();
            IList<SiteFeed> prodDealerFeed = ProdDealerFeed();

            int[] devDealerWebsiteIds = new int[devDealerFeed.Count];
            int[] prodDealerWebsiteIds = new int[prodDealerFeed.Count];

            int i = 0;
            foreach (var item in devDealerFeed)
            {
                devDealerWebsiteIds[i++] = item.SiteId;
            }

            int j = 0;
            foreach (var item in prodDealerFeed)
            {
                prodDealerWebsiteIds[j++] = item.SiteId;
            }

            string tempoparyPathToSaveFile = @"C:\Temp\test.csv"; // Path.GetTempPath();
            InventoryService inventoryService = new InventoryService(new FtpService(), tempoparyPathToSaveFile);
            IList<Vehicle> allVehiclesFromFeedFiles = inventoryService.GetVehiclesListFromFeedFiles(DevDealerFeed());

            QuantumLogicDbContext quantumLogicDbContext = new QuantumLogicDbContext();
            IQueryable<Vehicle> oldVehicles = quantumLogicDbContext.Vehicles.Where(r => devDealerWebsiteIds.Contains(r.SiteId));

            foreach (var vehicle in oldVehicles)
            {
                quantumLogicDbContext.Vehicles.Remove(vehicle);
            }

            foreach (var vehicle in allVehiclesFromFeedFiles)
            {
                quantumLogicDbContext.Vehicles.Add(vehicle);
            }

            quantumLogicDbContext.SaveChanges();
        }
    }
}
