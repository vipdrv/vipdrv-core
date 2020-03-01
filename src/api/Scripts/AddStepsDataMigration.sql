USE [database_name]
GO

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'ScheduleStep' as Descriptor, 1 as IsActive, 'ScheduleStep' as [Name], 1 as [Order], Id as SiteId
FROM [dbo].[Site]

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'ExpertStep' as Descriptor, [UseExpertStep] as IsActive, 'ExpertStep' as [Name], ExpertStepOrder + 1 as [Order], Id as SiteId
FROM [dbo].[Site]

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'BeverageStep' as Descriptor, UseBeverageStep as IsActive, 'BeverageStep' as [Name], BeverageStepOrder + 1 as [Order], Id as SiteId
FROM [dbo].[Site]

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'RouteStep' as Descriptor, [UseRouteStep] as IsActive, 'RouteStep' as [Name], RouteStepOrder + 1 as [Order], Id as SiteId
FROM [dbo].[Site]

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'MusicStep' as Descriptor, 0 as IsActive, 'MusicStep' as [Name], 5 as [Order], Id as SiteId
FROM [dbo].[Site]

INSERT INTO [dbo].[Step] ([Descriptor] ,[IsActive] ,[Name] ,[Order] ,[SiteId])
SELECT 'TestDriveFromHomeStep' as Descriptor, 0 as IsActive, 'TestDriveFromHomeStep' as [Name], 6 as [Order], Id as SiteId
FROM [dbo].[Site]

GO