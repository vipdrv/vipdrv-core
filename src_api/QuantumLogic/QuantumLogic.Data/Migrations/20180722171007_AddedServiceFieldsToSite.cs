using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedServiceFieldsToSite : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "FtpLogin",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "FtpPassword",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<bool>(
                name: "InjectButtonToSidebar",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "InjectButtonToSrp",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "InjectButtonToVdp",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "InjectWidgetToSaw",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<int>(
                name: "NewVehiclesCount",
                table: "Site",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<bool>(
                name: "SendEmailNotificationsToCustomer",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<int>(
                name: "UsedVehiclesCount",
                table: "Site",
                nullable: false,
                defaultValue: 0);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "FtpLogin",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "FtpPassword",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "InjectButtonToSidebar",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "InjectButtonToSrp",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "InjectButtonToVdp",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "InjectWidgetToSaw",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "NewVehiclesCount",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "SendEmailNotificationsToCustomer",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UsedVehiclesCount",
                table: "Site");
        }
    }
}
