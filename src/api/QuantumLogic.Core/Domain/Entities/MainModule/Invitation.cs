using QuantumLogic.Core.Exceptions.NotSupported;
using System;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class Invitation : Entity<int>, IValidable, IUpdatableFrom<Invitation>
    {
        public string InvitationCode { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public DateTime CreatedTimeUtc { get; set; }
        public bool Used { get; set; }
        public DateTime? UsedTimeUtc { get; set; }
        public int AvailableSitesCount { get; set; }

        public int? InvitatorId { get; set; }
        public User Invitator { get; set; }
        public int RoleId { get; set; }
        public Role Role { get; set; }

        #region Ctors

        public Invitation()
            : base()
        { }

        public Invitation(int id, string invitationCode, string email, string phoneNumber, int availableSitesCount, int invitatorId, int roleId)
            : base(id)
        {
            InvitationCode = invitationCode;
            Email = email;
            PhoneNumber = phoneNumber;
            AvailableSitesCount = availableSitesCount;
            InvitatorId = invitatorId;
            RoleId = roleId;
            Used = false;
            CreatedTimeUtc = DateTime.UtcNow;
            UsedTimeUtc = null;
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

        public void UpdateFrom(Invitation actualEntity)
        {
            throw new OperationIsNotSupportedException();
        }

        #endregion
    }
}
