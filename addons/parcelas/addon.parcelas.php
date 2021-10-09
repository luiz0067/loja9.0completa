<?php



class ADDON_PARCELAS extends ISC_ADDON



{







	public function __construct()



	{







		parent::__construct();











		$this->SetName('Simulador de Parcelas e Addons');











		$this->SetImage('');











		$this->SetHelpText('Escolha quais meios de pagamento o simulador de parcela ira exibir');











		$this->RegisterMenuItem(array(



			'location'		=> 'mnuTools',



			'icon'			=> 'icon.gif',



			'text'			=> 'Simulador de Parcelas e Addons',



			'description'	=> 'Configure as Opções que Sera Ultilizadas no Simulador de Frete',



			'id'			=> 'addon_simularparcelas'



		));















	}







	public function SetCustomVars()



	{





	$this->_variables['loginparapreco'] = array(

			'type' => 'dropdown',

			'name' => 'Amostrar preço na loja ?',

			'default' => 'sim',

			'options' => array(

				'Mostrar somente para logados' => 'nao',

				'Mostrar para todos' => 'sim'

			),

			"multiselect" => false,

			'required' => true);







		$this->_variables['tipos'] = array(



			'type' => 'dropdown',



			'name' => 'Tipos Aceitos (Pagina Produto)',



			'default' => '',



			'options' => array(



				'Deposito' => 'deposito',
	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',


				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'PagDigital' => 'pagdigital',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypal',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => true,



			'required' => true



		);



			$this->_variables['rodape1'] = array(



			'type' => 'dropdown',



			'name' => 'Tipo Aceito 01 (Rodape Produtos)',



			'default' => 'deposito',



			'options' => array(



		        'Nao Mostrar' => '1',



				'Deposito' => 'deposito',

	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',

				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'PagDigital' => 'pagdigital',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypal',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps1',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => false,



			'required' => true



		);



		$this->_variables['rodape2'] = array(



			'type' => 'dropdown',



			'name' => 'Tipo Aceito 02 (Rodape Produtos)',



			'default' => 'pagseguro',



			'options' => array(



				'Nao Mostrar' => '1',



				'Deposito' => 'deposito',
	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',


				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'PagDigital' => 'pagdigital',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypal',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps1',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => false,



			'required' => true



		);



		$this->_variables['descboleto'] = array("name" => "Desconto % (Apenas Boleto)",



			   "type" => "textbox",



			   "help" => 'Ponha o a Taxa a ser Cobrado em Cada Boleto.',



			   "default" => '0',



			   "required" => false);



			   



	   $this->_variables['scroll'] = array(



			'type' => 'dropdown',



			'name' => 'Tipo Scroll de Produtos',



			'default' => 'html',



			'options' => array(



				'Nao Mostrar' => 'nao',



				'HTML' => 'html',



				'Flash' => 'flash'



			),



			"multiselect" => false,



			'required' => true);



			



	   	   $this->_variables['scrolln'] = array(



			'type' => 'dropdown',



			'name' => 'Numero de Produtos Scroll',



			'default' => '10',



			'options' => array(



				'5' => '5',



				'10' => '10',



				'15' => '15',



				'20' => '20',



				'25' => '25',



				'30' => '30',



			),



			"multiselect" => false,



			'required' => true);



			



		$this->_variables['pdf'] = array(



			'type' => 'dropdown',



			'name' => 'Ativar Catalogo PDF',



			'default' => 'sim',



			'options' => array(



				'Sim, Ativar' => 'sim',



				'Não Ativar' => 'nao'



			),



			"multiselect" => false,



			'required' => true);



			



			$this->_variables['twitter'] = array(



			'type' => 'dropdown',



			'name' => 'Ativar Ultimos 5 Twitter',



			"help" => 'Obs: essa funcao so funciona se seu servidor aceita conexoes fopen e possuir funcao load_xml ativa.',



			'default' => 'nao',



			'options' => array(



				'Sim, Ativar' => 'sim',



				'Não Ativar' => 'nao'



			),



			"multiselect" => false,



			'required' => true);



				$this->_variables['usertwitter'] = array("name" => "Usuario Twitter",



			   "type" => "textbox",



			   "help" => 'Ponha o seu nome de usuario usado no twitter.',



			   "default" => 'googlebrasil',



			   "required" => false);



			   



	}











	public function Init()



	{







	}











	public function EntryPoint()



	{







	}











	public function ToolsMenuExample()



	{



	}



}