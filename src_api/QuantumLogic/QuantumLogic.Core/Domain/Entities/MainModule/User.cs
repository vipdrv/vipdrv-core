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

        #endregion

        #region Ctors

        public User()
            : base()
        {
            Sites = new HashSet<Site>();
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

        public void UpdateFrom(User actualEntity)
        {
            Email = actualEntity.Email;
            MaxSitesCount = actualEntity.MaxSitesCount;
        }

        #endregion
    }
}
