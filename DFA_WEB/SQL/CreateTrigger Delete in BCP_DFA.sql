

/****** Object:  Trigger [dbo].[trgMasterCollector]    Script Date: 12/16/2014 17:14:41 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [dbo].[BCP_DFAOnDelete] ON [dbo].[BCP_DFA]
INSTEAD OF DELETE
AS
DELETE
BCP_DFADetailCekBG
From deleted d,
BCP_DFADetailCekBG k
Where k.kodenota=d.kodenota

DELETE
BCP_DFADetailTransfer
From deleted d,
BCP_DFADetailTransfer k
Where k.kodenota=d.kodenota

DELETE
BCP_DFA
From deleted d,
BCP_DFA k
Where k.kodenota=d.kodenota




