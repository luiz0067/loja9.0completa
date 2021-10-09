<?php
include('../init.php');
//pega os dados
$loja = GetConfig('ShopPath');
$base = "http://www.easysw.com/htmldoc/pdf-o-matic.php";
$pdfdata = @file_get_contents($base."?URL=".urlencode($loja)."%2Fmodificacoes%2Fprodutos.php&FORMAT=.pdf");
//aqui faz o download
@header("Cache-Control: ");// leave blank to avoid IE errors
@header("Pragma: ");// leave blank to avoid IE errors
@header("Content-type: application/octet-stream");
@header("Content-Disposition: attachment; filename=Catalogo-PDF-".time()."-cliquemania.pdf");
echo $pdfdata;
?>