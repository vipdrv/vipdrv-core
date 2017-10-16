using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Exceptions.NotFound;
using QuantumLogic.Core.Exceptions.NotSupported;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Invitations
{
    public class InvitationDomainService : EntityDomainService<Invitation, int>, IInvitationDomainService
    {
        #region Ctors

        public InvitationDomainService(IInvitationRepository repository, IInvitationPolicy policy, IInvitationValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        public async Task UseInvitationAsync(string invitationCode)
        {
            Invitation entity = await Repository.FirstOrDefaultAsync(r => r.InvitationCode == invitationCode && r.Used);
            if (entity == null)
            {
                throw new EntityNotFoundException();
            }
            ((IInvitationPolicy)Policy).CanUse(entity);
            entity.Used = true;
            entity.UsedTimeUtc = DateTime.UtcNow;
        }

        public override Task<Invitation> CreateAsync(Invitation entity)
        {
            entity.InvitationCode = Guid.NewGuid().ToString();
            entity.Used = false;
            entity.CreatedTimeUtc = DateTime.UtcNow;
            return base.CreateAsync(entity);
        }
        public override Task<Invitation> UpdateAsync(Invitation entity)
        {
            throw new OperationIsNotSupportedException();
        }

        protected override Task CascadeDeleteActionAsync(Invitation entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Invitation>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Invitation>>();
        }
        protected override Expression<Func<Invitation, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new Expression<Func<Invitation, object>>[0];
        }
        protected override Expression<Func<Invitation, object>>[] GetRetrieveEntityIncludes()
        {
            return new Expression<Func<Invitation, object>>[0];
        }
    }
}
