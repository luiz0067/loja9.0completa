<?php
require_once("../init.php");
//////////////////////////////////////////////
	function limpar($val)
	{

		$val = str_replace("�", "a", $val);
		$val = str_replace("�", "a", $val);
		$val = str_replace("�", "e", $val);
		$val = str_replace("�", "i", $val);
		$val = str_replace("�", "o", $val);
		$val = str_replace("�", "u", $val);
		$val = str_replace("�", "e", $val);
		$val = str_replace("�", "o", $val);
		$val = str_replace("&", "e", $val);
		$val = str_replace("�", "a", $val);
		$val = str_replace("�", "c", $val);
		$val = str_replace("�", "A", $val);
		$val = str_replace("�", "E", $val);
		$val = str_replace("�", "I", $val);
		$val = str_replace("�", "O", $val);
		$val = str_replace("�", "U", $val);
		$val = str_replace("�", "A", $val);
		$val = str_replace("�", "O", $val);
		$val = str_replace("�", "O", $val);
		$val = str_replace("�", "E", $val);
		$val = str_replace("�", "C", $val);
		$val = str_replace("�", "o", $val);
		return $val;
	}
echo "<productslider>
<config>
<version>WebSHop</version> 
<alignment>center</alignment>
<spacing>20</spacing>
<margin>50</margin>
<scaleSize>1.2</scaleSize>
<enableToolTips>0</enableToolTips>
<enableSound>1</enableSound>
<stageColor>0xFFFFFF</stageColor>
<iconColor>0x000000</iconColor>
<imageHeight>120</imageHeight>
<imageWidth>100</imageWidth>
<scrollSpeed>7</scrollSpeed>
<enablePrice>1</enablePrice>
<autoScale>1</autoScale>
<sliderDirection>horizontal</sliderDirection>
<enableIcon>1</enableIcon>
<enableFixedToolTips>1</enableFixedToolTips>
</config>
<products>";

$query = "select * from [|PREFIX|]products ORDER BY rand() LIMIT 0,15";
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
$image = "select * from [|PREFIX|]product_images where imageprodid = '$row[productid]' and imageisthumb = '1'";
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);
$valorPa =number_format($row['prodcalculatedprice'], 2, ',', '');
$url = ProdLink($row['prodname']);
//$urls = $GLOBALS['ShopPath'];
echo"<product> 
<productName><![CDATA[".limpar($row['prodname'])."]]></productName> 
<imagePath><![CDATA[modificacoes/miniatura.php?w=120&img=../product_images/".$img['imagefile']."]]></imagePath> 
<targetURL><![CDATA[".$url."]]></targetURL> 
<productPrice><![CDATA[".$valorPa."]]></productPrice> 
<productCurrency><![CDATA[R$]]></productCurrency> 
</product>";
}
echo "</products>
</productslider>";
?>



