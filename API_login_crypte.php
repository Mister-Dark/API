<?php 
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
header('Access-Control-Allow-Headers:*');
header('Content-Type:application/json'); 

require("cnx.php");

$token     =mysqli_real_escape_string($mysqli,$_POST['token']);
$App_name  =mysqli_real_escape_string($mysqli,$_POST['App_name']);
$pass      =$_POST['pass'];
$login     =mysqli_real_escape_string($mysqli,$_POST['login']);

$req = "SELECT * FROM application where token = '$token' AND name_app='$App_name'";
$r1=mysqli_query($mysqli, $req);
$count=mysqli_num_rows($r1);
if($count >0)
{ 
//$query="SELECT * FROM sys_user JOIN op_etudiant ON sys_user.id_generate_tac = op_etudiant.code_generate_tac WHERE SUBSTR(sys_user.name,-9)=SUBSTR('$login',-9)";
$query="SELECT * FROM sys_user JOIN op_etudiant ON sys_user.id_generate_tac = op_etudiant.code_generate_tac WHERE sys_user.name ='$login'";
$resultat=mysqli_query($mysqli,$query);
 $myarray=array();
    if(mysqli_num_rows($resultat)> 0)
    {
      while($row=mysqli_fetch_array($resultat))
      {
	   $Refresh = $row['Refresh'];
		  
		 if($Refresh=='0'){
		 $records=array(
            "id"=>$row['Id_etudiant_identification'],
            "Login"=>$row['name'],
            "password"=>$row['pwd'],
		    "Nom"=>$row['nom'],
		    "Postnom"=>$row['postnom'],
		    "Prenom"=>$row['prenom'],
		    "Code Etudiant"=>$row['id_generate_tac'],
		    "statut"=>$row['statut'],
			 "Refresh"=>$row['Refresh'],
		    "Photo"=>$row['photo']
         );
       array_push($myarray, $records);
            $arr=['msg'=>'Donnees recuperer','status'=>201,'données'=>$myarray];
            echo json_encode($arr);
			 exit();
		 }if($Refresh=='1'){
		 if(password_verify($pass,$row['pwd']))
        {
             $records=array(
            "id"=>$row['Id_etudiant_identification'],
            "Login"=>$row['name'],
            "password"=>$row['pwd'],
		    "Nom"=>$row['nom'],
		    "Postnom"=>$row['postnom'],
		    "Prenom"=>$row['prenom'],
		    "Code Etudiant"=>$row['id_generate_tac'],
		    "statut"=>$row['statut'],
			 "Refresh"=>$row['Refresh'],
		    "Photo"=>$row['photo']
         );
       array_push($myarray, $records);
            $arr=['msg'=>'Donnees recuperer','status'=>200,'données'=>$myarray];
            echo json_encode($arr);
			exit();
        }
		  array_push($myarray, $records);
          $arr=['msg'=>'Mot de pass Incorrect','status'=>400];
            echo json_encode($arr);
		 exit();
		 
		 }
      
      }
    }else{
	$arr=['msg'=>'Login incorrect','status'=>400];
    echo json_encode($arr);
}
}else{
      $arr=['msg'=>'erreur de connexion','status'=>400];
     echo json_encode($arr);
}


?>
