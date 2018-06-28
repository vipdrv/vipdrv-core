using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Utils.Import.DataModels;
using System;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleImportResult : ImportResult<Vehicle>
    {
        #region Ctors

        public VehicleImportResult(IEnumerable<Vehicle> data, TimeSpan elapsedTime) 
            : base(data, elapsedTime)
        { }

        #endregion
    }
}
