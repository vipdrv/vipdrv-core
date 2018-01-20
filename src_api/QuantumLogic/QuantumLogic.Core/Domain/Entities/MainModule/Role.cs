using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class Role : Entity<int>
    {
        public string Name { get; set; }
        public bool CanBeUsedForInvitation { get; set; }
        public virtual ICollection<RoleClaim> RoleClaims { get; set; }
        public virtual ICollection<UserRole> UserRoles { get; set; }

        #region Ctors

        public Role()
            : base()
        {
            RoleClaims = new HashSet<RoleClaim>();
            UserRoles = new HashSet<UserRole>();
        }

        public Role(int id, string name)
            : this(id, name, new HashSet<RoleClaim>(), new HashSet<UserRole>())
        { }

        public Role(int id, string name, ICollection<RoleClaim> roleClaims, ICollection<UserRole> userRoles)
            : base(id)
        {
            Name = name;
            RoleClaims = roleClaims;
            UserRoles = userRoles;
        }

        #endregion

        public override string ToString()
        {
            return $"{Name}";
        }
    }
}
