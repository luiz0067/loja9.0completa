<html>
<head><title>Rastrear Encomenda</title></head>

<body>
<?php
include "../config/config.php";

$servidor = $GLOBALS['ISC_CFG']["dbServer"];
$usuariodb = $GLOBALS['ISC_CFG']["dbUser"];
$senhadb = $GLOBALS['ISC_CFG']["dbPass"];
$bancodados = $GLOBALS['ISC_CFG']["dbDatabase"];
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$nomeloja = $GLOBALS['ISC_CFG']["StoreName"];
$emailloja = $GLOBALS['ISC_CFG']["OrderEmail"];
$urlloja = $GLOBALS['ISC_CFG']["ShopPath"];

$conexao2 = mysql_connect($servidor, $usuariodb, $senhadb) or print(mysql_error());
$selecionabanco = mysql_select_db($bancodados,$conexao2) or print(mysql_error());
////////////////////////////////
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$o = mysql_query("select * from ".$prefixotabela."orders where ordstatus='2' and ordtrackingno!=''") or print(mysql_error());
while($dados = mysql_fetch_array($o)){
$objeto = str_replace(' ','',$dados['ordtrackingno']);
$emails = str_replace(' ','',$dados['ordshipemail']);
$nomes = $dados['ordbillfirstname'];
$pedido = $dados['orderid'];
echo 'Nome: '.$nomes.'<br>';
echo 'Email: '.$emails.'<br>';
echo 'Objeto: '.$objeto.'<br>';
if($objeto!="") {
$url = "http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=$objeto";
//echo $url;
$ch = curl_init();
$timeout = 10; // set to zero for no timeout
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$file_contents = curl_exec($ch);
curl_close($ch);
$arquivo = array();
$arquivo = explode("\n", $file_contents);

$items = Array();
foreach ($arquivo as $num => $linha) {
if (substr($linha, 0, 7) == '<tr><td') {
if (preg_match('/<td rowspan=[0-9]>.+?<\/td>/', $linha, $match))
$items[$num]['data'] = utf8_encode(strip_tags($match[0]));
if (preg_match('/<td colspan=[0-9]>.+?<\/td>/', $linha, $match))
$items[$num-1]['para'] = utf8_encode(strip_tags($match[0]));
if (preg_match('/<td>.+?<\/td>/', $linha, $match))
$items[$num]['local'] = utf8_encode(strip_tags($match[0]));
if (preg_match('/<FONT.*>.+?<\/font>/', $linha, $match))
$items[$num]['situacao'] = utf8_encode(strip_tags($match[0]));
}
}
if (!$items) {
$items[0]['data'] =  0;
$items[0]['situacao'] = 'Sem informações sobre o pacote';
$items[0]['descricao'] = "O nosso sistema não possui dados sobre o objeto informado. Verifique se o código digitado está correto: $objeto";
} else {
// detinatário do email
$to = $emails;
$subj = 'Rastreamento Online do Pedido: #'.$pedido;
$nome = $nomeloja;
$email = $emailloja;

//msg
$msg = "<b>Ola, ".$nomes."</b>";
$msg .= "<center>Rastreamento do Objeto: <b>".$objeto."</b><br><img src='".$urlloja."/gerenciar/correios.jpg'></center><table width=600 border=1><tr>";
$msg .= "<td width=20%>Data</td>";
$msg .= "<td width=60%>Local</td>";
$msg .= "<td width=20%>Situacao</td></tr>";
foreach ($items as $ver) {
$msg .= "<tr><td width=20%><b>".$ver['data']."</b></td>";
$msg .= "<td width=60%>".$ver['local']."</td>";
$msg .= "<td width=20%>".$ver['situacao']."</td></tr>";
if($ver['situacao']=='Entregue'){
// construção do cabecalho
$subjs = 'Pedido: #'.$pedido." Entregue";
$mens = '<br>Ola Administrador,<br>O Pedido: #'.$pedido." Ja foi Entregue pelos Correios.<br>Mude o Status do Pedido para <b>COMPLETO</a>.";
$headers = "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset='ISO-8859-1'\n";
$headers .= "From: ".$nome." <".$email.">\n";
$headers .= "Return-Path: <$email>\n";
$headers .= "Reply-to: $nome <$email>\n";
$headers .= "X-Priority: 1\n"; 
mail($email,$subjs,$mens,$headers);
}
}
$msg .= "</table><br>Obrigado por Compra Conosco,<br>
<a href='".$urlloja."' target='_blank'>".$nomeloja."</a><br>";
// construção do cabecalho
$headers = "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset='ISO-8859-1'\n";
$headers .= "From: ".$nome." <".$email.">\n";
$headers .= "Return-Path: <$email>\n";
$headers .= "Reply-to: $nome <$email>\n";
$headers .= "X-Priority: 1\n"; 
mail($to,$subj,$msg,$headers);

}

} else {
echo "Codigo Invalido!<br>";
}

}
?>
