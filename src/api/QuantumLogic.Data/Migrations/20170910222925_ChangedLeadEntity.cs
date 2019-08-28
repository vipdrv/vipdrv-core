using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class ChangedLeadEntity : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Route_BeverageId",
                table: "Lead");

            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Route_ExpertId",
                table: "Lead");

            migrationBuilder.RenameColumn(
                name: "Username",
                table: "Lead",
                newName: "SecondName");

            migrationBuilder.AddColumn<string>(
                name: "FirstName",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Beverage_BeverageId",
                table: "Lead",
                column: "BeverageId",
                principalTable: "Beverage",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Expert_ExpertId",
                table: "Lead",
                column: "ExpertId",
                principalTable: "Expert",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Beverage_BeverageId",
                table: "Lead");

            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Expert_ExpertId",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "FirstName",
                table: "Lead");

            migrationBuilder.RenameColumn(
                name: "SecondName",
                table: "Lead",
                newName: "Username");

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Route_BeverageId",
                table: "Lead",
                column: "BeverageId",
                principalTable: "Route",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Route_ExpertId",
                table: "Lead",
                column: "ExpertId",
                principalTable: "Route",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }
    }
}
