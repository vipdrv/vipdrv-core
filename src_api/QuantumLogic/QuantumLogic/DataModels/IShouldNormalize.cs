namespace QuantumLogic.WebApi.DataModels
{
    public interface IShouldNormalize
    {
        void NormalizeAsRequest();
        void NormalizeAsResponse();
    }
}
