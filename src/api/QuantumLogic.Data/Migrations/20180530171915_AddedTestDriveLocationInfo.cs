using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedTestDriveLocationInfo : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "LocationAddress",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "LocationType",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddColumn<bool>(
                name: "ShowLocationInfo",
                table: "Lead",
                nullable: false,
                defaultValue: false);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "LocationAddress",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "LocationType",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "ShowLocationInfo",
                table: "Lead");
        }
    }
}
