using Microsoft.Extensions.Options;

namespace QuantumLogic.xUnitTests.Mocks.Configurations.Options
{
    public class MockOptions<TOptions> : IOptions<TOptions>
        where TOptions : class, new()
    {
        public TOptions Value { get; set; }

        public MockOptions(TOptions value)
        {
            Value = value;
        }
    }
}
