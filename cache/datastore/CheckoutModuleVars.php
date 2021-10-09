<?php

/** Loja Virtual V2010 - hiperlojas2008@gmail.com **
  *
  * Data: Sun, 26 Sep 2010 20:20:01 +0000
  *
  * nao modificar o arquivo manualmente
  *
*/

$cacheData = array (
  'checkout_deposito' => 
  array (
    'displayname' => 'Deposito Bancario',
    'helptext' => 'Banco: Banco do Brasil
Agencia: 64646
Nome: John Smith
Conta: XXXXXXXXXXXX

Mais Instrucoes e Detalhes.',
    'availablecountries' => 'all',
    'desconto' => '10',
    'is_setup' => '1',
  ),
  'checkout_mercadopago' => 
  array (
    'token' => 'eeee',
    'acrecimo' => '0.00',
    'jurosde' => '12',
    'helptext' => '<a href="javascript:window.open(\'%%GLOBAL_ShopPath%%/modules/checkout/mercadopago/repagar.php?pedido=%%GLOBAL_OrderId%%\',\'popup\',\'width=800,height=800,scrollbars=yes\');void(0);"><img src=\'%%GLOBAL_ShopPath%%/modules/checkout/mercadopago/images/final.gif\' border=\'0\'></a>
<br>',
    'pagemail' => 'eeee',
    'availablecountries' => 
    array (
      0 => 'all',
      1 => '30',
    ),
    'displayname' => 'MercadoPago Pagamentos',
    'is_setup' => '1',
  ),
  'checkout_moip' => 
  array (
    'helptext' => '<a href="javascript:window.open(\'%%GLOBAL_ShopPath%%/modules/checkout/moip/repagar.php?pedido=%%GLOBAL_OrderId%%\',\'popup\',\'width=800,height=800,scrollbars=yes\');void(0);"><img src=\'%%GLOBAL_ShopPath%%/modules/checkout/moip/images/final.gif\' border=\'0\'></a>
<br>',
    'htmlmoip' => 'eeee',
    'jurosde' => '12',
    'acrecimo' => '0',
    'pagemail' => 'contato@contato.com.br',
    'availablecountries' => 'all',
    'displayname' => 'Moip',
    'is_setup' => '1',
  ),
  'checkout_pagamentodigital' => 
  array (
    'helptext' => '<a href="javascript:window.open(\'%%GLOBAL_ShopPath%%/modules/checkout/pagamentodigital/repagar.php?pedido=%%GLOBAL_OrderId%%\',\'popup\',\'width=800,height=800,scrollbars=yes\');void(0);"><img src=\'%%GLOBAL_ShopPath%%/modules/checkout/pagamentodigital/images/final.gif\' border=\'0\'></a>
<br>',
    'displayname' => 'PagamentoDigital',
    'availablecountries' => 'all',
    'pagdigemail' => 'vendas@mundodocabelo.com.br',
    'acrecimo' => '0.00',
    'is_setup' => '1',
    'jurosde' => '11',
  ),
);

?>