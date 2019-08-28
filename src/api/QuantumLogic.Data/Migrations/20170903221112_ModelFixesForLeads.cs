using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class ModelFixesForLeads : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Site_SiteId",
                table: "Lead");

            migrationBuilder.RenameColumn(
                name: "UserName",
                table: "Lead",
                newName: "Username");

            migrationBuilder.RenameColumn(
                name: "Recieved",
                table: "Lead",
                newName: "RecievedUtc");

            migrationBuilder.AlterColumn<string>(
                name: "Username",
                table: "Lead",
                nullable: true,
                oldClrType: typeof(int));

            migrationBuilder.AlterColumn<int>(
                name: "BeverageId",
                table: "Lead",
                nullable: true,
                oldClrType: typeof(int));

            migrationBuilder.CreateIndex(
                name: "IX_Lead_BeverageId",
                table: "Lead",
                column: "BeverageId");

            migrationBuilder.CreateIndex(
                name: "IX_Lead_ExpertId",
                table: "Lead",
                column: "ExpertId");

            migrationBuilder.CreateIndex(
                name: "IX_Lead_RouteId",
                table: "Lead",
                column: "RouteId");

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

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Route_RouteId",
                table: "Lead",
                column: "RouteId",
                principalTable: "Route",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Site_SiteId",
                table: "Lead",
                column: "SiteId",
                principalTable: "Site",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Route_BeverageId",
                table: "Lead");

            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Route_ExpertId",
                table: "Lead");

            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Route_RouteId",
                table: "Lead");

            migrationBuilder.DropForeignKey(
                name: "FK_Lead_Site_SiteId",
                table: "Lead");

            migrationBuilder.DropIndex(
                name: "IX_Lead_BeverageId",
                table: "Lead");

            migrationBuilder.DropIndex(
                name: "IX_Lead_ExpertId",
                table: "Lead");

            migrationBuilder.DropIndex(
                name: "IX_Lead_RouteId",
                table: "Lead");

            migrationBuilder.RenameColumn(
                name: "Username",
                table: "Lead",
                newName: "UserName");

            migrationBuilder.RenameColumn(
                name: "RecievedUtc",
                table: "Lead",
                newName: "Recieved");

            migrationBuilder.AlterColumn<int>(
                name: "UserName",
                table: "Lead",
                nullable: false,
                oldClrType: typeof(string),
                oldNullable: true);

            migrationBuilder.AlterColumn<int>(
                name: "BeverageId",
                table: "Lead",
                nullable: false,
                oldClrType: typeof(int),
                oldNullable: true);

            migrationBuilder.AddForeignKey(
                name: "FK_Lead_Site_SiteId",
                table: "Lead",
                column: "SiteId",
                principalTable: "Site",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }
    }
}
