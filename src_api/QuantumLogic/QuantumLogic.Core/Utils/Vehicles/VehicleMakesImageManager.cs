using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.Core.Utils.Vehicles
{
    public class VehicleMakesImageManager
    {
#warning use DB to fill this store
        private ISet<VehicleMake> _vehicleMakes = new HashSet<VehicleMake>()
        {

        };

        public string GetImageForMake(string makeAlias)
        {
            VehicleMake vehicleMake = _vehicleMakes
                .FirstOrDefault(r => String.Equals(r.Alias, makeAlias, StringComparison.OrdinalIgnoreCase));
            if (vehicleMake != null)
            {
                return vehicleMake.ImageUrl;
            }
            else
            {
                return String.Empty;
            }
        }
    }
}
