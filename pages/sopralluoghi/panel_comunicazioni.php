<?php
$query_comunicazioni="SELECT *";
$query_comunicazioni= $query_comunicazioni." FROM segnalazioni.v_comunicazioni_sopralluoghi 
WHERE id=".$id. ";";
//echo $query_comunicazioni;
$result_comunicazioni=pg_query($conn, $query_comunicazioni);
$check_messaggi_notifica=0;
while($r_comunicazioni = pg_fetch_assoc($result_comunicazioni)) {
	$check_messaggi_notifica=$check_messaggi_notifica+1;
}
?>


<div class="panel-group">
			  <div class="panel panel-success">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" href="#list_comunicazioni"><i class="fa fa-comments"></i> Comunicazioni presidio </a>
			        <?php if ($check_messaggi_notifica > 0 ){ 
			        echo "( "; 
			        ?>
			        <i class="fas fa-envelope faa-ring animated" style="color:#ff0000"></i>
			        <?php 
			        echo " ".$check_messaggi_notifica. ")"; 
			         } ?>
			      </h4>
			    </div>
			    <div id="list_comunicazioni" class="panel-collapse collapse">
			      <div class="panel-body"-->
				<?php
				// cerco l'id_lavorazione
				$query_comunicazioni="SELECT *";
				$query_comunicazioni= $query_comunicazioni." FROM segnalazioni.v_comunicazioni_sopralluoghi WHERE id=".$id. ";";
				
				
				//echo $query_comunicazioni;
				
				//echo $query_comunicazioni;
				$result_comunicazioni=pg_query($conn, $query_comunicazioni);
				$check_messaggi_notifica=0;
				$testo="";
				while($r_comunicazioni = pg_fetch_assoc($result_comunicazioni)) {
					$check_messaggi_notifica=$check_messaggi_notifica+1;
					if ($check_messaggi_notifica>0){
						$testo= $testo. "<hr>";
					}
					$i=$i+1;
					$testo= $testo. "<i class=\"fa fa-comment\"></i> ". $r_comunicazioni['data_ora_stato'];
					$testo= $testo. " - Da " .$r_comunicazioni['mittente']. " a ". $r_comunicazioni['destinatario'];
					$testo= $testo. " : " .$r_comunicazioni['testo'];
					if ($r_comunicazioni['allegato']!=''){
						$allegati=explode(";",$r_comunicazioni['allegato']);
						// Count total files
						$countfiles = count($allegati);
						// Looping all files
						if($countfiles > 0) {
							for($i=0;$i<$countfiles;$i++){
								$n_a=$i+1;
								//$testo= $testo. ' - <a href="../../'.$allegati[$i].'"> Allegato '.$n_a.'</a>';
								if(@is_array(getimagesize('../../'.$allegati[$i]))){
									//$image = true;
									$testo= $testo. '<br><img src="../../'.$allegati[$i].'" alt="'.$allegati[$i].'" width="30%"> 
									<a target="_new" title="Visualizza immagine in nuova scheda" href="../../'.$allegati[$i].'"> Apri immagine'.$n_a.'</a>';
								} else {
									//$image = false;
									$testo= $testo. '<br><a target="_new" href="../../'.$allegati[$i].'"> Apri allegato '.$n_a.' in nuova scheda</a>';
								}
							}
						}
					}
				}
				echo $testo;
				//OLD
				/*$result_comunicazioni=pg_query($conn, $query_comunicazioni);
				$check_messaggi_notifica=0;
				$i=0;
				while($r_comunicazioni = pg_fetch_assoc($result_comunicazioni)) {
					if ($i>0){
						echo "<hr>";
					}
					$i=$i+1;
					echo "<i class=\"fa fa-comment\"></i> ". $r_comunicazioni['data_ora_stato'];
					echo " - Da " .$r_comunicazioni['mittente']. " a ". $r_comunicazioni['destinatario'];
					echo " : " .$r_comunicazioni['testo'];
					if ($r_comunicazioni['allegato']!=''){
						echo '<a href="../../'.$r_comunicazioni['allegato'].'"> Allegato </a>';
					}
					//echo " - <a class=\"btn btn-info\" href=\"dettagli_incarico.php?id=".$r_comunicazioni['id']."\"> <i class=\"fas fa-info\"></i> Dettagli</a>";
				}*/
				
	
	
				?>
			
			
			</div>
    </div>
  </div>
</div>


<?php

?>