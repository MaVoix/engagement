<?php


$aResponse = array();
$aResponse["type"] = "message";

$aResponse["message"] = array();
$aResponse["message"]["title"]="Erreur";
$aResponse["message"]["type"]="error";
$aResponse["message"]["text"]="Tous les champs suivi de * sont obligatoires !";
$aResponse["durationMessage"] = "3000";
$aResponse["durationRedirect"] = "1";
$aResponse["durationFade"] = "500";
$aResponse["required"] = array();

$nError = 0;

//check edit KEY
$bEdit=false;
$OldGroup=new Group();
if(isset($_POST["id"]) && isset($_POST["key"])){
    $oListeGroup=new GroupListe();
    $oListeGroup->applyRules4Key($_POST["key"],$_POST["id"]);
    $aGroups=$oListeGroup->getPage();
    if(count($aGroups)==1){
        $bEdit=true;
        $OldGroup=new Group(array("id"=>$aGroups[0]["id"]));
        $OldGroup->hydrateFromBDD(array('*'));
    }else{

        $nError++;
        $aResponse["message"]["text"] = "Impossible de modifier ce groupe.";
    }
}


//mandatory fields
$aMandoryFields=array("group_name");

//ajoute les engagements si l'utilisateur n'est pas admin
if($oMe->getType()!="admin"){
    $aEngagements=array();
    $aMandoryFields=array_merge($aMandoryFields,$aEngagements);
}


foreach($aMandoryFields as $sField){
    if (!isset($_POST[$sField]) || $_POST[$sField] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => $sField));
        $_POST[$sField]="";
    }
}

/*
if(ConfigService::get("enable-captcha")){
    if (!isset($_POST["captcha"]) || $_POST["captcha"] == "") {
        $nError++;
        array_push($aResponse["required"], array("field" => "captcha"));
        $_POST["captcha"]="";
    }else{
        if(SessionService::get("captcha-value") !=  $_POST["captcha"]){
            $nError++;
            $aResponse["message"]["text"] = "Le code de sécurité est incorrect.";
            $_POST["captcha"]="";
        }
    }
}*/



//check upload picture
/*$aLimitMime = ConfigService::get("mime-type-limit");
$aMime = array_keys(ConfigService::get("mime-type-limit"));

if ($nError == 0) {
    if (!isset($_POST["imageFilename"]) || $_POST["imageFilename"] == "") {
        $nError++;
        $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
    }
    if (!isset($_POST["imageData"]) || $_POST["imageData"] == "") {
        $nError++;
        $aResponse["message"]["text"] = "N'oubliez pas d'envoyer votre photo !";
    }
}

$sExtension="jpg";
if ($nError == 0 ) {
    //Add base 64 encode data in FILE "image"
    if(!isset($_FILES)){
        $_FILES=array("image"=>array());
    }
    $sExtension=strtolower(substr($_POST["imageFilename"],-3));
    if($sExtension=="peg"){
        $sExtension="jpg";
    }
    $_FILES["image"]["tmp_name"]= '../tmp/'.md5(rand(1000,99999).time().ConfigService::get("key")).'.'.$sExtension;
    $_FILES["image"]["name"]=$_POST["imageFilename"];
    $encodedData = explode(',', $_POST["imageData"]);
    $decodedData = base64_decode($encodedData[1]);
    file_put_contents($_FILES["image"]["tmp_name"], $decodedData ) ;
}

if ($nError == 0 ) {
    if (!in_array(mime_content_type($_FILES['image']['tmp_name']), $aMime)) {
        $nError++;
        $aResponse["message"]["text"] = "Format de fichier de votre photo non reconnu.";
    }
}
if ($nError == 0) {
    if (filesize($_FILES['image']['tmp_name']) > ConfigService::get("max-filesize") * 1024 * 1024) {
        $nError++;
        $aResponse["message"]["text"] = "Votre photo dépasse le poids maximum autorisé. (" . ConfigService::get("max-filesize") . " Mb )";
    }
}


if ($nError == 0) {
    //format de l'image
    $img = new claviska\ SimpleImage($_FILES['image']['tmp_name']);
    if (
        $img->getWidth() < ConfigService::get("min-width") || $img->getWidth() > ConfigService::get("max-width") ||
        $img->getHeight() < ConfigService::get("min-height") || $img->getHeight() > ConfigService::get("max-height")
    ) {
        $nError++;
        $aResponse["message"]["text"] = "Les dimensions de votre photo ne sont pas valides ( entre ".ConfigService::get("min-width")."px et ".ConfigService::get("max-height")."px )";
    }

}*/


if($nError==0){
    if($bEdit){
        $Group=new Group(array("id"=>$OldGroup->getId()));
        $Group->setDate_amended(date("Y-m-d H:i:s"));
        $OldGroup->hydrateFromBDD(array('*'));
    }else{
        $Group=new Group();
        $Group->setDate_created(date("Y-m-d H:i:s"));
        //generate key for link
        $sKey=sha1($_SERVER["REMOTE_ADDR"].ConfigService::get("key").rand(1000,9999).time());
        $Group->setKey_edit($sKey);
        $Group->setState("offline");
    }

    //force le mode offline sur l'enregistrement par un utilisateur
    if($oMe->getType()!="admin"){
        $Group->setState("offline");
    }

    $Group->setName($_POST["group_name"]);


    //save Files
    /*$outputDir = "data/" . date("Y") . "/" . date("m") . "/" . date("d") . "/". time() . session_id() . "/";
    mkdir($outputDir, 0777, true);
    $outputFilePhoto= $outputDir."original.".$sExtension;

    $outputFilePhotoFit= $outputDir."photo-fit.jpg";
    if (@copy($_FILES['image']['tmp_name'], $outputFilePhoto)) {
        $img = new claviska\ SimpleImage($outputFilePhoto);
        $img->bestFit(800, 800);
        $img->toFile($outputFilePhotoFit, "image/jpeg", 100);
        $Group->setPath_pic($outputFilePhoto);
    }else{
        $aResponse["message"]["text"] = "Erreur lors de l'enregistrement de votre photo.";
        $nError++;
    }
    @unlink($_FILES['image']['tmp_name']);*/



    if( $nError==0){

        $Group->save();

       /* $TwigEngine = App::getTwig();
        $sBodyMailHTML = $TwigEngine->render("visitor/mail/body.html.twig", [
            "group" => $Group
        ]);
        $sBodyMailTXT = $TwigEngine->render("visitor/mail/body.txt.twig", [
            "group" => $Group
        ]);
        if(!$bEdit) {
          Mail::sendMail($Group->getEmail(), "Confirmation de group", $sBodyMailHTML, $sBodyMailTXT, true);
        }*/

        if($oMe->getType()=="admin"){
            $aResponse["message"]["text"] = "Informations enreigistrées correctement !";
            $aResponse["redirect"] = "/groupe/list.html";
        }else{
            $aResponse["message"]["text"] = "Félicitations !";
            $aResponse["redirect"] = "/groupe/felicitation.html";
        }

        SessionService::set("last-save-id",$Group->getId());

        $aResponse["durationMessage"] = "2000";
        $aResponse["durationRedirect"] = "2000";
        $aResponse["durationFade"] = "10000";
        $aResponse["message"]["title"] = "";
        $aResponse["message"]["type"] = "success";
        //if edit clean old file
        /*if($bEdit){
            @unlink($OldGroup->getPath_pic());
            $aResponse["message"]["text"] = "Modification enregistrée !";
        }else{
            $aResponse["message"]["text"] = "Group envoyée correctement !";
        }*/

    }

}


//return
$aDataScript['data'] = json_encode($aResponse);