<?php

class ISC_SITEMAP_ROOT extends ISC_SITEMAP_NODE implements iISC_SITEMAP_NODE {

	/**
	*
	* @return string
	*/
	public function generateNodeHtml ()
	{
		$html = '';

		if ($this->countChildren()) {
			$html .= '<ul>';

			foreach ($this->getChildren() as $child) {
				$html .= $child->generateNodeHtml();
			}

			$html .= '</ul>';
		}

		return $html;
	}
}
