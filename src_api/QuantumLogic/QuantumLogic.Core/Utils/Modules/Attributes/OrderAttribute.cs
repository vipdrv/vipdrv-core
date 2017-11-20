using System;

namespace QuantumLogic.Core.Utils.Modules.Attributes
{
    [AttributeUsage(AttributeTargets.Class)]
    public class OrderAttribute : Attribute
    {
        public int Order { get; private set; }

        public OrderAttribute(int order = 0)
        {
            Order = order;
        }
    }
}
