

/****** Object:  Table [dbo].[BCP_DFASecureCode]    Script Date: 01/08/2015 11:51:48 ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BCP_DFASecureCode]') AND type in (N'U'))
DROP TABLE [dbo].[BCP_DFASecureCode]
GO


/****** Object:  Table [dbo].[BCP_DFASecureCode]    Script Date: 01/08/2015 11:51:48 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[BCP_DFASecureCode](
	[Sopir] [varchar](6) NOT NULL,
	[DeviceID] [varchar](7) NULL,
 CONSTRAINT [PK_BCP_DFASecureCode] PRIMARY KEY CLUSTERED 
(
	[Sopir] ASC
)
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

--insert into BCP_DFASecureCode(Sopir,DeviceID) values ('01/744','0597979')


