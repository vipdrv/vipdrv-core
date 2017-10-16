using QuantumLogic.Core.Domain.Entities.MainModule;
using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.Invitations
{
    public class InvitationDto : EntityDto<Invitation, int>
    {
        public string InvitationCode { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }

        public DateTime CreatedTimeUtc { get; set; }
        public bool Used { get; set; }
        public DateTime? UsedTimeUtc { get; set; }
        public int AvailableSitesCount { get; set; }

        public int InvitatorId { get; set; }
        public string Invitator { get; set; }
        public int RoleId { get; set; }
        public string Role { get; set; }

        public override void MapFromEntity(Invitation entity)
        {
            base.MapFromEntity(entity);
            InvitationCode = entity.InvitationCode;
            Email = entity.Email;
            PhoneNumber = entity.PhoneNumber;
            CreatedTimeUtc = entity.CreatedTimeUtc;
            Used = entity.Used;
            UsedTimeUtc = entity.UsedTimeUtc;
            AvailableSitesCount = entity.AvailableSitesCount;
            UsedTimeUtc = entity.UsedTimeUtc;
            InvitatorId = entity.InvitatorId;
            Invitator = entity.Invitator.ToString();
            RoleId = entity.RoleId;
            Role = entity.Role.ToString();
        }
        public override Invitation MapToEntity()
        {
            Invitation entity = base.MapToEntity();
            entity.Email = Email;
            entity.PhoneNumber = PhoneNumber;
            entity.AvailableSitesCount = AvailableSitesCount;
            entity.RoleId = RoleId;
            return entity;
        }

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }
    }
}
