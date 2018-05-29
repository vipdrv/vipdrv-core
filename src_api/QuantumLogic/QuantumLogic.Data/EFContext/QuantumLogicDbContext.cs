using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Data.EFContext
{
    /// <summary>
    /// Is used as application's main data base context
    /// </summary>
    public class QuantumLogicDbContext : DbContext
    {
        #region Ctors

        public QuantumLogicDbContext()
            : base()
        { }

        public QuantumLogicDbContext(DbContextOptions options)
            : base(options)
        { }

        #endregion

        #region DbSets

        public virtual DbSet<User> Users { get; set; }
        public virtual DbSet<Role> Roles { get; set; }
        public virtual DbSet<Claim> Claims { get; set; }
        public virtual DbSet<Invitation> Invitations { get; set; }
        public virtual DbSet<Site> Sites { get; set; }
        public virtual DbSet<Lead> Leads { get; set; }
        public virtual DbSet<Beverage> Beverages { get; set; }
        public virtual DbSet<Expert> Experts { get; set; }
        public virtual DbSet<Route> Routes { get; set; }
        public virtual DbSet<WidgetTheme> WidgetThemes { get; set; }
        public virtual DbSet<Vehicle> Vehicles { get; set; }

        #endregion

        //// Uncomment to use EF migrations
        // protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        // {
        //     optionsBuilder.UseSqlServer(
        //         "Server=mssql-ukr.elrondsoft.com;Database=dev_testdrive;User Id=sa; Password=zmgPe89AnGFtPPb5;");
        //     base.OnConfiguring(optionsBuilder);
        // }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            modelBuilder.Entity<User>(entity =>
            {
                entity.ToTable("User");
                entity.HasKey(r => r.Id);
            });

            modelBuilder.Entity<UserClaim>(entity =>
            {
                entity.ToTable("UserClaim");
                entity.HasKey(r => new { r.UserId, r.ClaimId });
                entity
                    .HasOne(r => r.User)
                    .WithMany(r => r.UserClaims)
                    .HasForeignKey(r => r.UserId)
                    .OnDelete(DeleteBehavior.Cascade);
                entity
                    .HasOne(r => r.Claim)
                    .WithMany()
                    .HasForeignKey(r => r.ClaimId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<UserRole>(entity =>
            {
                entity.ToTable("UserRole");
                entity.HasKey(r => new { r.UserId, r.RoleId });
                entity
                    .HasOne(r => r.User)
                    .WithMany(r => r.UserRoles)
                    .HasForeignKey(r => r.UserId)
                    .OnDelete(DeleteBehavior.Cascade);
                entity
                    .HasOne(r => r.Role)
                    .WithMany(r => r.UserRoles)
                    .HasForeignKey(r => r.RoleId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Role>(entity =>
            {
                entity.ToTable("Role");
                entity.HasKey(r => r.Id);
            });

            modelBuilder.Entity<RoleClaim>(entity =>
            {
                entity.ToTable("RoleClaim");
                entity.HasKey(r => new { r.RoleId, r.ClaimId });
                entity
                    .HasOne(r => r.Role)
                    .WithMany(r => r.RoleClaims)
                    .HasForeignKey(r => r.RoleId)
                    .OnDelete(DeleteBehavior.Cascade);
                entity
                    .HasOne(r => r.Claim)
                    .WithMany()
                    .HasForeignKey(r => r.ClaimId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Claim>(entity =>
            {
                entity.ToTable("Claim");
                entity.HasKey(r => r.Id);
            });

            modelBuilder.Entity<ExternalLogin>(entity =>
            {
                entity.ToTable("ExternalLogin");
                entity.HasKey(r => r.Id);
                entity
                    .HasOne(e => e.User)
                    .WithMany(b => b.ExternalLogins)
                    .HasForeignKey(r => r.UserId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Invitation>(entity =>
            {
                entity.ToTable("Invitation");
                entity.HasKey(r => r.Id);
                entity
                    .HasOne(e => e.Invitator)
                    .WithMany(b => b.CreatedInvitations)
                    .HasForeignKey(r => r.InvitatorId)
                    .OnDelete(DeleteBehavior.Cascade);
                entity
                    .HasOne(e => e.Role)
                    .WithMany()
                    .HasForeignKey(r => r.RoleId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Site>(entity =>
            {
                entity.ToTable("Site");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.User)
                    .WithMany(b => b.Sites)
                    .HasForeignKey(r => r.UserId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Beverage>(entity =>
            {
                entity.ToTable("Beverage");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Beverages)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Expert>(entity =>
            {
                entity.ToTable("Expert");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Experts)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Route>(entity =>
            {
                entity.ToTable("Route");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Routes)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<WidgetTheme>(entity =>
            {
                entity.ToTable("WidgetTheme");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(p => p.Site)
                    .WithOne(i => i.WidgetTheme)
                    .HasForeignKey<WidgetTheme>(b => b.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Lead>(entity =>
            {
                entity.ToTable("Lead");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Beverage)
                    .WithMany()
                    .HasForeignKey(r => r.BeverageId)
                    .OnDelete(DeleteBehavior.Restrict);
                entity
                    .HasOne(e => e.Expert)
                    .WithMany()
                    .HasForeignKey(r => r.ExpertId)
                    .OnDelete(DeleteBehavior.Restrict);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Leads)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Restrict);
                entity
                    .HasOne(e => e.Route)
                    .WithMany()
                    .HasForeignKey(r => r.RouteId)
                    .OnDelete(DeleteBehavior.Restrict);
            });
            
            modelBuilder.Entity<Step>(entity =>
            {
                entity.ToTable("Step");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Steps)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Vehicle>(entity =>
            {
                entity.ToTable("Vehicle");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany()
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });
        }
    }
}
