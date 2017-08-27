using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class User : Entity<int>, IValidable, IUpdatableFrom<User>
    {
        #region Fields

        public string Email { get; set; }
        public string Password { get; set; }
        public int MaxSitesCount { get; set; }

        #endregion

        #region Relations

        public virtual ICollection<Site> Sites { get; set; }

        #endregion

        #region Ctors

        public User()
            : base()
        {
            Sites = new HashSet<Site>();
        }

        public User(int id, string email, string password, int maxSitesCount)
            : this()
        {
            Id = id;
            Email = email;
            Password = password;
            MaxSitesCount = maxSitesCount;
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
            Password = actualEntity.Password;
            MaxSitesCount = actualEntity.MaxSitesCount;
        }

        #endregion
    }
}
