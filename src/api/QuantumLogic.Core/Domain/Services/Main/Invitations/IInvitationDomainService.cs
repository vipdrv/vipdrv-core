using QuantumLogic.Core.Domain.Entities.MainModule;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Invitations
{
    public interface IInvitationDomainService : IEntityDomainService<Invitation, int>
    {
        Task<Invitation> UseInvitationAsync(string invitationCode);
    }
}
