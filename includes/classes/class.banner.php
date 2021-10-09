<?php

	/**
	*	Before the template parsing engine runs we will create a global list of banners
	*	for the selected page which will be "hooked" into the template system so they
	*	can be displayed on the site.
	**/
	class ISC_BANNER
	{

		public function __construct()
		{

			// First up, which page are we on?
			$GLOBALS['Banners'] = array();
			$banners = array();
			$page = "";
			$page_type = "";

			if(isset($GLOBALS['ISC_CLASS_SEARCH'])) {
				$page_type = 'search_page';
			}
			else if(isset($GLOBALS['ISC_CLASS_BRANDS'])) {
				$page_type = 'brand_page';
			}
			else if(isset($GLOBALS['ISC_CLASS_CATEGORY'])) {
				$page_type = 'category_page';
			}
			else if(isset($GLOBALS['ISC_CLASS_INDEX'])) {
				$page_type = 'home_page';
			}

			// Save the page type globally so we can access it from the template engine
			$GLOBALS['PageType'] = $page_type;

			if($page_type != "") {
				$stamp = time();
				$query = sprintf("select * from [|PREFIX|]banners
								  where page='%s'
										and status='1'
										and (
											(datefrom = 0 and dateto = 0)
											or (datefrom < '%s' and dateto > '%s')
										)
								  order by rand()", $GLOBALS['ISC_CLASS_DB']->Quote($page_type), $GLOBALS['ISC_CLASS_DB']->Quote($stamp), $GLOBALS['ISC_CLASS_DB']->Quote($stamp));

				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

				while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					array_push($banners, $row);
				}

				if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {
					foreach($banners as $banner) {
						if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
							$banner['content'] = str_replace($GLOBALS['ShopPathNormal'], $GLOBALS['ShopPathSSL'], $banner['content']);
						}

						$bannerContent = $banner['content'];
						// Wrap the banner in a div which can be styled
						
						switch($banner['location']){
							case "top":
								$banner['content'] = '<div id="flashcontent" style="width: 100%;">';
								$banner['content'] .= '</div>';
								$banner['content'] .= '<script type="text/javascript" src="%%GLOBAL_ShopPath%%/admin/includes/amcharts/swfobject.js?%%GLOBAL_JSCacheToken%%"></script>';
								$banner['content'] .= '<script type="text/javascript">';
								$banner['content'] .= '	$(document).ready(function() {';
								$banner['content'] .= '		var so = new SWFObject("%%GLOBAL_ShopPath%%/banners/holder.swf?%%GLOBAL_JSCacheToken%%", "banner", "100%", "180", "8", "#FFFFFF");';
								$banner['content'] .= '		so.addVariable("conteudoTxt", "'.$bannerContent.'");';
								$banner['content'] .= '		so.addVariable("bannerId", "%%GLOBAL_BannerId%%");';
								$banner['content'] .= '		so.addVariable("ToDo", "%%GLOBAL_FormAction%%");';
								$banner['content'] .= '		so.addVariable("SiteUrl", "%%GLOBAL_ShopPath%%");';
								$banner['content'] .= '		so.write("flashcontent");';
								$banner['content'] .= '	});';
								$banner['content'] .= '</script> ';
								break;
							case "bottom":
								$banner['content'] = sprintf("<div class=' banner_%s_%s'>%s</div>", $banner['page'], $banner['location'], $banner['content']);
								break;
							default: $banner['content'] = sprintf("<div class=' banner_%s_%s'>%s</div>", $banner['page'], $banner['location'], $banner['content']);
							
						}
						// Wrap the banner in a div which can be styled
						

					switch($page_type) {
							case "home_page":
							case "search_page": {
								if($banner['location'] == "top" && !isset($GLOBALS['Banners']['top'])) {
									$GLOBALS['Banners']['top'] = $banner;
								}
								else if($banner['location'] == "bottom" && !isset($GLOBALS['Banners']['bottom'])) {
									$GLOBALS['Banners']['bottom'] = $banner;
								}
								// modificacao
								else if($banner['location'] == "direito" && !isset($GLOBALS['Banners']['direito'])) {
									$GLOBALS['Banners']['direito'] = $banner;
								}	
								else if($banner['location'] == "esquerdo" && !isset($GLOBALS['Banners']['esquerdo'])) {
									$GLOBALS['Banners']['esquerdo'] = $banner;
								}
								break;
							}
							case "brand_page":
							case "category_page": {
								if($banner['location'] == "top" && !isset($GLOBALS['Banners'][$banner['catorbrandid']]['top'])) {
									$GLOBALS['Banners'][$banner['catorbrandid']]['top'] = $banner;
								}
								else if($banner['location'] == "bottom" && !isset($GLOBALS['Banners'][$banner['catorbrandid']]['bottom'])) {
									$GLOBALS['Banners'][$banner['catorbrandid']]['bottom'] = $banner;
								}
								// modificacao
								else if($banner['location'] == "direito" && !isset($GLOBALS['Banners'][$banner['catorbrandid']]['direito'])) {
									$GLOBALS['Banners'][$banner['catorbrandid']]['direito'] = $banner;
								}
								else if($banner['location'] == "esquerdo" && !isset($GLOBALS['Banners'][$banner['catorbrandid']]['esquerdo'])) {
									$GLOBALS['Banners'][$banner['catorbrandid']]['esquerdo'] = $banner;
								}
								break;	
							}
						}
					}
				}
			}
		}
	}

?>