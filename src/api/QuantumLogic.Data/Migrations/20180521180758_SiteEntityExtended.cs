using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class SiteEntityExtended : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "BeverageStepOrder",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "ExpertStepOrder",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "RouteStepOrder",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UseBeverageStep",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UseExpertStep",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UseRouteStep",
                table: "Site");

            migrationBuilder.AddColumn<string>(
                name: "ZipCode",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<bool>(
                name: "AvailableTestDriveFromHome",
                table: "Site",
                nullable: false,
                defaultValue: false);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "ZipCode",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "AvailableTestDriveFromHome",
                table: "Site");

            migrationBuilder.AddColumn<bool>(
                name: "UseRouteStep",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<int>(
                name: "BeverageStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<int>(
                name: "ExpertStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<int>(
                name: "RouteStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<bool>(
                name: "UseBeverageStep",
                table: "Site",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "UseExpertStep",
                table: "Site",
                nullable: false,
                defaultValue: false);
        }
    }
}
