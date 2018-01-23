using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class User : Entity<int>, IValidable, IUpdatableFrom<User>
    {
        #region Fields

        public string Username { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public string PasswordHash { get; set; }

        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string AvatarUrl { get; set; }

        public int MaxSitesCount { get; set; }

        #endregion

        #region Relations

        public virtual ICollection<ExternalLogin> ExternalLogins { get; set; }
        public virtual ICollection<UserRole> UserRoles { get; set; }
        public virtual ICollection<UserClaim> UserClaims { get; set; }

        public virtual ICollection<Site> Sites { get; set; }
        public virtual ICollection<Invitation> CreatedInvitations { get; set; }

        #endregion
        
        public virtual string FullName
        {
            get
            {
                return $"{FirstName} {SecondName}";
            }
        }

        #region Ctors

        public User()
            : base()
        {
            ExternalLogins = new HashSet<ExternalLogin>();
            UserRoles = new HashSet<UserRole>();
            UserClaims = new HashSet<UserClaim>();
            Sites = new HashSet<Site>();
            CreatedInvitations = new HashSet<Invitation>();
        }

        #endregion

        public override string ToString()
        {
            return $"{FirstName} ({Username}) {SecondName}";
        }

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

        public void UpdateFrom(User actualEntity)
        {
            FirstName = actualEntity.FirstName;
            SecondName = actualEntity.SecondName;
            Email = actualEntity.Email;
            PhoneNumber = actualEntity.PhoneNumber;
            MaxSitesCount = actualEntity.MaxSitesCount;
            AvatarUrl = actualEntity.AvatarUrl;
            UserRoles = actualEntity.UserRoles;
            // UserClaims = actualEntity.UserClaims;
        }

        #endregion
    }
}
