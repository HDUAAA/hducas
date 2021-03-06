<?php
error_reporting(E_ALL ^ E_NOTICE);
header("Content-Type: text/json; charset=UTF-8");

$localurl = $_SERVER['HTTPS']?'https://':'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];

$ticket = !empty(htmlspecialchars(@$_REQUEST['ticket'])) ? htmlspecialchars($_REQUEST['ticket']) : null;
if (is_null($ticket)) {
	header("Location: http://cas.hdu.edu.cn/cas/login?service=".urlencode($localurl));
  	exit();
}

$response = file_get_contents("http://cas.hdu.edu.cn/cas/serviceValidate?ticket=".$ticket."&service=".urlencode($localurl));

if(explode('"',explode('value="',$response)[3])[0]==null||explode('"',explode('value="',$response)[3])[0]==""){
	$userinfo["state"]=403;
	setcookie("hdusso_state", 403);
}
else{
	$userinfo["state"]=200;
	$userinfo["name"]=explode('"',explode('value="',$response)[1])[0];
	$userinfo["sid"]=explode('"',explode('value="',$response)[3])[0];
	$userinfo["sex"]=explode('"',explode('value="',$response)[6])[0];
	$userinfo["institute"]=explode('"',explode('value="',$response)[7])[0];
	$userinfo["class"]=explode('"',explode('value="',$response)[8])[0];
	setcookie("hdusso_state", 200);
	setcookie("hdusso_name", $userinfo["name"]);
  	setcookie("hdusso_sid", $userinfo["sid"]);
  	setcookie("hdusso_sex", $userinfo["sex"]);
  	setcookie("hdusso_institute", $userinfo["institute"]);
  	setcookie("hdusso_class", $userinfo["class"]);
}

$userinfo = json_encode($userinfo);
echo $userinfo;
header("Location: "."./index.php?act=login");
exit();

?>