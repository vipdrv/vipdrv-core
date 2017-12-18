using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.WebApi.Providers.Export.Excel.DataModels;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Providers.Export.Excel.Leads
{
    public static class ExcelExportLeadOptionsProvider
    {
        public static IList<EntityPropertyMapper<Lead>> GetEntityOptions(Func<EntityPropertyMapper<Lead>, bool> filter, Func<string, string> localize, TimeSpan timeZoneOffset)
        {
            return new List<EntityPropertyMapper<Lead>>()
            {
                new EntityPropertyMapper<Lead>("Id", localize("Id"), (r => r.Id), false),
                //new EntityPropertyMapper<Lead>("IsReachedByManager", localize("Reached by manager"), (r => r.IsReachedByManager), false),
                new EntityPropertyMapper<Lead>("FirstName", localize("First name"), (r => r.FirstName), true),
                new EntityPropertyMapper<Lead>("SecondName", localize("Second name"), (r => r.SecondName), true),
                new EntityPropertyMapper<Lead>("Site", localize("Site"), (r => r.Site.Name), true),
                new EntityPropertyMapper<Lead>("RecievedDateTimeUtc", localize("Recieved date"), (r => r.RecievedUtc.FormatUtcDateTimeToUserFriendlyString(timeZoneOffset)), true),
                new EntityPropertyMapper<Lead>("BookingDateTimeUtc", localize("Booking date"), (r => r.BookingDateTimeUtc.FormatUtcDateTimeToUserFriendlyString(timeZoneOffset)), true),
                new EntityPropertyMapper<Lead>("Expert", localize("Expert"), (r => r.Expert.Name), true),
                new EntityPropertyMapper<Lead>("Route", localize("Route"), (r => r.Route.Name), true),
                new EntityPropertyMapper<Lead>("Beverage", localize("Beverage"), (r => r.Beverage.Name), true),
                new EntityPropertyMapper<Lead>("UserEmail", localize("Email"), (r => r.UserEmail), true),
                new EntityPropertyMapper<Lead>("UserPhone", localize("Phone"), (r => r.UserPhone), true),
                new EntityPropertyMapper<Lead>("CarTitle", localize("Car title"), (r => r.CarTitle), true),
                new EntityPropertyMapper<Lead>("CarVin", localize("Car vin"), (r => r.CarVin), true)
            }
            .Where(filter)
            .ToList();
        }
    }
}
