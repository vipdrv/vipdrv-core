using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Migrations
{
    [DbContext(typeof(QuantumLogicDbContext))]
    [Migration("20171211021552_AddedBookingDateTime")]
    partial class AddedBookingDateTime
    {
        protected override void BuildTargetModel(ModelBuilder modelBuilder)
        {
            modelBuilder
                .HasAnnotation("ProductVersion", "1.1.2")
                .HasAnnotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn);

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.Claim", b =>
                {
                    b.Property<string>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Name");

                    b.HasKey("Id");

                    b.ToTable("Claim");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.ExternalLogin", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("LoginProvider");

                    b.Property<string>("ProviderDisplayName");

                    b.Property<string>("ProviderKey");

                    b.Property<int>("UserId");

                    b.HasKey("Id");

                    b.HasIndex("UserId");

                    b.ToTable("ExternalLogin");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.Invitation", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<int>("AvailableSitesCount");

                    b.Property<DateTime>("CreatedTimeUtc");

                    b.Property<string>("Email");

                    b.Property<string>("InvitationCode");

                    b.Property<int>("InvitatorId");

                    b.Property<string>("PhoneNumber");

                    b.Property<int>("RoleId");

                    b.Property<bool>("Used");

                    b.Property<DateTime?>("UsedTimeUtc");

                    b.HasKey("Id");

                    b.HasIndex("InvitatorId");

                    b.HasIndex("RoleId");

                    b.ToTable("Invitation");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.Role", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Name");

                    b.HasKey("Id");

                    b.ToTable("Role");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.RoleClaim", b =>
                {
                    b.Property<int>("RoleId");

                    b.Property<string>("ClaimId");

                    b.HasKey("RoleId", "ClaimId");

                    b.HasIndex("ClaimId");

                    b.ToTable("RoleClaim");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.User", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("AvatarUrl");

                    b.Property<string>("Email");

                    b.Property<string>("FirstName");

                    b.Property<int>("MaxSitesCount");

                    b.Property<string>("PasswordHash");

                    b.Property<string>("PhoneNumber");

                    b.Property<string>("SecondName");

                    b.Property<string>("Username");

                    b.HasKey("Id");

                    b.ToTable("User");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.UserClaim", b =>
                {
                    b.Property<int>("UserId");

                    b.Property<string>("ClaimId");

                    b.HasKey("UserId", "ClaimId");

                    b.HasIndex("ClaimId");

                    b.ToTable("UserClaim");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.UserRole", b =>
                {
                    b.Property<int>("UserId");

                    b.Property<int>("RoleId");

                    b.HasKey("UserId", "RoleId");

                    b.HasIndex("RoleId");

                    b.ToTable("UserRole");
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

                    b.Property<DateTime>("BookingDateTimeUtc");

                    b.Property<string>("CarImageUrl");

                    b.Property<string>("CarTitle");

                    b.Property<string>("CarVin");

                    b.Property<int?>("ExpertId");

                    b.Property<string>("FirstName");

                    b.Property<bool>("IsNew");

                    b.Property<bool>("IsReachedByManager");

                    b.Property<DateTime>("RecievedUtc");

                    b.Property<int?>("RouteId");

                    b.Property<string>("SecondName");

                    b.Property<int>("SiteId");

                    b.Property<string>("UserEmail");

                    b.Property<string>("UserPhone");

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

                    b.Property<string>("NotificationContacts");

                    b.Property<string>("ImageUrl");

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

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.ExternalLogin", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.User", "User")
                        .WithMany("ExternalLogins")
                        .HasForeignKey("UserId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.Invitation", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.User", "Invitator")
                        .WithMany("CreatedInvitations")
                        .HasForeignKey("InvitatorId")
                        .OnDelete(DeleteBehavior.Cascade);

                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.Role", "Role")
                        .WithMany()
                        .HasForeignKey("RoleId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.RoleClaim", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.Claim", "Claim")
                        .WithMany()
                        .HasForeignKey("ClaimId")
                        .OnDelete(DeleteBehavior.Cascade);

                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.Role", "Role")
                        .WithMany("RoleClaims")
                        .HasForeignKey("RoleId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.UserClaim", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.Claim", "Claim")
                        .WithMany()
                        .HasForeignKey("ClaimId")
                        .OnDelete(DeleteBehavior.Cascade);

                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.User", "User")
                        .WithMany("UserClaims")
                        .HasForeignKey("UserId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.UserRole", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.Role", "Role")
                        .WithMany("UserRoles")
                        .HasForeignKey("RoleId")
                        .OnDelete(DeleteBehavior.Cascade);

                    b.HasOne("QuantumLogic.Core.Domain.Entities.MainModule.User", "User")
                        .WithMany("UserRoles")
                        .HasForeignKey("UserId")
                        .OnDelete(DeleteBehavior.Cascade);
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
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Beverage", "Beverage")
                        .WithMany()
                        .HasForeignKey("BeverageId");

                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Expert", "Expert")
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
