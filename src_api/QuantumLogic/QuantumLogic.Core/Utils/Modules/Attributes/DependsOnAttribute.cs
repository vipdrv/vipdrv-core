using System;

namespace QuantumLogic.Core.Utils.Modules.Attributes
{
    [AttributeUsage(AttributeTargets.Class)]
    public class DependsOnAttribute : Attribute
    {
        public Type[] DependsOnModuleTypes { get; private set; }
        public DependsOnAttribute(params Type[] dependsOnModuleTypes)
        {
            DependsOnModuleTypes = dependsOnModuleTypes;
        }
    }
}
