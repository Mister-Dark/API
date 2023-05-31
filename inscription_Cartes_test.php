<?php
require("cnx.php");
require("Fonctions/Fonction.php");
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
header('Access-Control-Allow-Headers:*');
header('Content-Type:application/json');
require 'vendor/autoload.php';
//require("TransAc_Orange_paiement_ok.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
//$url = "https://marchand.maishapay.online/api/payment/rest/vers1.0/merchant";

$created_at      = date("Y-m-d H:i:s");
$last_update     = date("Y-m-d H:i:s");
$lelo            = date("Y-m-d H:i:s");
$gatewayMode          = $_POST["gatewayMode"];
//$amount               = "10";//$_POST["amount"];
$currency             = $_POST["currency"];
$customerFullName     = $_POST["customerFullName"];
$customerPhoneNumber  = $_POST["customerPhoneNumber"];
$customerEmailAddress = $_POST["customerEmailAddress"];
$chanel               = $_POST["chanel"];
$provider             = $_POST["provider"];
$walletID             = $_POST["walletID"];
$IDetudiant           = $_POST["IDetudiant"];
$IDabonnement         = $_POST["IDabonnement"];
$tagCarte             =mysqli_real_escape_string($mysqli,$_POST['tagCarte']);
$IdAgent              = mysqli_real_escape_string($mysqli,$_POST["IdAgent"]);
$phone                = $_POST["telephone"];

$codeDate = date("Ymd");
$code_unique =strtoupper(createId('STDTAC'.$codeDate));
//$transactionReference =$code_unique;
$transactionReference = createIdTRS('STDTAC');

$queryCheckCart="SELECT * FROM rf_card WHERE rf_card.number ='$tagCarte' LIMIT 1";
    $resultatCheck=mysqli_query($mysqli,$queryCheckCart);
    while ($rowCarte=mysqli_fetch_array($resultatCheck)) 
    {
	//$row20=mysqli_fetch_array($ResultatQueryRf);
    $statutActivation = $rowCarte['status_activation']; 
	$statutAttribution =$rowCarte['status_attribution'];
	}


if($statutActivation=="ACTIVED")
    {
	
	if($statutAttribution=="NON")
    {
	
$queryCheckEtudiant="SELECT Id_etudiant_identification FROM op_etudiant WHERE op_etudiant.Id_etudiant_identification ='$IDetudiant'";
    $ResultatCheckEtudiant=mysqli_query($mysqli, $queryCheckEtudiant);
    $count1=mysqli_num_rows($ResultatCheckEtudiant);
    if($count1 >0)
    {
    $arr=['msg'=>'vous avez deja un compte','status'=>201];
    echo json_encode($arr);
    }else{

    $query_checkUser="SELECT name FROM sys_user WHERE name ='$phone'";
    $rs_check=mysqli_query($mysqli, $query_checkUser);
    $count_rs=mysqli_num_rows($rs_check);
    if($count_rs >0)
    {
      $arr=['msg'=>'ce numero est déjà utilise','status'=>401];
      echo json_encode($arr);
    }else{
		
	$QueryRfs="SELECT rf_etudiant_identification.nom,rf_etudiant_identification.postnom,rf_etudiant_identification.prenom FROM rf_etudiant_identification WHERE rf_etudiant_identification.Id='$IDetudiant'";
    $ResultatQueryRf=mysqli_query($mysqli,$QueryRfs);
	while ($row20=mysqli_fetch_array($ResultatQueryRf)) 
    {
	//$row20=mysqli_fetch_array($ResultatQueryRf);
    $nom = $row20["nom"]; 
	$noms= mysqli_real_escape_string($mysqli, $nom);
	$postnoms =$row20['postnom'];
	$postnom= mysqli_real_escape_string($mysqli, $postnoms);
	$prenoms = $row20['prenom']; 
	$prenom  = mysqli_real_escape_string($mysqli, $prenoms);
	}
	$queryCreateEtudiants=mysqli_query($mysqli,"INSERT INTO `op_etudiant` (`Id`, `nom`, `prenom`, `postnom`, `sexe`, `lieuNaissance`, `Telephone`, `matricule`, `avenue`, `numero`, `email`, `photo`, `DateNaissance`, `last_update`, `created_at`, `Id_user_created_at`, `Id_etudiant_identification`, `Id_adresse`, `Id_promotion`, `code_generate_tac`, `CodeUnique`, `source`) VALUES (NULL, '$noms', '$prenom', '$postnom', 'NULL', 'NULL', '$phone', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', current_timestamp(), '$IdAgent', '$IDetudiant', '0', '0', '$tagCarte', 'NULL', 'NULL')")  or die($mysqli->error.__LINE__);
if($queryCreateEtudiants){
$queryUpdateCarte=mysqli_query($mysqli,"UPDATE `rf_card` SET `date_attribution` = '$lelo', `status_attribution` = 'YES', `is_user_attributed` = '$IdAgent' WHERE `rf_card`.`number` = '$tagCarte'")  or die($mysqli->error.__LINE__);
	
$queryUpdateE=mysqli_query($mysqli,"UPDATE rf_etudiant_identification SET status= 'USED' WHERE rf_etudiant_identification.Id = '$IDetudiant'")  or die($mysqli->error.__LINE__);	
}	

$queryCreateUser="insert into sys_user set id_generate_tac='$tagCarte', name='$phone', pwd='Trans@106', last_update='$last_update', created_at='$created_at', Id_user_created_at='$IdAgent', statut='pending'"or die($mysqli->error.__LINE__);
    $ResultatCreate=mysqli_query($mysqli,$queryCreateUser)or die($mysqli->error.__LINE__);
    if($queryCreateUser){
	$queryLastID="SELECT MAX(Id) AS lastId FROM sys_user";
    $reseltLastID=mysqli_query($mysqli,$queryLastID) or die($mysqli->error.__LINE__);
    $row=mysqli_fetch_array($reseltLastID);
    $lastId=$row['lastId'];
		
    $QueryRole=mysqli_query($mysqli,"insert into sys_users_roles set Id_users ='$lastId', Id_roles='3', last_update='$last_update', created_at='$created_at', id_user_created_at='$IdAgent'")  or die($mysqli->error.__LINE__);
	
	

if($currency=='USD'){
    $k36="SELECT a.Id Id, Type_abonnement TYPE,Duree_abonnement Duree,taux_change,ROUND(prix/taux_change,1)prix
    FROM db_academia.op_abonnement a INNER JOIN db_academia.sys_currency c ON c.Id =a.id_currency WHERE a.Id = '$IDabonnement'";
}
elseif($currency=='CDF'){
    $k36="SELECT a.Id Id, Type_abonnement TYPE,Duree_abonnement Duree,taux_change,prix,a.id_currency FROM db_academia.op_abonnement a 
    INNER JOIN db_academia.sys_currency c ON c.Id =a.id_currency WHERE a.Id = '$IDabonnement'";
}

$r36=mysqli_query($mysqli,$k36);
while ($row36 = mysqli_fetch_object($r36))
{ 
$prix_abonnement = $row36->prix;
$dure_abonnement = $row36->Duree_abonnement;
}
$amount        = $prix_abonnement;

$k3="SELECT Id,code_generate_tac  FROM op_etudiant WHERE op_etudiant.Id_etudiant_identification  ='$IDetudiant'";
$r3=mysqli_query($mysqli,$k3);
while ($row3 = mysqli_fetch_object($r3))
{ 
$IDetudiants = $row3->code_generate_tac;
$Identifiant = $row3->Id;
}
	
$getLastId ="SELECT Id AS Idlast FROM op_abonnement WHERE Type_abonnement='prelevement'";
$Idlast = $mysqli->query($getLastId) or die($mysqli->error.__LINE__);
while ($row = mysqli_fetch_object($Idlast))
{ 
$Idabonnement = $row->Idlast;                                                       
}  
//echo $Idabonnement;
$k1="SELECT Id  FROM rf_operateur_paiement WHERE rf_operateur_paiement.libele  ='$provider'" or die($mysqli->error.__LINE__);
$r1=mysqli_query($mysqli,$k1)or die($mysqli->error.__LINE__);
while ($rows = mysqli_fetch_object($r1))
{ 
$Idope = $rows->Id;                                                       
}

$k2="SELECT Id FROM sys_currency WHERE sys_currency.format_key='$currency'";
$r2=mysqli_query($mysqli,$k2);
while($row2 = mysqli_fetch_object($r2))
{ 
$IdCurence = $row2->Id;                                                       
}  

$q_transaction = "INSERT INTO `op_transaction` (`Id`, `Id_op`, `Id_currency`, `Id_abonnement`, `Id_user_created_at`, `id_config_api`, `libele_tac`, `status_tac`, `last_update`, `created_at`, `gatewayMode_qm`, `publicApiKey_qm`, `secretApiKey_qm`, `transactionReference_qm`, `amount_qm`, `customerFullName_qm`, `customerPhoneNumber_qm`, `customerEmailAddress_qm`, `chanel_qm`, `provider_qm`, `walletID_rm`, `statusCode_rm`, `status_rm`, `transactionDate_rm`, `transactionDescription_rm`, `transactionId_rm`, `mobilenumber_qw`, `merchantid_qw`, `invoiceid_qw`, `terminalid_qw`, `encryptkey_qw`, `securityparams_gpslatitude_qw`, `securityparams_gpslongitude_qw`, `otp_qw`, `referencenumber_rw`, `stan_rw`, `rrn_rw`, `tranauthid_rw`, `respcode_rw`, `respcodedesc_rw`, `id_generate_tac`, `source`)VALUES (NULL, '$Idope', '$IdCurence', '$IDabonnement', '$IdAgent', NULL, 'Prelevement', 'PENDING', '$last_update', '$created_at', '$gatewayMode', NULL, NULL, '$referenceTransaction', '$amount', NULL, NULL, NULL, '$chanel', '$provider', '$walletID', NULL, 'PENDING ', '$lelo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$tagCarte', 'mobile')"or die(mysqli_error()); 
$ResultatQueryTransaction=mysqli_query($mysqli,$q_transaction)or die($mysqli->error.__LINE__);

if($ResultatQueryTransaction){
//Insertion dans Op_check_course	
$q300="SELECT id_customer FROM cb_check_courses WHERE id_customer ='$tagCarte'";
$rs300=mysqli_query($mysqli, $q300);
$count1=mysqli_num_rows($rs300);
if($count1 >0)
{
$arr=['msg'=>'CE Compte existe déjà','status'=>206];
echo json_encode($arr);
}else{
$QueryCourse="INSERT INTO `cb_check_courses` (`id_customer`, `id_abonnement`, `date_expired_abo`, `date_abonnement`, `validity_abonnement`, `status_day`, `status_month`, `count_courses_day`, `fixed_courses_abo`) VALUES ('$tagCarte', '', '', '', '', '', '', '', '')";
$r200=mysqli_query($mysqli,$QueryCourse);
}
}

	

if($provider =='ORANGE'){
	//echo 'ORANGE'."\n";

	//header('Location: echecpaye.php');


	$response = orangemoney($amount,$transactionReference,$currency,$walletID);
	// print_r($response);
	 $decoded_json = json_decode($response, true);
	
     global $resultDesc1;
     global $montant1;
     global $refPayments1;
     global $resultCodes1;
     global $transIds1;
     global $txnstatut1;
	
	 $resultDesc1 =$resultDesc=$decoded_json['resultDesc'];
	 $montant1 =$montant=$decoded_json['montant'];
	 $refPayments1 =$refPayments =$decoded_json['refPayment'];
	 $resultCodes1 =$resultCodes=$decoded_json['resultCode'];
	  $transIds1 =$transIds=$decoded_json['transId'];
	 $txnstatut1 =$txnstatut=$decoded_json['txnstatus'];
	
	
	
	
	if($txnstatut1==200){
		//print_r($response);
		$leloU            = date("Y-m-d H:i:s");
	$updateTransactionOK="UPDATE op_transaction SET status_tac = 'valider', amount_qm = '$montant1',  last_update='$leloU',statusCode_rm = '$resultCodes1', status_rm = 'APPROVED' ,transactionId_rm='$refPayments1', transactionDescription_rm = '$resultDesc1' WHERE transactionReference_qm ='$transIds1'"  or die($mysqli->error.__LINE__);
$resultatTransactionOK=$mysqli->query($updateTransactionOK)or die($mysqli->error.__LINE__);
if($resultatTransactionOK){
$Q100="UPDATE sys_user SET statut= 'activer' WHERE sys_user.id_generate_tac = '$tagCarte'";
$r100=$mysqli->query($Q100)or die($mysqli->error.__LINE__);	
	
//$q300="SELECT * FROM cb_check_courses WHERE id_customer ='$tagCarte'";
//$rs300=mysqli_query($mysqli, $q300);
//$count1=mysqli_num_rows($rs300);
//if($count1 >0)
//{
//$arr=['msg'=>'vous avez deja un compte ok','status'=>206];
//echo json_encode($arr);
//}else{
//$k200="INSERT INTO `cb_check_courses` (`id_customer`, `id_abonnement`, `date_expired_abo`, `date_abonnement`, `validity_abonnement`, `status_day`, `status_month`, `count_courses_day`, `fixed_courses_abo`) VALUES ('$tagCarte', '', '', '', '', '', '', '', '')";
//$r200=mysqli_query($mysqli,$k200);
//}
	
  if($Q100){
	 
     $sender="Trans-acad"; 
    $tel = $_POST["walletID"];
    $msg = urlencode("Bravo! Compte créé avec succès.Veuillez passer au box pour retrait de la carte. Cliquez-ici : www.trans-academia.cd ou tapez *481#");

    //file_get_contents("http://rslr.connectbind.com:8080/bulksms/bulksms?username=Injo-sinainfo&password=@Wqywv9E&type=0&dlr=1&destination=".$tel."&source=".$sender."&message=".$msg);
	  file_get_contents("http://rslr.connectbind.com:8080/bulksms/bulksms?username=Injo-sinainfo&password=@Wqywv9EES&type=0&dlr=1&destination=".$tel."&source=".$sender."&message=".$msg);
    //echo json_encode(array("flag"=>"1","Id"=>$IDetudiants));
	  //header('Location:AKADEMIA/index.php');
	  
	
	//header('Location:http://app.web.trans-academia.cd/index.php');  
  }
  else{
   // echo json_encode(array("flag"=>"0"));
  }
	
}
		
    //echo $montant1."\n";
	//echo $refPayments."\n";
	//echo $resultCodes."\n";
	//echo $transIds."\n";
	//echo $txnstatut."\n";
	 $arr=['msg'=>'Transaction effectuer avec succees','status'=>200];
    echo json_encode($arr);	
		
	}elseif($resultDesc1=='Push envoyé'){
	//print_r($response);
	$leloU            = date("Y-m-d H:i:s");
	$updateTransactionKO="UPDATE op_transaction SET status_tac = 'ECHEC', last_update='$leloU', statusCode_rm = '10009', status_rm = 'DECLINED' ,transactionId_rm='$transIds1', transactionDescription_rm = '$resultDesc1' WHERE transactionReference_qm ='$transIds1'"  or die($mysqli->error.__LINE__);
$resultatTransactionKO=$mysqli->query($updateTransactionKO)or die($mysqli->error.__LINE__);
		
if($resultatTransactionKO){
$Q100="UPDATE sys_user SET statut= 'pending' WHERE sys_user.id_generate_tac = '$tagCarte'";
$r100=$mysqli->query($Q100)or die($mysqli->error.__LINE__);
//echo "transaction echouer :".$transIds."\n"; 
	
}
$arr=['msg'=>'Transaction echouer avec succées:'.$transIds.'\n','status'=>400];
echo json_encode($arr);		
		
	}elseif($resultDesc1=='echec transaction'){
	//print_r($response);
	$leloU            = date("Y-m-d H:i:s");
	$updateTransactionKO="UPDATE op_transaction SET status_tac = 'ECHEC', last_update='$leloU', statusCode_rm = '$resultCodes1', status_rm = 'DECLINED' ,transactionId_rm='$transIds1', transactionDescription_rm = '$resultDesc1' WHERE transactionReference_qm ='$transIds1'"  or die($mysqli->error.__LINE__);
$resultatTransactionKO=$mysqli->query($updateTransactionKO)or die($mysqli->error.__LINE__);
		
if($resultatTransactionKO){
$Q100="UPDATE sys_user SET statut= 'pending' WHERE sys_user.id_generate_tac = '$tagCarte'";
$r100=$mysqli->query($Q100)or die($mysqli->error.__LINE__);
//echo "transaction echouer :".$transIds."\n"; 
	
}
$arr=['msg'=>'Transaction echouer avec succées:'.$transIds.'\n','status'=>400];
echo json_encode($arr);	
}elseif($resultDesc1=='TransId INCORRECT'){
//print_r($response);
	$leloU            = date("Y-m-d H:i:s");
	$updateTransactionKO="UPDATE op_transaction SET status_tac = 'ECHEC', last_update='$leloU', statusCode_rm = '$resultCodes1', status_rm = 'DECLINED' ,transactionId_rm='$transIds1', transactionDescription_rm = '$resultDesc1' WHERE transactionReference_qm ='$transIds1'"  or die($mysqli->error.__LINE__);
$resultatTransactionKO=$mysqli->query($updateTransactionKO)or die($mysqli->error.__LINE__);
		
if($resultatTransactionKO){
$Q100="UPDATE sys_user SET statut= 'pending' WHERE sys_user.id_generate_tac = '$tagCarte'";
$r100=$mysqli->query($Q100)or die($mysqli->error.__LINE__);
//echo "transaction echouer :".$transIds."\n"; 
	
}
$arr=['msg'=>'Transaction echouer avec succées:'.$transIds.'\n','status'=>400];
echo json_encode($arr);		
		
}
	//echo $txnstatut1;	
//$arr=['msg'=>'Transaction echouer avec succées:'.$transIds.'\n','status'=>400];
//echo json_encode($arr);	

    
}else{


}

	}else{
    $arr=['msg'=>'erreur enregistrement n a pas abouti','status'=>400];
     echo json_encode($arr);
    }//Fin script $queryCreateUser
				
}//Fin script $query_checkUser
}//Fin script $queryCheckEtudiant	
}elseif($statutAttribution=='YES'){ //Fin script Verification carte
	
    $arr=['msg'=>'Carte deja attribuée','status'=>400];
    echo json_encode($arr);
    }else{
	$arr=['msg'=>'Carte non attribuée','status'=>400];
    echo json_encode($arr);
	}		
}elseif($statutActivation=='DESACTIVED'){ //Fin script Verification carte
    $arr=['msg'=>'Carte non activée dans le systeme','status'=>400];
    echo json_encode($arr);
    exit();
    }else{
	$arr=['msg'=>'Carte Inconnue dans le système','status'=>400];
    echo json_encode($arr);
	}	


function orangemoney($amount,$transactionReference,$currency,$customerPhoneNumber) {

	$xml="<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://services.ws1.com/\">\n      		    <soapenv:Header/>\n  <soapenv:Body>\n    <ser:doS2M>\n      <subsmsisdn>$customerPhoneNumber</subsmsisdn>\n      <PartnId>9390</PartnId>\n      <mermsisdn>0847576307</mermsisdn>\n      <transid>$transactionReference</transid>\n      <currency>$currency</currency>\n      <amount>$amount</amount>\n      <callbackurl>https://api.trans-academia.cd/Callback_om.php</callbackurl>\n      <message_s2m/>\n    </ser:doS2M>\n  </soapenv:Body>\n</soapenv:Envelope>";
	
	//return print($xml);
	
	$response = postMethod($xml);
	$xml = str_ireplace(['S:', 'S:', 'ns2:'],'',$response);
	$xml1 = simplexml_load_string($xml);
	//echo json_decode(json_encode($xml1),true);
	$json =json_decode(json_encode($xml1),true);	
	$transid=$json['Body']['doS2MResponse']['return']['transId'];
	//print_r($json['Body']['doS2MResponse']['return']['transId']);
	
	// INSERT INTO
	$response = CHECK_STATUS_orangemoney($transid);
    //print_r(json_encode($response));
	return json_encode($response);
}

function CHECK_STATUS_orangemoney($transid){
	$xml ="<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ser=\"http://services.ws1.com/\">\n\t<soapenv:Header />\n\t<soapenv:Body>\n\t\t<ser:doCheckTrans>\n\t\t\t<PartnId>9390</PartnId>\n\t\t\t<mermsisdn>0847576307</mermsisdn>\n\t\t\t<transid>$transid</transid>\n\t\t</ser:doCheckTrans>\n\t</soapenv:Body>\n</soapenv:Envelope>";
	sleep(30);
	$response = postMethod($xml);
	$xml = str_ireplace(['S:', 'S:', 'ns2:'],'',$response);
	$xml1 = simplexml_load_string($xml);
	//echo json_decode(json_encode($xml1),true);
	$json =json_decode(json_encode($xml1),true);	
	
	$rslt = $json['Body']['doCheckTransResponse']['return'];
	/*$rslt = json_encode($rslt);
	
	$montant =    $rslt['montant'];
	$refPayment = $rslt['refPayment'];
	$resultCode = $rslt['resultCode'];
	$resultDesc = $rslt['resultDesc'];
	$transId    = $rslt['transId'];
	$txnstatus  = $rslt['txnstatus']; */
		
	if($resultDesc =="Push envoyé"){
		CHECK_STATUS_orangemoney($transid);
	}else{
		return $rslt;
		//print_r($rslt);
	    // echo 'Return : 1 :'.$montant.' 2:'.$refPayment.' 3: '.$resultCode.' 4: '.$resultDesc.'5: '.$transId.' 6: '.$txnstatus;
	}
	
}

function postMethod($xml)
{
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://41.77.223.184:8088/apigatewayom/apigwomService');// serveur test
	//curl_setopt($ch, CURLOPT_URL, 'https://10.25.23.165:8088/apigatewayom/apigwomService');// serveur production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer c0d9acb4-6e84-2954-83e2-411704dc24dr',
    'Content-Type: text/xml',
    'Pragma: no-cache',
    'SOAPAction: urn:schemas-upnp-org:service:WANIPConnection:1#ForceTermination',
]);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch); return $response;	
	curl_close($ch);
	return($response);
}
function savetrans(){
 
	global $resultDesc1;
	global $montant1;
	global $refPayments1 ;
	global $resultCodes1;
	global $transIds1;
    global $txnstatut1;
    
}
savetrans();
