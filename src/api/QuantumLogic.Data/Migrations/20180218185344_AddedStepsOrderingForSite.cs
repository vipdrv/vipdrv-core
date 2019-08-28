using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedStepsOrderingForSite : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<int>(
                name: "BeverageStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 1);

            migrationBuilder.AddColumn<int>(
                name: "ExpertStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 2);

            migrationBuilder.AddColumn<int>(
                name: "RouteStepOrder",
                table: "Site",
                nullable: false,
                defaultValue: 3);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
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
        }
    }
}
