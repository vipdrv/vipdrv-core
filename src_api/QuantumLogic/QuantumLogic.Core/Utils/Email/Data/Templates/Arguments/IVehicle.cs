using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Data.Templates.Arguments
{
    public interface IVehicle
    {
        string Title { get; set; }
        string ImageUrl { get; set; }
        string VdpUrl { get; set; }
        string Vin { get; set; }
        string Stock { get; set; }
        string Condition { get; set; } // new|used
        string Year { get; set; }
        string Make { get; set; }
        string Model { get; set; }
        string Body { get; set; }
        string Engine { get; set; }
        string Exterior { get; set; }
        string Interior { get; set; }
        string Drivetrain { get; set; }
        string Transmission { get; set; }
        string Msrp { get; set; }
        string InternetPrice { get; set; }
    }
}
