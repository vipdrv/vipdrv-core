namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class ExternalLogin : Entity<int>
    {
        public int UserId { get; set; }
        public User User { get; set; }
        public string LoginProvider { get; set; }
        public string ProviderKey { get; set; }
        public string ProviderDisplayName { get; set; }

        #region Ctors

        public ExternalLogin()
            : base()
        { }

        public ExternalLogin(int id, int userId, User user, string loginProvider, string providerKey, string providerDisplayName)
            : base(id)
        {
            UserId = userId;
            User = user;
            LoginProvider = loginProvider;
            ProviderKey = providerKey;
            ProviderDisplayName = providerDisplayName;
        }

        #endregion
    }
}
