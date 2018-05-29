using System.Collections.Generic;
using System.Linq;
using NUnit.Framework;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Ftp;
using QuantumLogic.Core.Utils.Inventory;
using QuantumLogic.Core.Utils.Inventory.Data;
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
                    FeedProvider = new VAutoInventoryProvider()
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
                    FeedFolder = "/DealerFeed/28-TruckWorld",
                    FeedProvider = new VAutoInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 36,
                    FeedFolder = "/DealerFeed/36-MBRVC",
                    FeedProvider = new VAutoInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 47,
                    FeedFolder = "/DealerFeed/47-Downey Nissan",
                    FeedProvider = new VAutoInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 54,
                    FeedFolder = "/DealerFeed/54-tafel-motors",
                    FeedProvider = new VAutoInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 55,
                    FeedFolder = "/DealerFeed/55-mb-cincy",
                    FeedProvider = new VAutoInventoryProvider()
                },
                new SiteFeed()
                {
                    SiteId = 56,
                    FeedFolder = "/DealerFeed/56-mb-west-chester",
                    FeedProvider = new VAutoInventoryProvider()
                }
            };
        }

        [Test, MaxTime(360000)]
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
            IList<Vehicle> allVehiclesFromFeedFiles = inventoryService.GetVehiclesListFromFeedFiles(prodDealerFeed);

            QuantumLogicDbContext quantumLogicDbContext = new QuantumLogicDbContext();
            IQueryable<Vehicle> oldVehicles = quantumLogicDbContext.Vehicles.Where(r => prodDealerWebsiteIds.Contains(r.SiteId));

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
