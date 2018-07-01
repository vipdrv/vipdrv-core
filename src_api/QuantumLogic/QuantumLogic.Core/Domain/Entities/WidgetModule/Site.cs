using QuantumLogic.Core.Domain.Entities.MainModule;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Site : Entity<int>, IValidable, IUpdatableFrom<Site>
    {
        #region Fields

        public int UserId { get; set; }
        public string BeautyId { get; set; }
        public string Name { get; set; }
        public string Url { get; set; }
        public string NotificationContacts { get; set; }
        public string ImageUrl { get; set; }
        public string DealerName { get; set; }
        public string DealerAddress { get; set; }
        public string DealerPhone { get; set; }
        public string WorkingHours { get; set; }
        public string WidgetAsSeparatePageUrl { get; set; }
        public string CrmProvider { get; set; }
        public string SiteProvider { get; set; }
        public string FeedFormat { get; set; }
        public string ZipCode { get; set; }
        public bool AvailableTestDriveFromHome { get; set; }
        public int MaxVehicleDeliveryDistance { get; set; }
        public string ImportRelativeFtpPath { get; set; }

        #endregion

        #region Relation

        public virtual User User { get; set; }
        public virtual ICollection<Beverage> Beverages { get; set; }
        public virtual ICollection<Expert> Experts { get; set; }
        public virtual ICollection<Route> Routes { get; set; }
        public virtual ICollection<Lead> Leads { get; set; }
        public virtual ICollection<Step> Steps { get; set; }
        public virtual WidgetTheme WidgetTheme { get; set; }

        #endregion

        public IList<string> EmailAdresses
        {
            get
            {
                if (!String.IsNullOrEmpty(NotificationContacts))
                {
                    string[] contactsArr = NotificationContacts.Split(';');
                    if (contactsArr.Any() && !String.IsNullOrEmpty(contactsArr[0]))
                    {
                        return contactsArr[0].Split(',');
                    }
                }

                return new List<string>(0);
            }
        }

        public IList<string> AdfEmailAdresses
        {
            get
            {
                if (!String.IsNullOrEmpty(NotificationContacts))
                {
                    string[] contactsArr = NotificationContacts.Split(';');
                    if (contactsArr.Any() && !String.IsNullOrEmpty(contactsArr[2]))
                    {
                        return contactsArr[2].Split(',');
                    }
                }

                return new List<string>(0);
            }
        }

        public IList<string> PhoneNumbers
        {
            get
            {
                if (!String.IsNullOrEmpty(NotificationContacts))
                {
                    string[] contactsArr = NotificationContacts.Split(';');
                    if (contactsArr.Any() && !String.IsNullOrEmpty(contactsArr[1]))
                    {
                        return contactsArr[1].Split(',');
                    }
                }

                return new List<string>(0);
            }
        }

        #region Ctors

        public Site()
            : base()
        {
            Beverages = new HashSet<Beverage>();
            Experts = new HashSet<Expert>();
            Routes = new HashSet<Route>();
            Leads = new HashSet<Lead>();
            Steps = new HashSet<Step>();
        }

        #endregion

        #region IValidable implementation

        public bool IsValid()
        {
            return InnerValidate(false);
        }
        public void Validate()
        {
            InnerValidate(true);
        }
        protected virtual bool InnerValidate(bool throwException)
        {
            return true;
        }

        #endregion

        #region IUpdatable implementation

        public void UpdateFrom(Site actualEntity)
        {
            UserId = actualEntity.UserId;
            BeautyId = actualEntity.BeautyId;
            Name = actualEntity.Name;
            Url = actualEntity.Url;
            NotificationContacts = actualEntity.NotificationContacts;
            ImageUrl = actualEntity.ImageUrl;
            DealerName = actualEntity.DealerName;
            DealerAddress = actualEntity.DealerAddress;
            DealerPhone = actualEntity.DealerPhone;
            WorkingHours = actualEntity.WorkingHours;
            WidgetAsSeparatePageUrl = actualEntity.WidgetAsSeparatePageUrl;
            ZipCode = actualEntity.ZipCode;
            AvailableTestDriveFromHome = actualEntity.AvailableTestDriveFromHome;
            MaxVehicleDeliveryDistance = actualEntity.MaxVehicleDeliveryDistance;
            ImportRelativeFtpPath = actualEntity.ImportRelativeFtpPath;
        }

        #endregion
    }
}
