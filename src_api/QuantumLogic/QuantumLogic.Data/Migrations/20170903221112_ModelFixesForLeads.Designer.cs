using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Migrations
{
    [DbContext(typeof(QuantumLogicDbContext))]
    [Migration("20170903221112_ModelFixesForLeads")]
    partial class ModelFixesForLeads
    {
        protected override void BuildTargetModel(ModelBuilder modelBuilder)
        {
            modelBuilder
                .HasAnnotation("ProductVersion", "1.1.2")
                .HasAnnotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn);

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.User", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Email");

                    b.Property<int>("MaxSitesCount");

                    b.Property<string>("Password");

                    b.HasKey("Id");

                    b.ToTable("User");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Beverage", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Description");

                    b.Property<bool>("IsActive");

                    b.Property<string>("Name");

                    b.Property<int>("Order");

                    b.Property<string>("PhotoUrl");

                    b.Property<int>("SiteId");

                    b.HasKey("Id");

                    b.HasIndex("SiteId");

                    b.ToTable("Beverage");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Expert", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Description");

                    b.Property<string>("FacebookUrl");

                    b.Property<bool>("IsActive");

                    b.Property<string>("LinkedinUrl");

                    b.Property<string>("Name");

                    b.Property<int>("Order");

                    b.Property<string>("PhotoUrl");

                    b.Property<int>("SiteId");

                    b.Property<string>("WorkingHours");

                    b.HasKey("Id");

                    b.HasIndex("SiteId");

                    b.ToTable("Expert");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Lead", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<int?>("BeverageId");

                    b.Property<int>("ExpertId");

                    b.Property<DateTime>("RecievedUtc");

                    b.Property<int>("RouteId");

                    b.Property<int>("SiteId");

                    b.Property<string>("UserEmail");

                    b.Property<string>("UserPhone");

                    b.Property<string>("Username");

                    b.HasKey("Id");

                    b.HasIndex("BeverageId");

                    b.HasIndex("ExpertId");

                    b.HasIndex("RouteId");

                    b.HasIndex("SiteId");

                    b.ToTable("Lead");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Route", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Description");

                    b.Property<bool>("IsActive");

                    b.Property<string>("Name");

                    b.Property<int>("Order");

                    b.Property<string>("PhotoUrl");

                    b.Property<int>("SiteId");

                    b.HasKey("Id");

                    b.HasIndex("SiteId");

                    b.ToTable("Route");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("BeautyId");

                    b.Property<string>("Contacts");

                    b.Property<string>("Name");

                    b.Property<string>("Url");

                    b.Property<int>("UserId");

                    b.HasKey("Id");

                    b.HasIndex("UserId");

                    b.ToTable("Site");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.WidgetTheme", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("ButtonImageUrl");

                    b.Property<string>("CssUrl");

                    b.Property<int>("SiteId");

                    b.HasKey("Id");

                    b.HasIndex("SiteId")
                        .IsUnique();

                    b.ToTable("WidgetTheme");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Beverage", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany("Beverages")
                        .HasForeignKey("SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Expert", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany("Experts")
                        .HasForeignKey("SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Lead", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Route", "Beverage")
                        .WithMany()
                        .HasForeignKey("BeverageId");

                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Route", "Expert")
                        .WithMany()
                        .HasForeignKey("ExpertId");

                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Route", "Route")
                        .WithMany()
                        .HasForeignKey("RouteId");

                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany("Leads")
                        .HasForeignKey("SiteId");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Route", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany("Routes")
                        .HasForeignKey("SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.User", "User")
                        .WithMany("Sites")
                        .HasForeignKey("UserId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.WidgetTheme", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithOne("WidgetTheme")
                        .HasForeignKey("QuantumLogic.Core.Domain.Entities.WidgetModule.WidgetTheme", "SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });
        }
    }
}
