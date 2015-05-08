

/****** Object:  Table [dbo].[BCP_DFA]    Script Date: 12/19/2014 13:56:10 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFA](
	[Kodenota] [varchar](20) NULL,
	[Faktur] [varchar](20) NULL,
	[StatusKirim] [bit] NULL,
	[Tunai] [money] NULL,
	[BG] [money] NULL,
	[Stempel] [bit] NULL,
	[TandaTerima] [bit] NULL,
	[BGDetail] [varchar](100) NULL,
	[AlasanBT] [varchar](10) NULL,
	[CreateDate] [datetime] NULL,
	[Longitude] [numeric](10, 6) NULL,
	[Latitude] [numeric](10, 6) NULL,
	[NC] [varchar](20) NULL,
	[StartEntry] [datetime] NULL,
	[FinishEntry] [datetime] NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

--ALTER TABLE BCP_DFA ADD StartEntry DATETIME NULL
--ALTER TABLE BCP_DFA ADD FinishEntry DATETIME NULL




