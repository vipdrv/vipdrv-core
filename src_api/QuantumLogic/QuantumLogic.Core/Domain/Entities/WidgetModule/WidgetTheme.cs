namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class WidgetTheme : Entity<int>, IValidable, IUpdatableFrom<WidgetTheme>
    {
        #region Fields

        public int SiteId { get; set; }
        public string CssUrl { get; set; }
        public string ButtonImageUrl { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }

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

        public void UpdateFrom(WidgetTheme actualEntity)
        {
            SiteId = actualEntity.SiteId;
            CssUrl = actualEntity.CssUrl;
            ButtonImageUrl = actualEntity.ButtonImageUrl;
        }

        #endregion
    }
}
