namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class UserClaim
    {
        public int UserId { get; set; }
        public User User { get; set; }
        public string ClaimId { get; set; }
        public Claim Claim { get; set; }
    }
}
