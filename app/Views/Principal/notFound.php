<br/>


 <?php  if(isset($_GET['erreurConf']) && !empty($_GET['erreurConf'])){


    $erreur = unserialize($_GET['erreurConf']);

     foreach($erreur as $err => $e){

         ?>  <p><?php echo htmlspecialchars($err) ;?> est <?php echo htmlspecialchars($e);?>.</p> <?php
     }

 }else{ ?>
        <p>La page demandée est introuvable.</p>
   <?php } ?>



