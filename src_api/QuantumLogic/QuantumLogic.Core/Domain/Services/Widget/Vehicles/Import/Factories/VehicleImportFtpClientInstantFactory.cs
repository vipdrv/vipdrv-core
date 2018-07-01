using FluentFTP;
using Microsoft.Extensions.Options;
using QuantumLogic.Core.Configurations;
using QuantumLogic.Core.Shared.Factories;
using System.Net;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories
{
    public class VehicleImportFtpClientInstantFactory : IInstantFactory<IFtpClient>
    {
        #region Injected dependencies

        protected readonly VehicleImportFtpServerConfiguration FtpConfiguration;

        #endregion

        #region Ctors

        public VehicleImportFtpClientInstantFactory(IOptions<VehicleImportFtpServerConfiguration> ftpConfigurationOption)
        {
            FtpConfiguration = ftpConfigurationOption.Value;
        }

        #endregion

        public IFtpClient Create()
        {
            return new FtpClient(FtpConfiguration.Host)
            {
                Credentials = new NetworkCredential(FtpConfiguration.Username, FtpConfiguration.Password)
            };
        }
    }
}
