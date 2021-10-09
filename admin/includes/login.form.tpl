<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Administração Loja Virtual - Login de Acesso</title>
<meta name="robots" content="noindex, nofollow" />
<link rel="SHORTCUT ICON" href="favicon.ico" />
<!--modificacoes-->
<script type="text/javascript" src="modificacoes/keyboard.js"></script>
<link rel="stylesheet" href="modificacoes/keyboard.css" type="text/css" />
<!--fim-->

<script type="text/javascript">
function entrar(){
document.getElementById("msg").style.display = "";
document.getElementById("msg_texto").innerHTML = "Validando...";
//window.location = '%%GLOBAL_ShopPath%%/admin/index.php?ToDo=processLogin';
var url = "%%GLOBAL_ShopPath%%/admin/index.php?ToDo=processLogin";
var params = "lorem=ipsum&name=binny";
http.open("POST", url, true);
http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
http.setRequestHeader("Content-length", params.length);
http.setRequestHeader("Connection", "close");

http.onreadystatechange = function() {//Call a function when the state changes.
	if(http.readyState == 4 && http.status == 200) {
		alert(http.responseText);
	}
}
http.send(params);

return false;
}
</script>

<style type="text/css">
	body { padding:0; margin:0; background:#b8b8b8 url(modificacoes/login_r1_c1.jpg) repeat-x; font-size:12px; font-family:Tahoma; }
	.frm_login { color:#FFF; }
  .campo_texto  { font-size:12px; font-family:Tahoma;border:1px solid #51A8FF; padding:3px; background-image:url(modificacoes/fundo_input.gif); background-repeat:repeat-x; background-position:top; }
	#msg_texto { font-size:12px; font-family:Tahoma; font-weight:bold; color:#FFF;  }
	a:link,a:visited { color: #FFFFFF; text-decoration: none; }
	a:hover { color: #FFFFFF; text-decoration: underline; }
</style>
</head>
<body>
<form action="%%GLOBAL_ShopPath%%/admin/index.php?ToDo=%%GLOBAL_SubmitAction%%" method="post" enctype="multipart/form-data" name="frmlogin" id="frmlogin" onSubmit="return entrar();">
  <p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"><img src="modificacoes/lojaswebshop.swf"></td>
    </tr>
    <tr>
      <td height="100" align="center" background="modificacoes/formloginbg.png" style="background-position:center;background-repeat:no-repeat;">
      <table border="0" cellspacing="5" cellpadding="0" class="frm_login">
        <tr>
          <td align="left"><b>Usuário:</b></td>
          <td rowspan="2" align="left">&nbsp;</td>
          <td align="left"><b>Senha: (<a href="index.php?ToDo=forgotPass" target="_blank">?</a>)</b></td>
        </tr>
        <tr>
          <td align="left"><input autocomplete="off" type="text" name="usuario" id="usuario" class="campo_texto" size=25></td>
          <td align="left"><input autocomplete="off" type="password" name="senha" id="senha" class="keyboardInput" value="%%GLOBAL_Password%%"></td>
        </tr>
      </table></td>
    </tr>
    <tr id="msg" style="display:">
      <td align="center" id="msg_texto" height="10" valign="top">%%GLOBAL_Message%%<br></td>
	  
    </tr>
    <tr>
      <td align="center"><br><input type="image" name="bt_entrar" src="modificacoes/bt_entrar.png" alt="Entrar"></td>
    </tr>
      </table>

	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>

</form>
</body>
</html>