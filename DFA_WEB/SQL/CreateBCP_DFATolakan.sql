

/****** Object:  Table [dbo].[BCP_DFATolakan]    Script Date: 01/19/2015 11:52:07 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BCP_DFATolakan]') AND type in (N'U'))
DROP TABLE [dbo].[BCP_DFATolakan]
GO


/****** Object:  Table [dbo].[BCP_DFATolakan]    Script Date: 01/19/2015 11:52:08 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFATolakan](
	[Tgl] [datetime] NOT NULL,
	[Faktur] [varchar](20) NOT NULL,
	[Brg] [varchar](20) NOT NULL,
	[Jml] [int] NOT NULL,
	[CreateBy] [varchar](50) NOT NULL,
	[ReasonCode] [varchar](10) NOT NULL,
	[KodeTO] [varchar](50) NULL,
	[Operator] [varchar](50) NULL,
	[StatusT] [bit] NOT NULL,
	[TglEntry] [datetime] NULL 
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


