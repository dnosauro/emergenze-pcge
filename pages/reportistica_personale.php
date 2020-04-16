<?php 

$subtitle="Report esteso (dettagli squadre e personale impiegato)";

$id=$_GET['id'];


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Gestione emergenze</title>
<?php 
//require('./tables/griglia_dipendenti_save.php');
require('./req.php');
require('/home/local/COMGE/egter01/emergenze-pcge_credenziali/conn.php');
//require('./conn.php');

require('./check_evento.php');


?>


    
</head>

<body>

    <div id="wrapper">

        <?php 
            require('./navbar_up.php')
        ?>  
        <?php 
            require('./navbar_left.php');
            
         

        ?> 
            

        <div id="page-wrapper">
            <div class="row">
                <!--div class="col-sm-12">
                    <h1 class="page-header">Dashboard</h1>
                </div-->
                <!-- /.col-sm-12 -->
            </div>
            <!-- /.row -->
            
            
            <?php //echo $note_debug; ?>
           

            
			

            <div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
			<h3>Evento n. <?php echo str_replace("'", "", $id); ?> - Tipo: 
			<?php
			$query_e='SELECT e.id, tt.descrizione, n.nota, to_char(e.data_ora_inizio_evento, \'DD/MM/YYYY HH24:MI\'::text) AS data_ora_inizio_evento, 
			to_char(e.data_ora_chiusura, \'DD/MM/YYYY HH24:MI\'::text) AS data_ora_chiusura, 
			to_char(e.data_ora_fine_evento, \'DD/MM/YYYY HH24:MI\'::text) AS data_ora_fine_evento 
            FROM eventi.t_eventi e
            JOIN eventi.join_tipo_evento t ON t.id_evento=e.id
			LEFT JOIN eventi.t_note_eventi n ON n.id_evento=e.id
            JOIN eventi.tipo_evento tt on tt.id=t.id_tipo_evento
			 	WHERE e.id =' .$id.';';
				$result_e = pg_query($conn, $query_e);
				while($r_e = pg_fetch_assoc($result_e)) {
					echo $r_e['descrizione'];
					$nota_evento=$r_e['nota'];
					$inizio_evento=$r_e['data_ora_inizio_evento'];
					$chiusura_evento=$r_e['data_ora_chiusura'];
					$fine_evento=$r_e['data_ora_fine_evento'];
				}
			?>
			<button class="btn btn-info noprint" onclick="printDiv('page-wrapper')">
			<i class="fa fa-print" aria-hidden="true"></i> Stampa pagina report (demo)</button>
			</h3>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<h3> Data:
			<script>
			var d = new Date();
			var curr_date = d.getDate();
			var curr_month = d.getMonth()+1;
			var curr_year = d.getFullYear();
			document.write(curr_date + "/" + curr_month + "/" + curr_year);
			</script>
			Ora:
			<script>
			var d = new Date();
			var curr_h = ('0'+d.getHours()).slice(-2);
			var curr_min = ('0'+d.getMinutes()).slice(-2);
			document.write(curr_h + ":" + curr_min);
			</script>
			</h3>
			</div>
			</div>
			<hr>
			<?php
			echo '<div class="row"><div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
			echo ' <img src="../img/pc_ge_sm.png" alt=""></div>';
			echo '<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">';
			if (isset($nota_evento)){
				echo '<h2>'.$nota_evento.'</h2>'; 
			}
			echo '<b>Municipi interessati</b>: ';
			$query3="SELECT  b.nome_munic From eventi.join_municipi a,geodb.municipi b  WHERE a.id_evento=".$id." and a.id_municipio::integer=b.codice_mun::integer;";
			//echo $query3;
			$result3 = pg_query($conn, $query3);
			$k=0;
			while($r3 = pg_fetch_assoc($result3)) {
				if ($k>0){
					echo ', ';
				}
				echo $r3["nome_munic"];
				$k=$k+1;
				//$municipir[]=array($id,$r3["nome_munic"]);
			}
			
			echo '<br><b>Data e ora inizio</b>: '.$inizio_evento;
			if ($chiusura_evento!=''){
				echo '<br><b>Data e ora inizio fase di chiusura</b>: '.$chiusura_evento;
			}
			if ($fine_evento!=''){
				echo '<br><b>Data e ora chiusura definitiva</b>: '.$fine_evento;
			}
			if ($chiusura_evento!='' && $fine_evento=='' ){
				echo ' - <i class="fas fa-hourglass-end"></i> Evento in chiusura';
			}
			if ($chiusura_evento!='' && $fine_evento!='' ){
				echo ' - <i class="fas fa-stop"></i> Evento chiuso';
			}
			echo '</div></div>';
			?>
			
			
			
			<!--hr>
			<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			
			<?php
			$query="SELECT * FROM eventi.v_allerte WHERE id_evento=".$id.";";
			$result = pg_query($conn, $query);
			while($r = pg_fetch_assoc($result)) {	

				$timestamp = strtotime($r["data_ora_inizio_allerta"]);
				setlocale(LC_TIME, 'it_IT.UTF8');
				$data_start = strftime('%A %e %B %G', $timestamp);
				$ora_start = date('H:i', $timestamp);
				$timestamp = strtotime($r["data_ora_fine_allerta"]);
				$data_end = strftime('%A %e %B %G', $timestamp);
				$ora_end = date('H:i', $timestamp);								
				$color=str_replace("'","",$r["rgb_hex"]);
				//echo $color;
				//echo '<span class="dot" style="background-color:'.$color.'"></span>';
				//echo "<style> .fas { color: ".$color."; -webkit-print-color-adjust: exact;}</style>";
				echo "<i class=\"fas fa-circle fa-1x\" style=\"color:".$color."\"></i> <b>Allerta ".$r["descrizione"]."</b> dalle ".$ora_start." di ".$data_start." alle ore " .$ora_end ." di ".$data_end. " <br>";
			}
			?>
			
 
			</div>	
			
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<?php
			$query="SELECT * FROM eventi.v_foc WHERE id_evento=".$id.";";
			$result = pg_query($conn, $query);
			while($r = pg_fetch_assoc($result)) {
				$timestamp = strtotime($r["data_ora_inizio_foc"]);
				setlocale(LC_TIME, 'it_IT.UTF8');
				$data_start = strftime('%A %e %B %G', $timestamp);
				$ora_start = date('H:i', $timestamp);
				$timestamp = strtotime($r["data_ora_fine_foc"]);
				$data_end = strftime('%A %e %B %G', $timestamp);
				$ora_end = date('H:i', $timestamp);
				$color=str_replace("'","",$r["rgb_hex"]);								
				echo "<i class=\"fas fa-circle fa-1x\" style=\"color:".$color."\"></i> <b> Fase di ".$r["descrizione"]."</b> dalle ".$ora_start." di ".$data_start." alle ore " .$ora_end ." di ".$data_end. " <br>";
			}
			?>
			</div>
			<hr>
			</div>
			
			<div class="row">
			 
			 <?php //require('./monitoraggio_meteo_embed.php'); ?>
            
			</div>
			
			<hr>
			<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3>Comunicazioni generiche</h3>
				<button type="button" class="btn btn-info noprint"  data-toggle="modal" data-target="#comunicazione">
					   <i class="fas fa-plus"></i> Aggiungi comunicazione</button>
					   <ul>
	   					<?php
						$query='SELECT id, to_char(data_aggiornamento, \'DD/MM/YY HH24:MI\'::text) AS data_aggiornamento, testo, allegato FROM report.t_comunicazione 
						WHERE id_evento = '.$id.';';
						//echo $query;
						$result = pg_query($conn, $query);
						$c=0;
						while($r = pg_fetch_assoc($result)) {
							if ($c==0){
								echo "<h3>Elenco comunicazioni generiche</h3>";
							}
							$c=$c+1;
							//echo '<button type="button" class="btn btn-info noprint"  data-toggle="modal" 
							//data-target="#update_mon_'.$r['id'].'">
							//<i class="fas fa-edit"></i> Edit </button>';
							echo " <li><b>Comunicazione del ".$r['data_aggiornamento']."</b>: ";
							echo $r['testo'];
							if ($r['allegato']!=''){
								echo " (<a href=\"../../".$r['allegato']."\">Allegato</a>)";
							}
							echo "</li>";
						}
						echo "</ul><hr>";
						?>
						
			</div>
			</div-->
			
			<!-- Modal comunicazione da UO-->
						<div id="comunicazione" class="modal fade" role="dialog">
						  <div class="modal-dialog">

							<!-- Modal content-->
							<div class="modal-content">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Comunicazioni sull'evento / Verbale COC</h4>
							  </div>
							  <div class="modal-body">
							  

								<form autocomplete="off"  enctype="multipart/form-data"  action="eventi/comunicazione.php?id=<?php echo $id; ?>" method="POST">
										 <div class="form-group">
										<label for="note">Testo comunicazione <?php echo $id_evento;?></label>  <font color="red">*</font>
										<textarea required="" class="form-control" id="note"  name="note" rows="3"></textarea>
									  </div>
									
									<!--	RICORDA	  enctype="multipart/form-data" nella definizione del form    -->
									<div class="form-group">
									   <label for="note">Eventuale allegato (es. verbale COC)</label>
										<input type="file" class="form-control-file" name="userfile" id="userfile">
									</div>

								<button  id="conferma" type="submit" class="btn btn-primary">Invia comunicazione</button>
									</form>

							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
							  </div>
							</div>

						  </div>
						</div>
			
			
			
			<div class="row">
			
            <?php require('./attivita_sala_emergenze_embed.php'); ?>
			
			</div>
			
			<hr>
            <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3>Comunicazioni e informazioni alla popolazione</h3>
			</div>
			</div>
			<div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h4> 
			 <?php if( $contatore_nverde > 0) {?>
				<i>Numero verde non attivo</i>
			 <?php } else { ?>
				<i>Numero verde attivo</i>
			 <?php }  ?> 
			</h4>
			
			<?php

			$query="SELECT * FROM eventi.t_attivazione_nverde WHERE id_evento=".$id." and data_ora_fine <= now();";
			//echo $query;
			//exit;
				$result = pg_query($conn, $query);
			while($r = pg_fetch_assoc($result)) {
				$check_nverde=2;
			}
			
				
			if($check_nverde==2) {
				echo "<h5>Storico numero verde<h5>";
			$result = pg_query($conn, $query);
			while($r = pg_fetch_assoc($result)) {	

				$timestamp = strtotime($r["data_ora_inizio"]);
				setlocale(LC_TIME, 'it_IT.UTF8');
				$data_start = strftime('%A %e %B %G', $timestamp);
				$ora_start = date('H:i', $timestamp);
				$timestamp = strtotime($r["data_ora_fine"]);
				$data_end = strftime('%A %e %B %G', $timestamp);
				$ora_end = date('H:i', $timestamp);								
				$color=str_replace("'","",$r["rgb_hex"]);
				//echo $color;
				echo "<li> <i class=\"fas fa-circle fa-1x\" style=\"color:".$color."\007c37\"></i> <b>Numero verde  attivo</b> dalle ".$ora_start." di ".$data_start." alle ore " .$ora_end ." di ".$data_end. " </li>";
			}
			}
			?>
            </div>
            
            </div>
			<div class="row">
            
			<?php require('./operatore_nverde_embed.php'); ?>
            
           
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <hr>
            <h4>Numero chiamate ricevute</h4>
            
            <?php 
            /*$query_e="SELECT e.id, tt.descrizione 
            FROM eventi.t_eventi e
            JOIN eventi.join_tipo_evento t ON t.id_evento=e.id
            JOIN eventi.tipo_evento tt on tt.id=t.id_tipo_evento
			 	WHERE e.valido != 'f'
			   GROUP BY e.id, tt.descrizione;";
             
            $result_e = pg_query($conn, $query_e);
				//echo "<ul>";
				while($r_e = pg_fetch_assoc($result_e)) {*/
					//echo '<b>Tipo Evento</b>:'.$r_e['descrizione']. '<br>';
					$query="SELECT count(r.id)
					FROM segnalazioni.t_richieste_nverde r 
					WHERE r.id_evento = ".$id.";";
					//echo $query;
					$result = pg_query($conn, $query);
					while($r = pg_fetch_assoc($result)) {
						echo "<b>Richieste generiche:</b>".$r['count']."<br>";
					}
					$query="SELECT count(r.id)
					FROM segnalazioni.t_segnalazioni r 
					WHERE r.id_evento = ".$id.";";
					//echo $query;
					$result = pg_query($conn, $query);
					while($r = pg_fetch_assoc($result)) {
						echo "<b>Segnalazioni:</b>".$r['count']."<br><br>";
					}
				/*}*/ 
            
            
            
            ?>
            
            
            </div>            
            </div>
            <!-- /.row -->            
            <hr>
            
            
            <?php 
             
            //require('./conteggi_dashboard.php');
            
            //require('./contatori.php');
            ?>
            
            <div class="row">
                
                
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h3>Elenco segnalazioni </h3>
			</div>
<hr>
<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">			
<h4>Riepilogo</h4>
</div>



<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
<svg width="400" height="300"></svg>
<?php
require('./grafico_criticita.php');
?>
</div>


<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">			
<table  id="segnalazioni_count" class="table table-condensed" 
style="word-break:break-all; word-wrap:break-word;" data-toggle="table" 
data-url="./tables/griglia_segnalazioni_conteggi.php?id=<?php echo $id?>" 
data-show-export="false" data-search="false" data-click-to-select="false" 
data-pagination="false" data-sidePagination="false" data-show-refresh="false" 
data-show-toggle="false" data-show-columns="false" data-toolbar="#toolbar">

<thead>

<tr>
   <th data-field="criticita" data-sortable="false" data-visible="true" >Tipologia</th>
   <th data-field="pervenute" data-sortable="true" data-visible="true">Pervenute</th>
   <th data-field="risolte" data-sortable="true" data-visible="true">Risolte</th>
</tr>
</thead>
</table>
</div>             
<hr>		 
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">			 
<h4>Dettaglio segnalazioni in elaborazione o chiuse</h4>

<?php 

$query= " SELECT 
 min(s.data_ora) as data_ora,
    count(s.id) AS num,
	string_agg(s.id::text, ', '::text) AS id_segn,
    string_agg(s.descrizione::text, ', '::text) AS descrizione,
    array_to_string(array_agg(DISTINCT c.descrizione::text), ', '::text) AS criticita,
    array_to_string(array_agg(DISTINCT m.nome_munic::text), ', '::text) AS nome_munic,
    string_agg(
        CASE
            WHEN s.id_civico IS NULL THEN ( SELECT concat('~ ', civici.desvia, ' ', civici.testo) AS concat
               FROM geodb.civici
              WHERE civici.geom && st_expand(st_transform(s.geom, 3003), 250::double precision)
              ORDER BY (st_distance(civici.geom, st_transform(s.geom, 3003)))
             LIMIT 1)
            ELSE (g.desvia::text || ' '::text) || g.testo::text
        END, ', '::text) AS localizzazione,
    jl.id_segnalazione_in_lavorazione AS id_lavorazione,
    l.in_lavorazione,
    l.descrizione_chiusura,
    l.id_profilo,
        CASE
            WHEN (( SELECT count(i.id) AS sum
               FROM segnalazioni.v_incarichi_last_update i
              WHERE i.id_lavorazione = jl.id_segnalazione_in_lavorazione AND i.id_stato_incarico < 3)) > 0 OR (( SELECT count(i.id) AS sum
               FROM segnalazioni.v_incarichi_interni_last_update i
              WHERE i.id_lavorazione = jl.id_segnalazione_in_lavorazione AND i.id_stato_incarico < 3)) > 0 OR (( SELECT count(i.id) AS sum
               FROM segnalazioni.v_provvedimenti_cautelari_last_update i
              WHERE i.id_lavorazione = jl.id_segnalazione_in_lavorazione AND i.id_stato_provvedimenti_cautelari < 3)) > 0 OR (( SELECT count(i.id) AS sum
               FROM segnalazioni.v_sopralluoghi_last_update i
              WHERE i.id_lavorazione = jl.id_segnalazione_in_lavorazione AND i.id_stato_sopralluogo < 3)) > 0 THEN 't'::text
            ELSE 'f'::text
        END AS incarichi,
	   (SELECT count(i.id) AS sum
         FROM segnalazioni.t_incarichi i
		JOIN segnalazioni.join_segnalazioni_incarichi j ON j.id_incarico= i.id
         WHERE j.id_segnalazione_in_lavorazione = jl.id_segnalazione_in_lavorazione) as conteggio_incarichi,
		(SELECT count(i.id) AS sum
         FROM segnalazioni.t_incarichi_interni i
		JOIN segnalazioni.join_segnalazioni_incarichi_interni j ON j.id_incarico= i.id
         WHERE j.id_segnalazione_in_lavorazione = jl.id_segnalazione_in_lavorazione) as conteggio_incarichi_interni,
		(SELECT count(i.id) AS sum
         FROM segnalazioni.t_sopralluoghi i
		JOIN segnalazioni.join_segnalazioni_sopralluoghi j ON j.id_sopralluogo= i.id
         WHERE j.id_segnalazione_in_lavorazione = jl.id_segnalazione_in_lavorazione) as conteggio_sopralluoghi,
		(SELECT count(i.id) AS sum
         FROM segnalazioni.t_provvedimenti_cautelari i
		JOIN segnalazioni.join_segnalazioni_provvedimenti_cautelari j ON j.id_provvedimento = i.id
         WHERE j.id_segnalazione_in_lavorazione = jl.id_segnalazione_in_lavorazione) as conteggio_pc,
    max(s.geom::text) AS geom 
   FROM segnalazioni.t_segnalazioni s
     JOIN segnalazioni.tipo_criticita c ON c.id = s.id_criticita
     JOIN eventi.t_eventi e ON e.id = s.id_evento
     LEFT JOIN segnalazioni.join_segnalazioni_in_lavorazione jl ON jl.id_segnalazione = s.id
     LEFT JOIN segnalazioni.t_segnalazioni_in_lavorazione l ON jl.id_segnalazione_in_lavorazione = l.id
     LEFT JOIN geodb.municipi m ON s.id_municipio = m.id::integer
     LEFT JOIN geodb.civici g ON g.id = s.id_civico
  WHERE s.id_evento=".$id." and jl.id_segnalazione_in_lavorazione > 0
  GROUP BY jl.id_segnalazione_in_lavorazione, l.in_lavorazione, l.id_profilo, s.id_evento, e.fine_sospensione, l.descrizione_chiusura
  ORDER BY data_ora ASC;";
//echo $query;
$result = pg_query($conn, $query);
while($r = pg_fetch_assoc($result)) {
	echo "<b>Id segnalazioni:</b>".$r['id_segn']." - ";
	if ($r['num'] > 1){
		echo "<b>Num. segnalazioni collegate:</b>".$r['num']." - ";
	}
	echo "<b>Stato</b>: ";
	if ($r['in_lavorazione']=='t'){
		echo '<i class="fas fa-play" style="color:#5cb85c"></i> in lavorazione';
	} else if ($r['in_lavorazione']=='f') {
		echo '<i class="fas fa-stop"></i> chiusa';
	} else {
		echo '<i class="fas fa-exclamation" style="color:#ff0000"></i> da prendere in carico';
	}
	echo "<br>";
	if($r['num']>1){
		echo "<b>Data e ora prina segnalazione:</b>".$r['data_ora']."<br>";
	} else {
		echo "<b>Data e ora segnalazione:</b>".$r['data_ora']."<br>";
	}
	echo "<b>Tipo criticit&agrave:</b>".$r['criticita']."<br>";
	echo "<b>Descrizione:</b>".$r['descrizione']."<br>";
	echo "<b>Municipio:</b>".$r['nome_munic']." - ";
	echo "<b>Indirizzo:</b>".$r['localizzazione']."<br>";
	if ($r['descrizione_chiusura']!=''){
		echo "<b>Note chiusura:</b>".$r['descrizione_chiusura']."<br>";
	}
	if ($r['incarichi']=='t'){
		echo '<i class="fas fa-circle" title="incarichi in corso" style="color:#f2d921"></i> Lavorazione in corso ';
	} else if ($r['incarichi']=='f') {
		echo '<i class="fas fa-circle" title="nessun incarico in corso" style="color:#ff0000"></i> Nessuna lavorazione in corso ';
	}
	echo "<br>";
	
	
	
	if ($r['conteggio_incarichi']>0){
		if ($r['conteggio_incarichi']==1){
			echo ' <h4> '.$r['conteggio_incarichi'].' incarico assegnato </h4>';
		} else {
			echo ' <h4> '.$r['conteggio_incarichi'].' incarichi assegnati </h4>';
		}		$query2 = "SELECT i.id,
		to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
		to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
		i.descrizione,
		u.descrizione AS descrizione_uo,
		to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
		to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
		to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
		i.note_ente,
		i.note_rifiuto,
		st.id_stato_incarico,
		t.descrizione AS descrizione_stato,
		st.parziale
	   FROM segnalazioni.t_incarichi i
		 JOIN segnalazioni.join_segnalazioni_incarichi j ON j.id_incarico = i.id
		 JOIN segnalazioni.stato_incarichi st ON st.id_incarico = i.id
		 JOIN segnalazioni.tipo_stato_incarichi t ON t.id = st.id_stato_incarico
		 JOIN segnalazioni.v_segnalazioni s ON s.id_lavorazione = j.id_segnalazione_in_lavorazione
		 JOIN varie.v_incarichi_mail u ON u.cod = i.id_uo::text
		 JOIN eventi.t_eventi e ON e.id = s.id_evento
		WHERE s.id_lavorazione=".$r['id_lavorazione']."
		AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_incarichi where id_incarico =i.id) 
		GROUP BY i.id, data_invio, ora_invio, i.descrizione, descrizione_uo, time_preview, time_start, time_stop, note_ente, note_rifiuto, descrizione_stato,parziale, id_stato_incarico;";
		//echo $query2 ."<br>";
		echo "<ul>";
		$result2 = pg_query($conn, $query2);
		while($r2 = pg_fetch_assoc($result2)) {
			echo "<li> ";
			if($r2['id_stato_incarico']==1){
				echo '<i class="fas fa-exclamation" title="Incarico inviato, ma non ancora preso in carico" style="color:#f2d921"></i>';
			} else if ($r2['id_stato_incarico']==2){
				echo '<i class="fas fa-play" title="Incarico in lavorazione" style="color:#f2d921"></i>';
			} else if($r2['id_stato_incarico']==3){
				echo '<i class="fas fa-check" title="Incarico chiuso" style="color:#5cb85c"></i>';
			}else if($r2['id_stato_incarico']==4){
				echo '<i class="fas fa-exclamation" title="Incarico rifiutato" style="color:#ff0000"></i>';
			}
			echo " Incarico ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
			echo "a ".$r2[descrizione_uo]." <br>Descrizione incarico: " .$r2['descrizione']." ";
			if ($r2['note_ente']!=''){
				echo ' - Note chiusura: '.$r2['note_ente'].' ';
			}
			if ($r2['note_rifiuto']!=''){
				echo ' - Note rifiuto: '.$r2['note_rifiuto'].' ';
			}
			if ($r2['parziale']=='t'){
				echo ' - <i class="fas fa-exclamation"></i> Incarico eseguito solo in parte';
			}
			echo "</li>";
		}
		echo "</ul>";
	} else {
		echo 'Nessun incarico assegnato - ';
	}
	
	
	
	if ($r['conteggio_incarichi_interni']>0){
		if ($r['conteggio_incarichi_interni']==1){
			echo ' <h4> '.$r['conteggio_incarichi_interni'].' incarico interno assegnato </h4>';
		} else {
			echo ' <h4> '.$r['conteggio_incarichi_interni'].' incarichi interni assegnati </h4>';
		}
		$query2 = "SELECT i.id,
		to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
		to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
		i.descrizione,
		to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
		to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
		to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
		i.note_ente,
		i.note_rifiuto,
		st.id_stato_incarico,
		t.descrizione AS descrizione_stato,
		st.parziale
	   FROM segnalazioni.t_incarichi_interni i
		 JOIN segnalazioni.join_segnalazioni_incarichi_interni j ON j.id_incarico = i.id
		 JOIN segnalazioni.stato_incarichi_interni st ON st.id_incarico = i.id
		 JOIN segnalazioni.tipo_stato_incarichi t ON t.id = st.id_stato_incarico
		 JOIN segnalazioni.v_segnalazioni s ON s.id_lavorazione = j.id_segnalazione_in_lavorazione
		 JOIN users.v_squadre_all u ON u.id::text = i.id_squadra::text
		 JOIN eventi.t_eventi e ON e.id = s.id_evento
		WHERE s.id_lavorazione=".$r['id_lavorazione']."
		AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_incarichi_interni where id_incarico =i.id) 
		GROUP BY i.id, data_invio, ora_invio, i.descrizione, time_preview, time_start, time_stop, note_ente, note_rifiuto, descrizione_stato,parziale, id_stato_incarico;";
		//echo $query2 ."<br>";
		echo "<ul>";
		$result2 = pg_query($conn, $query2);
		while($r2 = pg_fetch_assoc($result2)) {
			echo "<li> ";
			if($r2['id_stato_incarico']==1){
				echo '<i class="fas fa-exclamation" title="Incarico inviato, ma non ancora preso in carico" style="color:#ff0000"></i>';
			} else if ($r2['id_stato_incarico']==2){
				echo '<i class="fas fa-play" title="Incarico in lavorazione" style="color:#f2d921"></i>';
			} else if($r2['id_stato_incarico']==3){
				echo '<i class="fas fa-check" title="Incarico chiuso" style="color:#5cb85c"></i>';
			}else if($r2['id_stato_incarico']==4){
				echo '<i class="fas fa-exclamation" title="Incarico rifiutato" style="color:#ff0000"></i>';
			}
			echo " Incarico ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
			echo "<br>Descrizione incarico: " .$r2['descrizione']." ";
			if ($r2['note_ente']!=''){
				echo ' - Note chiusura: '.$r2['note_ente'].' ';
			}
			if ($r2['note_rifiuto']!=''){
				echo ' - Note rifiuto: '.$r2['note_rifiuto'].' ';
			}
			if ($r2['parziale']=='t'){
				echo ' - <i class="fas fa-exclamation"></i> Incarico eseguito solo in parte';
			}
			if($r2['id_stato_incarico']==4){
				$query_s="SELECT a.id, a.data_ora_invio as data_ora, a.data_ora_invio as data_ora_cambio, max(s.data_ora_stato) as time_stop, a.id_squadra::integer, b.nome
				FROM segnalazioni.t_incarichi_interni a
				JOIN users.t_squadre b ON a.id_squadra::integer = b.id::integer  
				JOIN segnalazioni.stato_incarichi_interni s ON s.id_incarico=a.id
				WHERE a.id=".$r2['id']."
				GROUP BY a.id, a.data_ora_invio, a.id_squadra, b.nome";
			} else { 
				$query_s="SELECT a.id_incarico, a.data_ora, a.data_ora_cambio, c.time_stop,a.id_squadra, b.nome 
				FROM segnalazioni.join_incarichi_interni_squadra a
				JOIN users.t_squadre b ON a.id_squadra=b.id 
				JOIN segnalazioni.t_incarichi_interni c ON c.id=a.id_incarico
				WHERE id_incarico =".$r2['id']." 
				ORDER BY data_ora";
			}
			//echo $query_s;
			echo "<ul>";
			require('./query_storico_squadre_incarichi.php');
			echo "</ul>";
			
			
			echo "</li>";
		}
		echo "</ul>";
	} else {
		echo 'Nessun incarico interno assegnato - ';
	}
	
	
	
	if ($r['conteggio_sopralluoghi']>0){
		if ($r['conteggio_sopralluoghi']==1){
			echo ' <h4> '.$r['conteggio_sopralluoghi'].' presidio assegnato </h4>';
		} else {
			echo ' <h4> '.$r['conteggio_sopralluoghi'].' presidi assegnati </h4>';
		}
		$query2 = "SELECT i.id,
		to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
		to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
		i.descrizione,
		to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
		to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
		to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
		i.note_ente,
		st.id_stato_sopralluogo,
		t.descrizione AS descrizione_stato
	   FROM segnalazioni.t_sopralluoghi i
		 JOIN segnalazioni.join_segnalazioni_sopralluoghi j ON j.id_sopralluogo = i.id
		 JOIN segnalazioni.stato_sopralluoghi st ON st.id_sopralluogo = i.id
		 JOIN segnalazioni.tipo_stato_sopralluoghi t ON t.id = st.id_stato_sopralluogo
		 JOIN segnalazioni.v_segnalazioni s ON s.id_lavorazione = j.id_segnalazione_in_lavorazione
		 JOIN segnalazioni.join_sopralluoghi_squadra ii ON ii.id_sopralluogo = i.id
		 JOIN users.v_squadre_all u ON u.id::text = ii.id_squadra::text
		 JOIN eventi.t_eventi e ON e.id = s.id_evento
		WHERE s.id_lavorazione=".$r['id_lavorazione']."
		AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_sopralluoghi where id_sopralluogo =i.id) 
		GROUP BY i.id, data_invio, ora_invio, i.descrizione, time_preview, time_start, time_stop, note_ente, descrizione_stato, id_stato_sopralluogo;";
		//echo $query2 ."<br>";
		echo "<ul>";
		$result2 = pg_query($conn, $query2);
		while($r2 = pg_fetch_assoc($result2)) {
			echo "<li> ";
			if($r2['id_stato_sopralluogo']==1){
				echo '<i class="fas fa-exclamation" title="Presidio inviato, ma non ancora preso in carico" style="color:#ff0000"></i>';
			} else if ($r2['id_stato_sopralluogo']==2){
				echo '<i class="fas fa-play" title="Presidio in lavorazione" style="color:#f2d921"></i>';
			} else if($r2['id_stato_sopralluogo']==3){
				echo '<i class="fas fa-check" title="Presidio chiuso" style="color:#5cb85c"></i>';
			}else if($r2['id_stato_sopralluogo']==4){
				echo '<i class="fas fa-exclamation" title="Presidio rifiutato" style="color:#ff0000"></i>';
			}
			echo " Presidio ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
			echo "Descrizione Presidio: " .$r2['descrizione']." ";
			if ($r2['note_ente']!=''){
				echo ' - Note chiusura: '.$r2['note_ente'].' ';
			}
			if($r2['id_stato_sopralluogo']==4){
				$query_s="SELECT a.id, a.data_ora_invio as data_ora, a.data_ora_invio as data_ora_cambio, max(s.data_ora_stato) as time_stop, a.id_squadra::integer, b.nome
				FROM segnalazioni.t_sopralluoghii a
				JOIN users.t_squadre b ON a.id_squadra::integer = b.id::integer  
				JOIN segnalazioni.stato_sopralluoghii s ON s.id_sopralluogo=a.id
				WHERE a.id=".$r2['id']."
				GROUP BY a.id, a.data_ora_invio, a.id_squadra, b.nome";
			} else { 
				$query_s="SELECT a.id_sopralluogo, a.data_ora, a.data_ora_cambio, c.time_stop,a.id_squadra, b.nome 
				FROM segnalazioni.join_sopralluoghi_squadra a
				JOIN users.t_squadre b ON a.id_squadra=b.id 
				JOIN segnalazioni.t_sopralluoghi c ON c.id=a.id_sopralluogo
				WHERE id_sopralluogo =".$r2['id']." 
				ORDER BY data_ora";
			}
			//echo $query_s;
			echo "<ul>";
			require('./query_storico_squadre_incarichi.php');
			echo "</ul>";
			
			
			echo "</li>";
		}
		echo "</ul>";
	} else {
		echo 'Nessun presidio assegnato - ';
	}
	
	
	
	
	
	/*if ($r['conteggio_sopralluoghi']>0){
		echo ' '.$r['conteggio_incarichi'].' presidi assegnati - ';
	} else {
		echo 'Nessun presidio assegnato - ';
	}*/
	if ($r['conteggio_pc']>0){
		if ($r['conteggio_pc']==1){
			echo ' <h4>'.$r['conteggio_pc'].' provvedimento cautelare assegnato </h4> ';
		} else {
			echo ' <h4>'.$r['conteggio_pc'].' provvedimenti cautelari assegnati </h4> ';
		}
		
	} else {
		echo '<h4> Nessun provvedidimento cautelare assegnato </h4> ';
	}
	
	
	/*	if ($r['conteggio_incarichi']>0){
			echo '<br>--<br><b>Incarichi:</b> ';
			$query_i = 'SELECT 
			data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato
			FROM segnalazioni.v_incarichi_last_update s 
			WHERE s.id_lavorazione='.$r['id_lavorazione'].' GROUP BY data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato ORDER BY data_ora_invio asc;';
			//echo $query_i;
			$result_i = pg_query($conn, $query_i);
			while($r_i = pg_fetch_assoc($result_i)) {
				echo '<br>' .$r_i['data_ora_invio'];
				echo ' - ' . $r_i['descrizione_stato']. ' - ';
				echo $r_i['descrizione_uo'] .' ('.$r_i['descrizione'].')';
			}
		}
	
		if ($r['conteggio_incarichi_interni']>0){
			echo '<br>--<br><b>Incarichi interni:</b> ';
			$query_i = 'SELECT 
			data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato
			FROM segnalazioni.v_incarichi_interni_last_update s 
			WHERE s.id_lavorazione='.$r['id_lavorazione'].' GROUP BY data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato  
			ORDER BY data_ora_invio asc;';
			//echo $query_i;
			$result_i = pg_query($conn, $query_i);
			while($r_i = pg_fetch_assoc($result_i)) {
				echo '<br>' .$r_i['data_ora_invio'];
				echo ' - ' . $r_i['descrizione_stato']. ' - ';
				echo $r_i['descrizione_uo'] .' ('.$r_i['descrizione'].')';
			}
		}
		
		
		
		if ($r['conteggio_sopralluoghi']>0){
			echo '<br>--<br><b>Presidi:</b> ';
			$query_i = 'SELECT 
			data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato
			FROM segnalazioni.v_sopralluoghi_last_update s 
			WHERE id_lavorazione='.$r['id_lavorazione'].' GROUP BY data_ora_invio, 
			descrizione, 
			descrizione_uo, descrizione_stato ORDER BY data_ora_invio asc;';
			//echo $query_i;
			$result_i = pg_query($conn, $query_i);
			while($r_i = pg_fetch_assoc($result_i)) {
				echo '<br>' .$r_i['data_ora_invio'];
				echo ' - ' . $r_i['descrizione_stato']. ' - ';
				echo $r_i['descrizione_uo'] .' ('.$r_i['descrizione'].')';
			}
		}
		
		*/
	//echo "<b>Note:</b>".$r['localizzazione']."<br>";
	echo "<hr>";
}
  
  
?>


<script>


 function nameFormatter(value) {
        if (value=='t'){
        		return '<i class="fas fa-play" style="color:#5cb85c"></i> in lavorazione';
        } else if (value=='f') {
        	   return '<i class="fas fa-stop"></i> chiusa';
        } else {
        	   return '<i class="fas fa-exclamation" style="color:#ff0000"></i> da prendere in carico';;
        }

    }
    
 function nameFormatterEdit(value) {
        
		return '<a class="btn btn-warning" href=./dettagli_segnalazione.php?id='+value+'> <i class="fas fa-edit"></i> </a>';
 
    }

  function nameFormatterRischio(value) {
        //return '<i class="fas fa-'+ value +'"></i>' ;
        
        if (value=='t'){
        		return '<i class="fas fa-exclamation-triangle" style="color:#ff0000"></i>';
        } else if (value=='f') {
        	   return '<i class="fas fa-check" style="color:#5cb85c"></i>';
        }
        else {
        		return '<i class="fas fa-question" style="color:#505050"></i>';
        }
    }


function nameFormatterMappa1(value, row) {
	//var test_id= row.id;
	return' <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myMap'+value+'"><i class="fas fa-map-marked-alt"></i></button> \
    <div class="modal fade" id="myMap'+value+'" role="dialog"> \
    <div class="modal-dialog"> \
      <div class="modal-content">\
        <div class="modal-header">\
          <button type="button" class="close" data-dismiss="modal">&times;</button>\
          <h4 class="modal-title">Anteprima segnalazione '+value+'</h4>\
        </div>\
        <div class="modal-body">\
        <iframe class="embed-responsive-item" style="width:100%; padding-top:0%; height:600px;" src="./mappa_leaflet.php#17/'+row.lat +'/'+row.lon +'"></iframe>\
        </div>\
        <!--div class="modal-footer">\
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\
        </div-->\
      </div>\
    </div>\
  </div>\
</div>';
}
	
	





</script>			 
                
                
                
                    <!--div id="panel-riepilogo" class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bell fa-fw"></i> Pannello riepilogo
                        </div>
                        
                        <div class="panel-body">
                            <div class="list-group">
                               
                                		<?php if($segn_limbo>0){?>
                                			 <a href="#segn_limbo_table" class="list-group-item">
	                                    <i class="fa fa-exclamation fa-fw" style="color:red"></i> Nuove segnalazioni da elaborare!
	                                    <span class="pull-right text-muted small"><em><?php echo $segn_limbo; ?></em>
	                                    </span>
	                                    </a>
                                    <?php }?>
                                
								
											<?php if($inc_limbo>0){?>
                                			 <div class="list-group-item" >
	                                    <i class="fa fa-exclamation fa-fw" style="color:red"></i> Nuovi incarichi ancora da prendere in carico!
	                                    <span class="pull-right text-muted small"><em><?php echo $inc_limbo; ?></em>
	                                    </span>
	                                    
	                                    </div>
                                    <?php }?>
								
								<div class="list-group-item" >
											
                                
                                    <i class="fa fa-users"></i> <b>Gestione squadre</b>
                                    <br><br>
                                     - <i class="fa fa-play"></i> Squadre in azione
                                    <span class="pull-right text-muted small"><em><?php echo $squadre_in_azione; ?></em>
                                    </span>
                                    
                                    <br>
                                     - <i class="fa fa-pause"></i> Squadre a disposizione
                                    <span class="pull-right text-muted small"><em><?php echo $squadre_disposizione; ?></em>
                                    </span>
                                    <br>
                                     - <i class="fa fa-stop"></i> Squadre a riposo
                                    <span class="pull-right text-muted small"><em><?php echo $squadre_riposo; ?></em>
                                    </span>
                                    <hr>
                                    Totale squadre eventi attivi:
                                    <span class="pull-right text-muted small"><em><?php echo $squadre_riposo; ?></em>
                                    </span>
                                </div>
                            
                            <a href="./gestione_squadre.php" class="btn btn-default btn-block">Vai alla gestione squadre</a>
							
							
							<div class="list-group-item" >
											
                                
                                    <i class="fa fa-pencil-ruler"></i> <b>Presidi</b>
                                    <br><br>
                                     - <i class="fa fa-pause"></i> Assegnati
                                    <span class="pull-right text-muted small"><em><?php echo $sopralluoghi_assegnati; ?></em>
                                    </span>
                                    
                                    <br>
                                     - <i class="fa fa-play"></i> In corso
                                    <span class="pull-right text-muted small"><em><?php echo $sopralluoghi_corso; ?></em>
                                    </span>
                                    <br>
                                     - <i class="fa fa-stop"></i> Conclusi
                                    <span class="pull-right text-muted small"><em><?php echo $sopralluoghi_conclusi; ?></em>
                                    </span>
                                    <hr>
                                    Totale presidi eventi attivi:
                                    <span class="pull-right text-muted small"><em><?php echo $sopralluoghi_tot; ?></em>
                                    </span>
                                </div>
                            
                            <a href="./nuovo_sopralluogo.php" class="btn btn-default btn-block">Crea un nuovo presidio</a>
							
							</div>
							
							<div class="list-group-item" >
											
                                
                                    <i class="fa fa-exclamation-triangle"></i> <b>Provvedimenti cautelari</b>
                                    <br><br>
                                     - <i class="fa fa-pause"></i> Assegnati
                                    <span class="pull-right text-muted small"><em><?php echo $pc_assegnati; ?></em>
                                    </span>
                                    
                                    <br>
                                     - <i class="fa fa-play"></i> In corso
                                    <span class="pull-right text-muted small"><em><?php echo $pc_corso; ?></em>
                                    </span>
                                    <br>
                                     - <i class="fa fa-stop"></i> Portati a termine
                                    <span class="pull-right text-muted small"><em><?php echo $pc_conclusi; ?></em>
                                    </span>
                                    <hr>
                                    Totale provvedimenti cautelari eventi attivi:
                                    <span class="pull-right text-muted small"><em><?php echo $pc_tot; ?></em>
                                    </span>
                                </div>
                            

                            <a href="./elenco_pc.php" class="btn btn-default btn-block">Elenco provvedimenti cautelari</a>
							
							</div>
                        
						
						
						
						

                    </div-->


                    
                    
                    
                    
                    
            </div> 
            
            
</div>        

<div class="row">              
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h3>Elenco presidi mobili </h3>

	<?php
	$query2 = "SELECT i.id,
	to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
	to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
	i.descrizione,
	to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
	to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
	to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
	i.note_ente,
	st.id_stato_sopralluogo,
	t.descrizione AS descrizione_stato
	FROM segnalazioni.t_sopralluoghi_mobili i
	JOIN segnalazioni.stato_sopralluoghi_mobili st ON st.id_sopralluogo = i.id
	JOIN segnalazioni.tipo_stato_sopralluoghi t ON t.id = st.id_stato_sopralluogo
	JOIN segnalazioni.join_sopralluoghi_mobili_squadra ii ON ii.id_sopralluogo = i.id
	JOIN users.v_squadre_all u ON u.id::text = ii.id_squadra::text
	JOIN eventi.t_eventi e ON e.id = i.id_evento
	WHERE i.id_evento=".$id."
	AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_sopralluoghi_mobili where id_sopralluogo =i.id) 
	GROUP BY i.id, data_invio, ora_invio, i.descrizione, time_preview, time_start, time_stop, note_ente, descrizione_stato, id_stato_sopralluogo;";
	//echo $query2 ."<br>";
	echo "<ul>";
	$result2 = pg_query($conn, $query2);
	while($r2 = pg_fetch_assoc($result2)) {
		echo "<li><h4> ";
		if($r2['id_stato_sopralluogo']==1){
			echo '<i class="fas fa-exclamation" title="Presidio inviato, ma non ancora preso in carico" style="color:#ff0000"></i>';
		} else if ($r2['id_stato_sopralluogo']==2){
			echo '<i class="fas fa-play" title="Presidio in lavorazione" style="color:#f2d921"></i>';
		} else if($r2['id_stato_sopralluogo']==3){
			echo '<i class="fas fa-check" title="Presidio chiuso" style="color:#5cb85c"></i>';
		}else if($r2['id_stato_sopralluogo']==4){
			echo '<i class="fas fa-exclamation" title="Presidio rifiutato" style="color:#ff0000"></i>';
		}
		echo " Presidio ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
		echo " - Descrizione Presidio: " .$r2['descrizione']." ";
		if ($r2['note_ente']!=''){
			echo ' - Note chiusura: '.$r2['note_ente'].' ';
		}
		echo "</h4>";
		if($r2['id_stato_sopralluogo']==4){
			$query_s="SELECT a.id, a.data_ora_invio as data_ora, a.data_ora_invio as data_ora_cambio, max(s.data_ora_stato) as time_stop, a.id_squadra::integer, b.nome
			FROM segnalazioni.t_sopralluoghi_mobili a
			JOIN users.t_squadre b ON a.id_squadra::integer = b.id::integer  
			JOIN segnalazioni.stato_sopralluoghi_mobili s ON s.id_sopralluogo=a.id
			WHERE a.id=".$r2['id']."
			GROUP BY a.id, a.data_ora_invio, a.id_squadra, b.nome";
		} else { 
			$query_s="SELECT a.id_sopralluogo, a.data_ora, a.data_ora_cambio, c.time_stop,a.id_squadra, b.nome 
			FROM segnalazioni.join_sopralluoghi_mobili_squadra a
			JOIN users.t_squadre b ON a.id_squadra=b.id 
			JOIN segnalazioni.t_sopralluoghi_mobili c ON c.id=a.id_sopralluogo
			WHERE id_sopralluogo =".$r2['id']." 
			ORDER BY data_ora";
		}
		//echo $query_s;
		echo "<ul>";
		require('./query_storico_squadre_incarichi.php');
		echo "</ul>";

	
	echo "</li>";
		}
		echo "</ul>";
	
?>
</div>  
</div>

<hr>
<div class="row">              
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h3>Elenco presidi fissi slegati da segnalazioni</h3>

	<?php
	$query2 = "SELECT i.id,
	to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
	to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
	i.descrizione,
	to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
	to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
	to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
	i.note_ente,
	st.id_stato_sopralluogo,
	jj.id_segnalazione_in_lavorazione,
	t.descrizione AS descrizione_stato
	FROM segnalazioni.t_sopralluoghi i
	JOIN segnalazioni.stato_sopralluoghi st ON st.id_sopralluogo = i.id
	JOIN segnalazioni.tipo_stato_sopralluoghi t ON t.id = st.id_stato_sopralluogo
	JOIN segnalazioni.join_sopralluoghi_squadra ii ON ii.id_sopralluogo = i.id
	JOIN users.v_squadre_all u ON u.id::text = ii.id_squadra::text
	JOIN eventi.t_eventi e ON e.id = i.id_evento
	LEFT JOIN segnalazioni.join_segnalazioni_sopralluoghi jj ON jj.id_sopralluogo=i.id
	WHERE i.id_evento=".$id." AND id_segnalazione_in_lavorazione is null 
	AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_sopralluoghi where id_sopralluogo =i.id) 
	GROUP BY i.id, data_invio, ora_invio, i.descrizione, time_preview, time_start, time_stop, note_ente, descrizione_stato, id_stato_sopralluogo, id_segnalazione_in_lavorazione;";
	//echo $query2 ."<br>";
	echo "<ul>";
	$result2 = pg_query($conn, $query2);
	while($r2 = pg_fetch_assoc($result2)) {
		echo "<li><h4> ";
		if($r2['id_stato_sopralluogo']==1){
			echo '<i class="fas fa-exclamation" title="Presidio inviato, ma non ancora preso in carico" style="color:#ff0000"></i>';
		} else if ($r2['id_stato_sopralluogo']==2){
			echo '<i class="fas fa-play" title="Presidio in lavorazione" style="color:#f2d921"></i>';
		} else if($r2['id_stato_sopralluogo']==3){
			echo '<i class="fas fa-check" title="Presidio chiuso" style="color:#5cb85c"></i>';
		}else if($r2['id_stato_sopralluogo']==4){
			echo '<i class="fas fa-exclamation" title="Presidio rifiutato" style="color:#ff0000"></i>';
		}
		echo " Presidio ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
		echo " - Descrizione Presidio: " .$r2['descrizione']." ";
		if ($r2['note_ente']!=''){
			echo ' - Note chiusura: '.$r2['note_ente'].' ';
		}
		echo "</h4>";
		if($r2['id_stato_sopralluogo']==4){
			$query_s="SELECT a.id, a.data_ora_invio as data_ora, a.data_ora_invio as data_ora_cambio, max(s.data_ora_stato) as time_stop, a.id_squadra::integer, b.nome
			FROM segnalazioni.t_sopralluoghi a
			JOIN users.t_squadre b ON a.id_squadra::integer = b.id::integer  
			JOIN segnalazioni.stato_sopralluoghi s ON s.id_sopralluogo=a.id
			WHERE a.id=".$r2['id']."
			GROUP BY a.id, a.data_ora_invio, a.id_squadra, b.nome";
		} else { 
			$query_s="SELECT a.id_sopralluogo, a.data_ora, a.data_ora_cambio, c.time_stop,a.id_squadra, b.nome 
			FROM segnalazioni.join_sopralluoghi_squadra a
			JOIN users.t_squadre b ON a.id_squadra=b.id 
			JOIN segnalazioni.t_sopralluoghi c ON c.id=a.id_sopralluogo
			WHERE id_sopralluogo =".$r2['id']." 
			ORDER BY data_ora";
		}
		//echo $query_s;
		echo "<ul>";
		require('./query_storico_squadre_incarichi.php');
		echo "</ul>";

	
	echo "</li>";
		}
		echo "</ul>";
	
?>
</div>  
</div>



  
<hr> 
<div class="row">              
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h3>Elenco provvedimenti cautelari </h3>
<table  id="pc_count" class="table table-condensed" 
style="word-break:break-all; word-wrap:break-word;" data-toggle="table" 
data-url="./tables/griglia_pc_report.php?id=<?php echo $id?>" 
data-show-export="false" data-search="false" data-click-to-select="false" 
data-pagination="false" data-sidePagination="false" data-show-refresh="true" 
data-show-toggle="false" data-show-columns="false" data-toolbar="#toolbar">

<thead>

<tr>
   <th data-field="tipo_provvedimento" data-sortable="false" data-visible="true" >Tipologia</th>
   <th data-field="descrizione_stato" data-sortable="true" data-visible="true">Stato</th>
   <th data-field="count" data-sortable="true" data-visible="true">Totale</th>
</tr>
</thead>
</table>
               
               <?php
               $query="SELECT sum(residenti) from segnalazioni.v_residenti_allontanati 
               where id_evento=".$id.";";
               $result = pg_query($conn, $query);
					while($r = pg_fetch_assoc($result)) {
						echo "<br><br><b>Residenti allontanati in questo momento:</b>".$r['sum']."<br><br>";
					}
                
                
				?>
				
				
				
				<hr>
				
				
	<?php
	$query2 = "SELECT i.id,
	to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
	to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
	i.descrizione,
	to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
	to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
	to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
	i.note_ente,
	tt.descrizione AS tipo,
	o.descrizione as descrizione_uo,
	st.id_stato_provvedimenti_cautelari,
	t.descrizione AS descrizione_stato,
	i.rimosso,
	to_char(h.data_ora_stato, 'DD/MM/YYYY'::text) AS data_rimozione,
	to_char(h.data_ora_stato, 'HH24:MI'::text) AS ora_rimozione
	FROM segnalazioni.t_provvedimenti_cautelari i
	JOIN segnalazioni.stato_provvedimenti_cautelari st ON st.id_provvedimento = i.id
	JOIN segnalazioni.tipo_stato_provvedimenti_cautelari t ON t.id = st.id_stato_provvedimenti_cautelari
	JOIN users.tipo_origine_provvedimenti_cautelari o ON i.id_uo::integer=o.id
	JOIN segnalazioni.tipo_provvedimenti_cautelari tt ON tt.id=i.id_tipo 
	JOIN eventi.t_eventi e ON e.id = i.id_evento
	LEFT JOIN segnalazioni.t_ora_rimozione_provvedimenti_cautelari h ON i.id=h.id_provvedimento
	WHERE i.id_evento=".$id." 
	AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_provvedimenti_cautelari where id_provvedimento =i.id) 
	GROUP BY i.id, data_invio, ora_invio, i.descrizione, time_preview, time_start, time_stop, note_ente, descrizione_stato, descrizione_uo, id_stato_provvedimenti_cautelari, 
	tipo, rimosso, data_rimozione, ora_rimozione 
	ORDER BY tipo, i.data_ora_invio ;";
	//echo $query2 ."<br>";
	echo "<ul>";
	$result2 = pg_query($conn, $query2);
	while($r2 = pg_fetch_assoc($result2)) {
		echo "<li><h4> ";
		if($r2['id_stato_provvedimenti_cautelari']==1){
			echo '<i class="fas fa-exclamation" title="Provvedimento inviato, ma non ancora preso in carico" style="color:#ff0000"></i>';
		} else if ($r2['id_stato_provvedimenti_cautelari']==2){
			echo '<i class="fas fa-play" title="Provvedimento in lavorazione" style="color:#f2d921"></i>';
		} else if($r2['id_stato_provvedimenti_cautelari']==3){
			echo '<i class="fas fa-check" title="Provvedimento chiuso" style="color:#5cb85c"></i>';
		}else if($r2['id_stato_provvedimenti_cautelari']==4){
			echo '<i class="fas fa-exclamation" title="Provvedimento rifiutato" style="color:#ff0000"></i>';
		}
		echo " ".$r2['tipo']." - ".$r2['descrizione_stato']." assegnato il " .$r2['data_invio']. " alle " .$r2['ora_invio']. " ";
		echo "da: " .$r2['descrizione_uo']." - Descrizione Provvedimento: " .$r2['descrizione']." ";
		if ($r2['note_ente']!=''){
			echo ' - Note chiusura: '.$r2['note_ente'].' ';
		}
		if ($r2['rimosso']=='t'){
			echo ' - Rimosso alle ore '.$r2['ora_rimozione'].' del '.$r2['data_rimozione'];
		}
		echo "</h4>";
		
		//*********************************************************************************************************
		// cerco gli incarichi associati a quel provvedimento cautelare
		
		/*if ($r['conteggio_incarichi']>0){
		if ($r['conteggio_incarichi']==1){
			echo ' <h4> '.$r['conteggio_incarichi'].' incarico assegnato </h4>';
		} else {
			echo ' <h4> '.$r['conteggio_incarichi'].' incarichi assegnati </h4>';
		}*/
		$query3 = "SELECT i.id,
		to_char(i.data_ora_invio, 'DD/MM/YYYY'::text) AS data_invio,
		to_char(i.data_ora_invio, 'HH24:MI'::text) AS ora_invio,
		i.descrizione,
		u.descrizione AS descrizione_uo,
		to_char(i.time_preview, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_preview,
		to_char(i.time_start, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_start,
		to_char(i.time_stop, 'DD/MM/YYYY HH24:MI:SS'::text) AS time_stop,
		i.note_ente,
		i.note_rifiuto,
		st.id_stato_incarico,
		t.descrizione AS descrizione_stato,
		st.parziale
	   FROM segnalazioni.t_incarichi i
		 JOIN segnalazioni.join_incarico_provvedimenti_cautelari j ON j.id_incarico=i.id
		 JOIN segnalazioni.stato_incarichi st ON st.id_incarico = i.id
		 JOIN segnalazioni.tipo_stato_incarichi t ON t.id = st.id_stato_incarico
		 JOIN varie.v_incarichi_mail u ON u.cod = i.id_uo::text 
		WHERE j.id_provvedimento= ".$r2['id']." AND st.data_ora_stato = (select max(data_ora_stato) from segnalazioni.stato_incarichi where id_incarico =i.id) 
		GROUP BY i.id, data_invio, ora_invio, i.descrizione, descrizione_uo, time_preview, time_start, time_stop, note_ente, note_rifiuto, descrizione_stato,parziale, id_stato_incarico;";
		//echo $query3 ."<br>";
		echo "<ul>";
		$result3 = pg_query($conn, $query3);
		while($r3 = pg_fetch_assoc($result3)) {
			echo "<li> ";
			if($r3['id_stato_incarico']==1){
				echo '<i class="fas fa-exclamation" title="Incarico inviato, ma non ancora preso in carico" style="color:#f2d921"></i>';
			} else if ($r3['id_stato_incarico']==2){
				echo '<i class="fas fa-play" title="Incarico in lavorazione" style="color:#f2d921"></i>';
			} else if($r3['id_stato_incarico']==3){
				echo '<i class="fas fa-check" title="Incarico chiuso" style="color:#5cb85c"></i>';
			}else if($r3['id_stato_incarico']==4){
				echo '<i class="fas fa-exclamation" title="Incarico rifiutato" style="color:#ff0000"></i>';
			}
			echo " Incarico ".$r3['descrizione_stato']." assegnato il " .$r3['data_invio']. " alle " .$r3['ora_invio']. " ";
			echo "a ".$r3[descrizione_uo]." <br>Descrizione incarico: " .$r3['descrizione']." ";
			if ($r3['note_ente']!=''){
				echo ' - Note chiusura: '.$r3['note_ente'].' ';
			}
			if ($r3['note_rifiuto']!=''){
				echo ' - Note rifiuto: '.$r3['note_rifiuto'].' ';
			}
			if ($r3['parziale']=='t'){
				echo ' - <i class="fas fa-exclamation"></i> Incarico eseguito solo in parte';
			}
			echo "</li>";
		}
		echo "</ul>";
	/*} else {
		echo 'Nessun incarico assegnato - ';
	}*/
		
		
		

	echo '<a class="btn btn-info noprint" target="_new" href="dettagli_provvedimento_cautelare.php?id='.$r2["id"].'"> Visualizza dettagli in nuova scheda</a>';
	echo "</li>";
		}
		echo "</ul>";
	
?>
            </div>
                <!-- /.col-sm-4 -->
            </div>
            <!-- /.row -->
            <div class="row">
                
                
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php
$date = date_create(date(), timezone_open('Europe/Berlin'));
$data = date_format($date, 'd-m-Y');
$ora = date_format($date, 'H:i');
//$data = date("d-m-Y");
//$ora = date("H:i:s");
	echo "<hr><div align='center'>Il presente report è stato ottenuto in maniera automatica utilizzando il Sistema 
	di Gestione delle Emergenze in data ".$data ." alle ore " .$ora.". 
	</div>";

?>
             </div>
            </div> <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php 

require('./footer.php');

require('./req_bottom.php');


?>

<script>

	/*var mymap = L.map('mapid').setView([44.411156, 8.932661], 12);

	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(mymap);

	L.marker([44.411156, 8.932661]).addTo(mymap)
		.bindPopup("<b>Hello world!</b><br />I am a leafletJS popup.").openPopup();




	var popup = L.popup();

	function onMapClick(e) {
		popup
			.setLatLng(e.latlng)
			.setContent("You clicked the map at " + e.latlng.toString())
			.openOn(mymap);
	}

	mymap.on('click', onMapClick);*/



  
$(document).ready(function() {
    $('#js-date').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date2').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
      $('#js-date3').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date4').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });  
     $('#js-date5').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date6').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
      $('#js-date7').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date8').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });  
    $('#js-date9').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date10').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });  
    
    
    $('#js-date12').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    $('#js-date13').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    });
    
    
    $('#js-date100').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true
    }); 
});

</script>
    

</body>

</html>