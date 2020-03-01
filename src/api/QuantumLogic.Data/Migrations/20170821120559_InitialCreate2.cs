using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;
using Microsoft.EntityFrameworkCore.Metadata;

namespace QuantumLogic.Data.Migrations
{
    public partial class InitialCreate2 : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Experts_Site_SiteId",
                table: "Experts");

            migrationBuilder.DropPrimaryKey(
                name: "PK_Experts",
                table: "Experts");

            migrationBuilder.RenameTable(
                name: "Experts",
                newName: "Expert");

            migrationBuilder.RenameIndex(
                name: "IX_Experts_SiteId",
                table: "Expert",
                newName: "IX_Expert_SiteId");

            migrationBuilder.AddPrimaryKey(
                name: "PK_Expert",
                table: "Expert",
                column: "Id");

            migrationBuilder.CreateTable(
                name: "Lead",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn),
                    BeverageId = table.Column<int>(nullable: false),
                    ExpertId = table.Column<int>(nullable: false),
                    Recieved = table.Column<DateTime>(nullable: false),
                    RouteId = table.Column<int>(nullable: false),
                    SiteId = table.Column<int>(nullable: false),
                    UserEmail = table.Column<string>(nullable: true),
                    UserName = table.Column<int>(nullable: false),
                    UserPhone = table.Column<string>(nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Lead", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Lead_Site_SiteId",
                        column: x => x.SiteId,
                        principalTable: "Site",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Route",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn),
                    Description = table.Column<string>(nullable: true),
                    IsActive = table.Column<bool>(nullable: false),
                    Name = table.Column<string>(nullable: true),
                    Order = table.Column<int>(nullable: false),
                    PhotoUrl = table.Column<string>(nullable: true),
                    SiteId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Route", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Route_Site_SiteId",
                        column: x => x.SiteId,
                        principalTable: "Site",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "WidgetTheme",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn),
                    ButtonImageUrl = table.Column<string>(nullable: true),
                    CssUrl = table.Column<string>(nullable: true),
                    SiteId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_WidgetTheme", x => x.Id);
                    table.ForeignKey(
                        name: "FK_WidgetTheme_Site_SiteId",
                        column: x => x.SiteId,
                        principalTable: "Site",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_Lead_SiteId",
                table: "Lead",
                column: "SiteId");

            migrationBuilder.CreateIndex(
                name: "IX_Route_SiteId",
                table: "Route",
                column: "SiteId");

            migrationBuilder.CreateIndex(
                name: "IX_WidgetTheme_SiteId",
                table: "WidgetTheme",
                column: "SiteId",
                unique: true);

            migrationBuilder.AddForeignKey(
                name: "FK_Expert_Site_SiteId",
                table: "Expert",
                column: "SiteId",
                principalTable: "Site",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Expert_Site_SiteId",
                table: "Expert");

            migrationBuilder.DropTable(
                name: "Lead");

            migrationBuilder.DropTable(
                name: "Route");

            migrationBuilder.DropTable(
                name: "WidgetTheme");

            migrationBuilder.DropPrimaryKey(
                name: "PK_Expert",
                table: "Expert");

            migrationBuilder.RenameTable(
                name: "Expert",
                newName: "Experts");

            migrationBuilder.RenameIndex(
                name: "IX_Expert_SiteId",
                table: "Experts",
                newName: "IX_Experts_SiteId");

            migrationBuilder.AddPrimaryKey(
                name: "PK_Experts",
                table: "Experts",
                column: "Id");

            migrationBuilder.AddForeignKey(
                name: "FK_Experts_Site_SiteId",
                table: "Experts",
                column: "SiteId",
                principalTable: "Site",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }
    }
}
