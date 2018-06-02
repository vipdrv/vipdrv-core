using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.VehicleMakes
{
    public class VehicleMakesImageManager
    {
        // make name (key) should be in UpperInvariant
        private IDictionary<string, string> _makeImages = new Dictionary<string, string>()
        {

        };

        public string GetImageForMake(string make)
        {
            string imageUrl;
            if (!_makeImages.TryGetValue(make.ToUpperInvariant(), out imageUrl))
            {
                imageUrl = String.Empty;
            }
            return imageUrl;
        }
    }
}
