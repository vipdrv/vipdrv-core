using QuantumLogic.Core.Utils.Inventory.InventoryProviders;

namespace QuantumLogic.Core.Utils.Inventory.Data
{
    public class SiteFeed
    {
        public int SiteId { get; set; }
        public string FeedFolder { get; set; }
        public IInventoryProvider FeedProvider { get; set; }
    }
}