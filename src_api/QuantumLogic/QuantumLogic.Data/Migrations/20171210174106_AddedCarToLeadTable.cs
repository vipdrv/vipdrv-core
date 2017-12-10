using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedCarToLeadTable : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AlterColumn<int>(
                name: "RouteId",
                table: "Lead",
                nullable: true,
                oldClrType: typeof(int));

            migrationBuilder.AlterColumn<int>(
                name: "ExpertId",
                table: "Lead",
                nullable: true,
                oldClrType: typeof(int));

            migrationBuilder.AddColumn<string>(
                name: "CarImageUrl",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CarTitle",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CarVin",
                table: "Lead",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CarImageUrl",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "CarTitle",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "CarVin",
                table: "Lead");

            migrationBuilder.AlterColumn<int>(
                name: "RouteId",
                table: "Lead",
                nullable: false,
                oldClrType: typeof(int),
                oldNullable: true);

            migrationBuilder.AlterColumn<int>(
                name: "ExpertId",
                table: "Lead",
                nullable: false,
                oldClrType: typeof(int),
                oldNullable: true);
        }
    }
}
