USE [OrderEntry]
GO

/****** Object:  Table [dbo].[BCS_PelangganCabang]    Script Date: 04/07/2015 09:57:54 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BCS_PelangganCabang]') AND type in (N'U'))
DROP TABLE [dbo].[BCS_PelangganCabang]
GO

USE [OrderEntry]
GO

/****** Object:  Table [dbo].[BCS_PelangganCabang]    Script Date: 04/07/2015 09:57:55 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCS_PelangganCabang](
	[KodeCabang] [varchar](12) NOT NULL,
	[KodeDivisi] [varchar](12) NOT NULL,
	[Perusahaan] [varchar](50) NOT NULL,
	[Pemilik] [varchar](40) NOT NULL,
	[Penghubung] [varchar](40) NOT NULL,
	[Segment] [varchar](6) NOT NULL,
	[SubSegment] [varchar](30) NULL,
	[Alamat] [varchar](75) NOT NULL,
	[Kota] [varchar](20) NOT NULL,
	[KodePos] [varchar](20) NOT NULL,
	[Kecamatan] [varchar](20) NOT NULL,
	[Kelurahan] [varchar](20) NULL,
	[NoTelp] [varchar](50) NULL,
	[NoHP] [varchar](18) NULL,
	[Longitude] [varchar](30) NULL,
	[Latitude] [varchar](30) NULL,
	[CreateBy] [varchar](20) NULL,
	[TglEntry] [datetime],
	[CreateDate] [datetime]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


