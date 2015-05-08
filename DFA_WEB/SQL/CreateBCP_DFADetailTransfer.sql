

/****** Object:  Table [dbo].[BCP_DFADetailTransfer]    Script Date: 12/17/2014 09:05:53 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFADetailTransfer](
	[Kode] [int] IDENTITY(1,1) NOT NULL,
	[KodeNota] [varchar](20) NOT NULL,
	[Faktur] [varchar](20) NOT NULL,
	[Bank] [varchar](20) NOT NULL,
	[TglTransfer] [datetime] NOT NULL,
	[Jml] [money] NOT NULL,
	[TransferStatus] [varchar](5) NULL,
	[KodeBank] [varchar](20) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


