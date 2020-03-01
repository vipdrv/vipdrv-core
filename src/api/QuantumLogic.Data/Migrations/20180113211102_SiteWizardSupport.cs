using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class SiteWizardSupport : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<bool>(
                name: "UseBeverageStep",
                table: "Site",
                nullable: false,
                defaultValue: true);

            migrationBuilder.AddColumn<bool>(
                name: "UseExpertStep",
                table: "Site",
                nullable: false,
                defaultValue: true);

            migrationBuilder.AddColumn<bool>(
                name: "UseRouteStep",
                table: "Site",
                nullable: false,
                defaultValue: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "UseBeverageStep",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UseExpertStep",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "UseRouteStep",
                table: "Site");
        }
    }
}
