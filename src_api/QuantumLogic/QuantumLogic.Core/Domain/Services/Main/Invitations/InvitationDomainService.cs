using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Enums;
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
        #region Injected dependencies

        protected readonly IQLSession Session;
        protected readonly IRoleRepository RoleRepository;

        #endregion

        #region Ctors

        public InvitationDomainService(IInvitationRepository repository, IInvitationPolicy policy, IInvitationValidationService validationService, IQLSession session, IRoleRepository roleRepository)
            : base(repository, policy, validationService)
        {
            Session = session;
            RoleRepository = roleRepository;
        }

        #endregion

        public async Task<Invitation> UseInvitationAsync(string invitationCode)
        {
            Invitation entity = await Repository.FirstOrDefaultAsync(r => r.InvitationCode == invitationCode && !r.Used);
            if (entity == null)
            {
                throw new EntityNotFoundException();
            }
            ((IInvitationPolicy)Policy).CanUse(entity);
            entity.Used = true;
            entity.UsedTimeUtc = DateTime.UtcNow;
            return entity;
        }

        public override Task<Invitation> CreateAsync(Invitation entity)
        {
            entity.InvitationCode = Guid.NewGuid().ToString();
            entity.InvitatorId = Session.UserId;
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
            #region Load related role action

            Action<Invitation> loadRelatedRoleActionExpression = async (entity) =>
            {
                entity.Role = await RoleRepository.SingleAsync(r => r.Id == entity.RoleId);
            };
            ISet<DomainMethodNames> loadRelatedRoleMethodsToUse = new HashSet<DomainMethodNames>()
            {
                DomainMethodNames.Create,
                DomainMethodNames.Update
            };
            LoadEntityRelationAction<Invitation> loadRelatedRole = new LoadEntityRelationAction<Invitation>(loadRelatedRoleActionExpression, loadRelatedRoleMethodsToUse);

            #endregion

            return new List<LoadEntityRelationAction<Invitation>>()
            {
                loadRelatedRole
            };
        }
        protected override Expression<Func<Invitation, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Invitation, object>>>()
            {
                entity => entity.Role,
                entity => entity.Invitator
            }
            .ToArray();
        }
        protected override Expression<Func<Invitation, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Invitation, object>>>()
            {
                entity => entity.Role,
                entity => entity.Invitator
            }
            .ToArray();
        }
    }
}
