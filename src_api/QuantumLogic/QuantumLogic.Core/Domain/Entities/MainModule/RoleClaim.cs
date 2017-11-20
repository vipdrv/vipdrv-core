namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class RoleClaim
    {
        public int RoleId { get; set; }
        public Role Role { get; set; }
        public string ClaimId { get; set; }
        public Claim Claim { get; set; }
    }
}
