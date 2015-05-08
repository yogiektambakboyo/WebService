

/****** Object:  Table [dbo].[BCP_DFADetailCekBG]    Script Date: 12/11/2014 11:06:22 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFADetailCekBG](
	[Kode] [int] IDENTITY(1,1) NOT NULL,
	[KodeNota] [varchar](20) NOT NULL,
	[Faktur] [varchar](20) NOT NULL,
	[Bank] [varchar](20) NOT NULL,
	[NoCekBG] [varchar](50) NOT NULL,
	[TglJatuhTempo] [datetime] NULL,
	[Jml] [money] NOT NULL,
	[BGStatus] [varchar](5) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


