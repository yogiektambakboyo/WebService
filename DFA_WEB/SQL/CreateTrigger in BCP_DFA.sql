

/****** Object:  Trigger [dbo].[BCP_DFAOnInsert]    Script Date: 12/11/2014 11:03:59 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [dbo].[BCP_DFAOnInsert] ON [dbo].[BCP_DFA] 
FOR INSERT
AS

update b set tglinput=k.tgltransaksi,UcStatus=i.AlasanBT,TglEntry=getdate()
from inserted i join bcp_detailpenagihan b on i.kodenota=b.kodenota and i.faktur=b.faktur
join kategori k on k.cabang=left(b.kodenota,5)
where i.statuskirim=0

update b set tglinput=k.tgltransaksi,[Status]=case 
when i.Tunai+i.BG>0 then 'Tunai/BG/Transfer'
when i.Stempel=1 then 'Stempel'
when i.TandaTerima=1 then 'Tanda Terima'
else 'Tunai/BG/Transfer' end,
Kembali=case when i.Tunai+i.BG>0 then 'C' else 'T' end,
Tunai=i.Tunai,BG=i.BG,TandaTerima=case when i.Stempel=1 or i.TandaTerima=1 then b.SisaBayar else 0 end,
TglTT=case when i.Stempel=1 or i.TandaTerima=1 then k.TglTransaksi else null end,TglEntry=getdate()
from inserted i join bcp_detailpenagihan b on i.kodenota=b.kodenota and i.faktur=b.faktur
join kategori k on k.cabang=left(b.kodenota,5)
where i.statuskirim=1

update m set statuskirim=1,deliverydate=k.tgltransaksi,tglentry=getdate()
from inserted i join masterjual m on m.kodenota=i.faktur
join kategori k on k.cabang=left(m.kodenota,5)
where i.statuskirim=1




GO


