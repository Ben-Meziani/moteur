<?php
use Core\Config;
require_once("../config/config.php");
$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal){
}
?>
<br/>
<div class="container loginClass">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <h5 class="card-title text-center"><?php echo htmlspecialchars($arrayConfigVal['name_projet']); ?></h5>
                    <?php if(!empty($errors) && $errors != null): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach($errors->errors as $error): ?>
                                    <?php if($error != null){ ?>
                                        <li style="list-style: none; margin-left: -15px;"><?php echo $error;?></li>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="form-signin" method="post" action="index.php?p=Users.login">
                        <div class="form-label-group">
                           <label for="identifiant">Identifiant : </label> <input type="text" id="identifiant" name="identifiant" class="form-control"/>
                        </div>
                        <br/>
                        <div class="form-label-group">
                            <label for="password">Mot de passe  : </label> <input type="password" id="password" name="password" class="form-control"/>
                        </div>
                        <br/>
                        <button class="btn btn-lg btn-danger btn-block text-uppercase" type="submit">Connexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>