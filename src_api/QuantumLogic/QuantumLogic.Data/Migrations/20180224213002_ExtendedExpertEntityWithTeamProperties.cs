using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class ExtendedExpertEntityWithTeamProperties : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<bool>(
                name: "IsPartOfTeamCPO",
                table: "Expert",
                nullable: false,
                defaultValue: true);

            migrationBuilder.AddColumn<bool>(
                name: "IsPartOfTeamNewCars",
                table: "Expert",
                nullable: false,
                defaultValue: true);

            migrationBuilder.AddColumn<bool>(
                name: "IsPartOfTeamUsedCars",
                table: "Expert",
                nullable: false,
                defaultValue: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "IsPartOfTeamCPO",
                table: "Expert");

            migrationBuilder.DropColumn(
                name: "IsPartOfTeamNewCars",
                table: "Expert");

            migrationBuilder.DropColumn(
                name: "IsPartOfTeamUsedCars",
                table: "Expert");
        }
    }
}
