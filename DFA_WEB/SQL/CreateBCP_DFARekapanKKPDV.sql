

/****** Object:  Table [dbo].[BCP_DFARekapanKKPDV]    Script Date: 12/27/2014 13:30:29 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BCP_DFARekapanKKPDV]') AND type in (N'U'))
DROP TABLE [dbo].[BCP_DFARekapanKKPDV]
GO



/****** Object:  Table [dbo].[BCP_DFARekapanKKPDV]    Script Date: 12/27/2014 13:30:30 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFARekapanKKPDV](
	[Kode] [int] IDENTITY(1,1) PRIMARY KEY,
	[KodeNota] [varchar](20) NOT NULL,
	[Tipe] [varchar](15) NOT NULL,
	[Bank] [varchar](50) NULL,
	[Nomor] [varchar](50) NULL,
	[Tgl] [datetime] NULL,
	[Jml] [money] NOT NULL,
	[StatusT] [bit] NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


