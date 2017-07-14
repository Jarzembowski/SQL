<?php
include_once('safe/conn.php');

//select usuario.UserId , case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end, ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01') as dt_ultima_atividade FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 group by usuario.UserId order by datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01'))

//select usuario.UserId , case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end, ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01') as dt_ultima_atividade, sum(IF(dataAmado = null or dataAmado = '' or dataAmado = '0000-00-00', 0, 1)) + sum(IF(dataVisto = null or dataVisto = '' or dataVisto = '0000-00-00', 0, 1)) + sum(IF(dataCompartilhado = null or dataCompartilhado = '' or dataCompartilhado = '0000-00-00', 0, 1)) as nivel_atividade FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 group by usuario.UserId order by datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01'))

/*
create table jsonDadosRelatorios
(id int not null auto_increment,
no_relatorio varchar(50) not null,
dt_sincronizacao datetime not null,
json_consulta longtext not null,
  PRIMARY KEY ( id )
)
*/

Class Atividade {
	
	public function getTotalInativos(){
		
		$pdo = pdo();
		$Query = "SELECT count(*) as qt_inativos from ( select count(usuario.UserId) FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 5) as total";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

		
	}

	public function getTotalLojistasInativos(){
		
		$pdo = pdo();
		$Query = "SELECT count(*) as qt_inativos from ( select count(loja.lojaId) FROM `loja` left outer join itemloja on loja.lojaId = itemloja.lojaId inner join item on item.itemId = itemloja.itemId group by loja.lojaId having ifNull(datediff(now(), ifNull(max(item.dataPublicacao),'2016-01-01')),100) > 20) as total_inativo_lojistas";	
	    $pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");	
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}

	public function getTotalLojistaInativosCritico(){
		
		$pdo = pdo();
		$Query = "SELECT count(*) as qt_inativos_critico from ( select count(loja.lojaId) FROM `loja` left outer join itemloja on loja.lojaId = itemloja.lojaId inner join item on item.itemId = itemloja.itemId group by loja.lojaId having ifNull(datediff(now(), ifNull(max(item.dataPublicacao),'2016-01-01')),100) > 30) as total_inativo_lojistas";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}

	public function getTotalLojistas(){		
		$pdo = pdo();
		$Query = "SELECT count(*) as total_lojistas FROM `loja`";
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;	
	}


	public function getTotal(){		
		$pdo = pdo();
		$Query = "SELECT count(*) as total_usuarios_app FROM `usuario` where UserType = 1";
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;	
	}
	
	public function getLista(){	


		$pdo = pdo();
		$Query = "select usuario.UserId , case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, 
				case when datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 30 
				then '##2##' 
				when  datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 5 then
				'##1##' else
				'##0##'  end as status,
				ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01') as dt_ultima_atividade, 
				sum(IF(dataAmado = null or dataAmado = '' or dataAmado = '0000-00-00', 0, 1)) + sum(IF(dataVisto = null or dataVisto = '' or dataVisto = '0000-00-00', 0, 1)) + sum(IF(dataCompartilhado = null or dataCompartilhado = '' or dataCompartilhado = '0000-00-00', 0, 1)) as nivel_atividade,				
				   sum(ItemVisto) + sum(ItemAmei) + sum(itemCompartilhado) as nivel_atividade_ completo
				   FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 group by usuario.UserId order by datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01'))";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");			
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

	}

	public function getlistaUsuariosEmail(){

		$pdo = pdo();
		$Query = "select usuario.UserId , case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, userEmail FROM `usuario` where UserType = 1";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");			
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

		
	}


	public function getUsuariosAtivosEmail(){

		$pdo = pdo();
		$Query = "select case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, userEmail FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 and instr(usuario.UserEmail, '@facebook.com') = 0 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) < 5";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");			
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

		
	}

		public function getUsuariosInativosEmail(){

		$pdo = pdo();
		$Query = "select case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, userEmail FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 and instr(usuario.UserEmail, '@facebook.com') = 0 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 5";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");			
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

		
	}

	public function getListaLojistas(){	


		$pdo = pdo();
		$Query = "Select loja.LojaId,loja.LojaNome, case when ifNull(datediff(now(), ifNull(max(item.dataPublicacao),'2016-01-01')),100) > 30 then '##2##' when ifNull(datediff(now(), ifNull(max(item.dataPublicacao),'2016-01-01')),100) > 20 then '##1##' else '##0##' end as status, ifNull(max(item.dataPublicacao),'2016-01-01') as dt_ultima_atividade FROM `loja` left outer join itemloja on loja.lojaId = itemloja.lojaId inner join item on item.itemId = itemloja.itemId group by loja.lojaId";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

	}

		public function getTotalUsuariosInativosCritico(){
		
		$pdo = pdo();
		$Query = "SELECT count(*) as qt_inativos_critico from ( select count(usuario.UserId) FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 30) as total";		
		$pdo->exec("set session TRANSACTION ISOLATION LEVEL read uncommitted");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}

	public function getListaLojistasBairro(){	


		$pdo = pdo();
		$Query = "select loja.LojaId,loja.LojaNome,loja.LojaStatus,loja.LojaBairro,

					(select sum(IFNULL(usuarioloja.visto, 0)) 
					from  usuarioloja
					where usuarioloja.idLoja = loja.lojaId) as qt_visitas,
					sum(
					case when IFNULL(usuarioitem.ItemVisto, 0) > 0  then 1 else 0 end
					) as qt_itens_vistos,
					sum(
					case when IFNULL(usuarioitem.ItemAmei, 0) > 0  then 1 else 0 end
					 ) as qt_itens_amados,				
					(select sum(case when IFNULL(usuariolojaamo.UserId, 0) > 0 then 1 else 0 end) 
					from  usuariolojaamo
					where usuariolojaamo.LojaId = loja.lojaId) as qt_seguidores			
					from loja
					inner join itemloja 
					on loja.LojaId = itemloja.LojaId
					left outer join usuarioitem
					on usuarioitem.ItemId = itemloja.ItemId

					group by loja.LojaId,loja.LojaNome,loja.LojaStatus,loja.LojaBairro";			
		$pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL READ uncommitted;");
		$go = $pdo->prepare($Query);
		$go->execute();
		$data = $go->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;

	}
}

/*
EMAILS USUARIOS INATIVOS
select case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, userEmail FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 and instr(usuario.UserEmail, '@facebook.com') = 0 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) > 5


EMAILS USUARIOS ATIVOS
select case usuario.FacebookNome when '' then usuario.USerNome else usuario.FacebookNome end as nome_usuario, userEmail FROM `usuario` left outer join usuarioitem on usuarioitem.UserId = usuario.UserId where UserType = 1 and instr(usuario.UserEmail, '@facebook.com') = 0 group by usuario.UserId having datediff(now(), ifNull(greatest(max(dataAmado),max(dataVisto),max(dataCompartilhado)),'2016-01-01')) < 5




*/




?>

