using System;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Step : Entity<int>, IPassivable, IOrderable,IValidable, IUpdatableFrom<Step>
    {
        public static string DescriptorStepExpert = "ExpertStep";
        public static string DescriptorStepBeverage = "BeverageStep";
        public static string DescriptorStepRoute = "RouteStep";
        public static string DescriptorStepSchedule = "ScheduleStep";
        public static string DescriptorStepMusic = "MusicStep";

        #region Fields

        public int SiteId { get; set; }
        public string Descriptor { get; set; }
        public string Name { get; set; }

        public int Order { get; set; }
        public bool IsActive { get; set; }

        #endregion

        #region Relation

        public virtual Site Site { get; set; }

        #endregion

        #region Ctors

        public Step()
            : base()
        { }

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

        public void UpdateFrom(Step actualEntity)
        {
            throw new NotImplementedException();
        }

        #endregion
    }
}
