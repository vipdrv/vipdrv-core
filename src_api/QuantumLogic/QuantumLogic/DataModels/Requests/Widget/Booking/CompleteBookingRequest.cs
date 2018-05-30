using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Newtonsoft.Json;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using System.Globalization;
using QuantumLogic.Core.Utils.Email.Data.Templates.Arguments;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class CompleteBookingRequest
    {
        public BookingUser BookingUser { get; set; }
        public DateTime? BookingDateTimeUtc { get; set; }
        public BookingVehicle BookingVehicle { get; set; }
        public TestDriveLocation TestDriveLocation { get; set; }

        public int? ExpertId { get; set; }
        public int? BeverageId { get; set; }
        public int? RoadId { get; set; }
        public int TimeZoneOffset { get; set; }

        public CompleteBookingRequest()
        {
            BookingUser = new BookingUser();
            BookingVehicle = new BookingVehicle();
            TestDriveLocation = new TestDriveLocation();
        }

        public virtual LeadFullDto MapToLeadFullDto(int siteId)
        {
            // TODO: Add TestDriveLocation to LeadFullDto and Admin Panel
            return new LeadFullDto(
                id: 0,
                siteId: siteId,
                expertId: ExpertId,
                beverageId: BeverageId,
                routeId: RoadId,
                firstName: BookingUser.FirstName,
                secondName: BookingUser.LastName,
                userPhone: BookingUser.Phone,
                userEmail: BookingUser.Email,
                userComment: BookingUser.Comment,
                carImageUrl: BookingVehicle.ImageUrl,
                carTitle: BookingVehicle.Title,
                carVin: BookingVehicle.Vin,
                vdpUrl: BookingVehicle.VdpUrl,
                bookingDateTimeUtc: BookingDateTimeUtc,
                isNew: true,
                showLocationInfo: TestDriveLocation.ShowLocationInfo,
                locationType: TestDriveLocation.LocationType,
                locationAddress: TestDriveLocation.LocationAddress);
        }
    }

    public class TestDriveLocation
    {
        public bool ShowLocationInfo { get; set; }
        public string LocationType { get; set; }
        public string LocationAddress { get; set; }
    }

    public class BookingUser
    {
        public string FirstName { get; set; }
        public string LastName { get; set; }
        public string Phone { get; set; }
        public string Email { get; set; }
        public string Comment { get; set; }
    }

    public class BookingVehicle : IVehicle
    {
        public string Title { get; set; }
        public string ImageUrl { get; set; }
        public string VdpUrl { get; set; }
        public string Vin { get; set; }
        public string Stock { get; set; }
        public string Condition { get; set; } // new|used
        public string Year { get; set; }
        public string Make { get; set; }
        public string Model { get; set; }
        public string Body { get; set; }
        public string Engine { get; set; }
        public string Exterior { get; set; }
        public string Interior { get; set; }
        public string Drivetrain { get; set; }
        public string Transmission { get; set; }
        public string Msrp { get; set; }
        public string InternetPrice { get; set; }

        public BookingVehicle() { }

        public BookingVehicle(string title,
            string imageUrl,
            string vdpUrl,
            string vin,
            string stock,
            string condition,
            string year,
            string make,
            string model,
            string body,
            string engine,
            string exterior,
            string interior,
            string drivetrain,
            string transmission,
            string msrp,
            string internetPrice)
        {
            Title = title;
            ImageUrl = imageUrl;
            VdpUrl = vdpUrl;
            Vin = vin;
            Stock = stock;
            Condition = condition;
            Year = year;
            Make = make;
            Model = model;
            Body = body;
            Engine = engine;
            Exterior = exterior;
            Interior = interior;
            Drivetrain = drivetrain;
            Transmission = transmission;
            Msrp = msrp;
            InternetPrice = internetPrice;
        }
    }
}
