<?php
include('../init.php');
ini_set("display_errors", 0);
ini_set("allow_url_fopen", 1);
ini_set("allow_url_include", 1); 
$cep = $_GET['getClientId'];
//dados
header('Content-Type: text/html; charset=utf-8');

$endr = "select * from [|PREFIX|]formfields where formfieldprivateid = 'AddressLine1'";
$endr = $GLOBALS['ISC_CLASS_DB']->Query($endr);
$endr = $GLOBALS['ISC_CLASS_DB']->Fetch($endr);

$endb = "select * from [|PREFIX|]formfields where formfieldprivateid = 'AddressLine2'";
$endb = $GLOBALS['ISC_CLASS_DB']->Query($endb);
$endb = $GLOBALS['ISC_CLASS_DB']->Fetch($endb);

$endc = "select * from [|PREFIX|]formfields where formfieldprivateid = 'City'";
$endc = $GLOBALS['ISC_CLASS_DB']->Query($endc);
$endc = $GLOBALS['ISC_CLASS_DB']->Fetch($endc);

$ende = "select * from [|PREFIX|]formfields where formfieldprivateid = 'State'";
$ende = $GLOBALS['ISC_CLASS_DB']->Query($ende);
$ende = $GLOBALS['ISC_CLASS_DB']->Fetch($ende);
//fim dados
if(!empty($cep)) {
$url = "http://cep.republicavirtual.com.br/web_cep.php?cep=".$cep."&formato=xml";
$end = simplexml_load_file($url);
$id = $end->xpath('resultado');
$estado = $end->xpath('uf');
$cidade = $end->xpath('cidade');
$bairro = $end->xpath('bairro');
$tipo = $end->xpath('tipo_logradouro');
$log = $end->xpath('logradouro');

if($id[0]>=1){

echo "document.getElementById('FormField_".$endr['formfieldid']."').value = '".$tipo[0]." ".$log[0]."';\n";    
echo "document.getElementById('FormField_".$endb['formfieldid']."').value = '".$bairro[0]."';\n";    
echo "document.getElementById('FormField_".$endc['formfieldid']."').value = '".$cidade[0]."';\n";

$situacao = $estado[0];

switch ($situacao) {
case "AC" :
$estadonovo = "Acre";
break;

case "AL" :
$estadonovo = "Alagoas";
break;

case "AP" :
$estadonovo = "Amapa";
break;

case "AM" :
$estadonovo = "Amazonas";
break;

case "BA" :
$estadonovo = "Bahia";
break;

case "CE" :
$estadonovo = "Ceara";
break;

case "DF" :
$estadonovo = "Distrito Federal";
break;

case "ES" :
$estadonovo = "Espirito Santo";
break;

case "GO" :
$estadonovo = "Goias";
break;

case "MA" :
$estadonovo = "Maranhao";
break;

case "MT" :
$estadonovo = "Mato Grosso";
break;

case "MS" :
$estadonovo = "Mato Grosso do Sul";
break;

case "MG" :
$estadonovo = "Minas Gerais";
break;

case "PA" :
$estadonovo = "Para";
break;

case "PB" :
$estadonovo = "Paraiba";
break;

case "PA" :
$estadonovo = "Parana";
break;

case "PE" :
$estadonovo = "Pernambuco";
break;

case "PI" :
$estadonovo = "Piaui";
break;

case "RJ" :
$estadonovo = "Rio de Janeiro";
break;

case "RN" :
$estadonovo = "Rio Grande do Norte";
break;

case "RS" :
$estadonovo = "Rio Grande do Sul";
break;

case "RO" :
$estadonovo = "Rondonia";
break;

case "RR" :
$estadonovo = "Roraima";
break;

case "SC" :
$estadonovo = "Santa Catarina";
break;

case "SP" :
$estadonovo = "Sao Paulo";
break;

case "SE" :
$estadonovo = "Sergipe";
break;

case "TO" :
$estadonovo = "Tocantins";
break;
}

echo "document.getElementById('FormField_".$ende['formfieldid']."').value = '".$estadonovo."';\n";  

}else{

echo "document.getElementById('FormField_".$endr['formfieldid']."').value = '';\n";    
echo "document.getElementById('FormField_".$endb['formfieldid']."').value = '';\n";    
echo "document.getElementById('FormField_".$endc['formfieldid']."').value = '';\n"; 
echo "document.getElementById('FormField_".$ende['formfieldid']."').value = '';\n";
     
}

}else{

echo "document.getElementById('FormField_".$endr['formfieldid']."').value = '';\n";    
echo "document.getElementById('FormField_".$endb['formfieldid']."').value = '';\n";    
echo "document.getElementById('FormField_".$endc['formfieldid']."').value = '';\n"; 
echo "document.getElementById('FormField_".$ende['formfieldid']."').value = '';\n";
 
}
?> 