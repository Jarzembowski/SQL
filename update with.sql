
					
with rs as (
select cd_impressora,ROW_NUMBER() over (partition by cd_empresa order by cd_filial) as rn
				from impressorafiscal where cd_empresa = 3) 

update rs
set cd_impressora = rn;
GO

