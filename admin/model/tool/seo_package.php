<?php
class ModelToolSeoPackage extends Model {
  
  public function getFriendlyUrls($lang) {

  }
  
	public function getProductCategoryName($product_id, $lang, $parent = false) {
    if ($parent) {
      $query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "product_to_category pc LEFT JOIN " . DB_PREFIX . "category c ON c.category_id = pc.category_id LEFT JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = c.parent_id WHERE pc.product_id = '" . (int)$product_id . "' AND cd.language_id = " . $lang . " LIMIT 1")->row;
    } else {
      $query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id) LEFT JOIN " . DB_PREFIX . "category_path cp ON (p2c.category_id = cp.category_id) WHERE p2c.product_id = '" . (int)$product_id . "' AND cd.language_id = " . $lang . " ORDER BY cp.level DESC LIMIT 1")->row;
    }
		
		if (isset($query['name']))
			return $query['name'];
		return '';
	}
  
  public function getCategoryName($category_id, $lang, $parent = false) {
    if (is_array($category_id)) {
      $cat_ids = implode(',', array_map('intval', $category_id));
      
      if ($parent) {
        $query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = c.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.parent_id) WHERE c.category_id IN (" . $cat_ids . ") AND cd.language_id = " . $lang . " ORDER BY cp.level DESC")->row;
      } else {
        $query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = cp.category_id) WHERE cp.category_id IN (" . $cat_ids . ") AND cd.language_id = " . $lang . " ORDER BY cp.level DESC")->row;
      }
    } else if ($parent) {
      $query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = c.parent_id WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = " . $lang . " LIMIT 1")->row;
    } else {
      $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "' AND language_id = " . $lang . " LIMIT 1")->row;
    }
		
		if (isset($query['name']))
			return $query['name'];
		return '';
	}
  
  public function getCategoryPrice($category_id, $type = 'lowest') {
    $order = ($type == 'lowest') ? 'ASC' : 'DESC';
    
    $query = $this->db->query("SELECT p.price FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' ORDER BY p.price " . $order . " LIMIT 1")->row;
    
    if (!empty($query['price'])) {
			$price = $this->currency->format($query['price'], $this->config->get('config_currency'));
      
      if (substr($price, -3) == '.00' || substr($price, -3) == ',00') {
        return substr($price, 0, -3);
      }
      
      return $price;
    }
    
		return '';
  }
  
  public function getParentCategoryName($category_id, $lang) {
		//$query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "category_description cd LEFT JOIN " . DB_PREFIX . "category c ON c.category_id = cd.category_id WHERE cd.category_id = '" . (int)$category_id . "' AND language_id = " . $lang . " LIMIT 1")->row;
		$query = $this->db->query("SELECT cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON c.parent_id = cd.category_id WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = " . $lang . " LIMIT 1")->row;
		
		if (isset($query['name']))
			return $query['name'];
		return '';
	}
	
	public function getProductCategoryId($product_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' LIMIT 1")->row;
		
		if (isset($query['category_id']))
			return $query['category_id'];
		return '';
	}
	
	public function getManufacturerName($manufacturer_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "' LIMIT 1")->row;
		
		if (isset($query['name']))
			return $query['name'];
		return '';
	}
	
	public function commonTransform($pattern, $lang, $row, $get_value = false) {
    $replace  = array();
    
    if ($this->config->get('mlseo_current_lang')) {
      $lang_code = $this->config->get('mlseo_current_lang');
    } else {
      $lang_code = $this->getLangCode($lang);
    }
    
    $replace['{'.$lang_code.'}'] = '';
    $replace['{/'.$lang_code.'}'] = '';
    $replace['{'.substr($lang_code, 0, 2).'}'] = '';
    $replace['{/'.substr($lang_code, 0, 2).'}'] = '';
    
    if (strpos($pattern, '[lang_id]') !== false)
			$replace['[lang_id]'] = $lang;
		if (strpos($pattern, '[lang]') !== false)
			$replace['[lang]'] = $lang_code;
    
    $pattern = str_replace(array_keys($replace), array_values($replace), $pattern);

    if ($this->config->get('mlseo_lang_codes')) {
      $lgCodes = array();
      foreach ($this->config->get('mlseo_lang_codes') as $lgCode) {
        $lgCodes[] = $lgCode;
        $lgCodes[] = substr($lgCode, 0, 2);
      }
      $lgCodes = implode('|', $lgCodes);
      
      $pattern = preg_replace('/\{('.$lgCodes.')\}(.*)\{\/('.$lgCodes.')\}/isU', '', $pattern);
    }
    
    while (preg_match_all('#\{([^\{].+\|.+)\}#U', $pattern, $results, PREG_SET_ORDER)) {
      if ($results) {
        foreach ($results as $res) {
          $rand = explode('|', $res[1]);
          
          if (is_array($rand)) {
            if ($get_value) {
              if (isset($this->session->data['gkd_seorand']) && $this->session->data['gkd_seorand']+1 <= count($rand)-1) {
                $this->session->data['gkd_seorand']++;
              } else {
                $this->session->data['gkd_seorand'] = 0;
              }
            
              $pattern = str_replace($res[0], $rand[$this->session->data['gkd_seorand']], $pattern);
            } else {
              $pattern = str_replace($res[0], $rand[array_rand($rand)], $pattern);
            }
          }
        }
      }
    }
    
    return $pattern;
  }
  
	public function transformProduct($pattern, $lang, $row, $get_value = false) {
    $pattern = $this->commonTransform($pattern, $lang, $row, $get_value);
    
		if (!isset($row['name'])) {
			$row['name'] = $row['product_description'][$lang]['name'];
			$row['description'] = $row['product_description'][$lang]['description'];
		}
		
    //$this->load->model('catalog/product');
    //$product_attributes = $this->model_catalog_product->getProductAttributes($row['product_id']);
    
    // $product_attributes = $this->db->query("SELECT ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$row['product_id'] . "' AND ad.language_id = '" . (int)$lang . "' AND pa.language_id = '" . (int)$lang . "' ORDER BY a.sort_order, ad.name")->rows;
		// var_dump($product_attributes);die;
    // preg_match('#\[attribute_name:(\d)\]#', $pattern, $res);
    
		$replace  = array();
		if (strpos($pattern, '[name]') !== false)
			$replace['[name]'] = trim($row['name']);
		if (strpos($pattern, '[model]') !== false)
			$replace['[model]'] = trim($row['model']);
		if (strpos($pattern, '[upc]') !== false)
			$replace['[upc]'] = trim($row['upc']);
		if (strpos($pattern, '[sku]') !== false)
			$replace['[sku]'] = trim($row['sku']);
		if (strpos($pattern, '[ean]') !== false)
			$replace['[ean]'] = trim($row['ean']);
		if (strpos($pattern, '[jan]') !== false)
			$replace['[jan]'] = trim($row['jan']);
		if (strpos($pattern, '[isbn]') !== false)
			$replace['[isbn]'] = trim($row['isbn']);
		if (strpos($pattern, '[mpn]') !== false)
			$replace['[mpn]'] = trim($row['mpn']);
		if (strpos($pattern, '[location]') !== false)
			$replace['[location]'] = trim($row['location']);
    if (strpos($pattern, '[price]') !== false)
			$replace['[price]'] = (float)$row['price'] ? $this->currency->format($row['price'], $this->config->get('config_currency')) : '';
		if (strpos($pattern, '[prod_id]') !== false)
			$replace['[prod_id]'] = isset($row['product_id']) ? trim($row['product_id']) : '';
		if (strpos($pattern, '[cat_id]') !== false)
			$replace['[cat_id]'] = isset($row['product_id']) ? trim($this->getProductCategoryId($row['product_id'], $lang)) : '';
		if (strpos($pattern, '[category]') !== false) {
      if (!empty($this->request->post['product_category'])) {
        $replace['[category]'] = trim($this->getCategoryName($this->request->post['product_category'], $lang));
      } else if (isset($row['product_id'])) {
        $replace['[category]'] = trim($this->getProductCategoryName($row['product_id'], $lang));
      } else {
        $replace['[category]'] = '';
      }
    }
		if (strpos($pattern, '[parent_category]') !== false) {
      if (!empty($this->request->post['product_category'])) {
        $replace['[parent_category]'] = trim($this->getCategoryName($this->request->post['product_category'], $lang, true));
      } else if (isset($row['product_id'])) {
        $replace['[parent_category]'] = trim($this->getProductCategoryName($row['product_id'], $lang, true));
      } else {
        $replace['[parent_category]'] = '';
      }
    }
		if (strpos($pattern, '[brand]') !== false)
			$replace['[brand]'] = (isset($row['product_id']) && $row['manufacturer_id']) ? trim($this->getManufacturerName($row['manufacturer_id'])) : '';
		if (strpos($pattern, '[desc]') !== false)
			$replace['[desc]'] = trim(preg_replace('/\s\s+/', ' ', substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8')), 0, 150)));
		// categories ?
			
		$value = str_replace(array_keys($replace), array_values($replace), $pattern);
		$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		
		return trim($value);
	}
	
	public function transformCategory($pattern, $lang, $row, $get_value = false) {
    $pattern = $this->commonTransform($pattern, $lang, $row, $get_value);
    
		if (!isset($row['name'])) {
			$row['name'] = $row['category_description'][$lang]['name'];
			$row['description'] = $row['category_description'][$lang]['description'];
		}
		
		$replace  = array();
    
		if (strpos($pattern, '[name]') !== false)
			$replace['[name]'] = trim($row['name']);
		if (strpos($pattern, '[desc]') !== false)
			$replace['[desc]'] = trim(preg_replace('/\s\s+/', ' ', substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8')), 0, 150)));
		if (strpos($pattern, '[cat_id]') !== false)
			$replace['[cat_id]'] = trim($row['category_id']);
    if (strpos($pattern, '[parent]') !== false)
			$replace['[parent]'] = !empty($row['category_id']) ? trim($this->getParentCategoryName($row['category_id'], $lang)) : '';
    if (strpos($pattern, '[lowest_price]') !== false)
			$replace['[lowest_price]'] = $this->getCategoryPrice($row['category_id'], 'lowest');
    if (strpos($pattern, '[highest_price]') !== false)
			$replace['[highest_price]'] = $this->getCategoryPrice($row['category_id'], 'highest');

			/*
		if (strpos($pattern, '[parent]') !== false)
			$replace['[parent]'] = $this->model_tool_seo_package->getParentCategoryName($row['category_id'], $lang);
			*/
			
		$value = str_replace(array_keys($replace), array_values($replace), $pattern);
		$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		
		return trim($value);
	}
	
	public function transformInformation($pattern, $lang, $row, $get_value = false) {
    $pattern = $this->commonTransform($pattern, $lang, $row, $get_value);
    
		if (!isset($row['title'])) {
			$row['title'] = $row['information_description'][$lang]['title'];
			$row['description'] = $row['information_description'][$lang]['description'];
		}
		
		$replace  = array();
		if (strpos($pattern, '[name]') !== false)
			$replace['[name]'] = trim($row['title']);
		if (strpos($pattern, '[title]') !== false)
			$replace['[title]'] = trim($row['title']);
		if (strpos($pattern, '[desc]') !== false)
			$replace['[desc]'] = trim(preg_replace('/\s\s+/', ' ', substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8')), 0, 150)));
			
		$value = str_replace(array_keys($replace), array_values($replace), $pattern);
		$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		
		return trim($value);
	}
	
	public function transformManufacturer($pattern, $lang, $row, $get_value = false) {
    $pattern = $this->commonTransform($pattern, $lang, $row, $get_value);
    
		$replace  = array();
		if (strpos($pattern, '[name]') !== false)
			$replace['[name]'] = trim($row['name']);
				
		$value = str_replace(array_keys($replace), array_values($replace), $pattern);
		$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		
		return $value;
	}
	
	public function filter_seo($seo_kw, $type, $id, $lang = '', $simulate = false) {
		$whitespace = $this->config->get('mlseo_whitespace');
    
    if ($lang) {
      $translit = $this->config->get('mlseo_ascii_'.$lang);
    } else {
      $translit = $this->config->get('mlseo_ascii_'.$this->config->get('config_language_id'));
    }
    
		if ($translit) {
      if ($this->config->get('mlseo_current_lang')) {
        $lang_code = $this->config->get('mlseo_current_lang');
      } else {
        $lang_code = $this->getLangCode($lang);
      }
      
			$seo_kw = URLify::downcode($seo_kw, substr($lang_code, 0, 2));
      
			// language specific tr
      /* now with urlify
			$seo_kw = $this->mb_strtr($seo_kw, array('À','Á','Â','Ã','Ä','Å','Ă','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ő','Ø','Œ','Ș','Ț','Ù','Ú','Û','Ü','Ű','Ý','Þ','ß','à','á','â','ã','ä','å','ă','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ő','ø','œ','ș','ț','ù','ú','û','ü','ű','ý','þ','ÿ'),
                                         array('A','A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','O','OE','S','T','U','U','U','U','U','Y','TH','ss','a','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','o','oe','s','t','u','u','u','u','u','y','th','y')); // latin
			$seo_kw = $this->mb_strtr($seo_kw, 'αβγδεζηθικλμνξοπρστυφχψωάέίόύήώςϊΰϋΐΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΆΈΊΌΎΉΏΪΫ', 'abgdezh8iklmn3oprstyfxpswaeioyhwsiyyiABGDEZH8IKLMN3OPRSTYFXPSWAEIOYHWIY'); // greek
			$seo_kw = $this->mb_strtr($seo_kw, 'ÁáČčĎďĚěŇňŘřŠšŤťÚúŮůÝýŽžĐđ', 'AaCcDdEeNnRrSsTtUuUuYyZzDd'); // czech, croatian, slovenian
			$seo_kw = $this->mb_strtr($seo_kw, 'ĄČĘĖĮYŠŲŪŽąčęėįyšųūž', 'ACEEIYSUUZaceeiysuuz'); // lithunanian
			$seo_kw = $this->mb_strtr($seo_kw, 'ĀČĒĢĪĶĻŅŠŪŽāčēģīķļņšūž', 'ACEGIKLNSUZacegiklnsuz'); // latvian
			$seo_kw = $this->mb_strtr($seo_kw, 'ÇçĞğİıÖöŞşÜü', 'CcGgIiOoSsUu'); // turkish
			$seo_kw = $this->mb_strtr($seo_kw, 'ăâîșțŢţ', 'aaistTt'); // romanian
			$seo_kw = $this->mb_strtr($seo_kw, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ', 'acelnoszzACeLNOSZZ'); // polish
			$seo_kw = $this->mb_strtr($seo_kw, array('Г','Д','Е','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ь','Ъ','щ','ш','ч','ц','ю','я','ж','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ь','ъ'),
                                         array('Sht','Sh','Ch','C','Yu','Ya','J','A','B','V','G','D','E','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','','A','sht','sh','ch','c','yu','ya','j','a','b','v','g','d','e','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','','a')); // bulgarian
			$seo_kw = $this->mb_strtr($seo_kw, array('أ','ب','ت','ث','ج','ح','خ','د','ذ','ر','ز','س','ش','ص','ض','ط','ظ','ع','غ','ف','ق','ك','ل','م','ن','ه','و','ي'), array('a','b','t','th','g','h','kh','d','th','r','z','s','sh','s','d','t','th','aa','gh','f','k','k','l','m','n','h','o','y')); // arabic
			$seo_kw = $this->mb_strtr($seo_kw, array('Ä','Ö','Ü','ä','ö','ü','ß','ẞ'), array('Ae','Oe','Ue','ae','oe','ue','ss','SS')); // german
			$seo_kw = $this->mb_strtr($seo_kw, array('ђ','ј','љ','њ','ћ','џ','đ','Ђ','Ј','Љ','Њ','Ћ','Џ','Đ'), array('dj','j','lj','nj','c','dz','dj','Dj','j','Lj','Nj','C','Dz','Dj',)); // serbian
			$seo_kw = $this->mb_strtr($seo_kw, array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','№'),
                                         array('a','b','v','g','d','e','yo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sh','','y','','e','yu','ya','A','B','V','G','D','E','Yo','Zh','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','H','C','Ch','Sh','Sh','','Y','','E','Yu','Ya','')); // russian
			$seo_kw = $this->mb_strtr($seo_kw, array('І', 'і', 'Ї', 'ї', 'Є', 'є', 'Ґ', 'ґ'), array('I','i','Yi','yi', 'Ye', 'ye', 'G', 'g')); // ukrainian
			*/
			
			// entities autodetect method (works for western europe languages)
			// $seo_kw = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($seo_kw, ENT_QUOTES, 'UTF-8'));
			// $seo_kw = html_entity_decode($seo_kw, ENT_QUOTES);
			// $seo_kw = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), $whitespace, $seo_kw);
			$seo_kw = trim($seo_kw, '_'.$whitespace);
		}
			
    if ($this->config->get('mlseo_lowercase')) {
      if (function_exists('mb_strtolower')) {
        $seo_kw = mb_strtolower($seo_kw, 'UTF-8');
      } else {
        $seo_kw = strtolower($seo_kw);
      }
		}
    
		$seo_kw = str_replace(' ', $whitespace, $seo_kw);
    
		//$seo_kw = str_replace(array('"',"'",'&','(',')','.','+',',','*',':',';','=','?','@','$','/','%','#'), '', $seo_kw);
		$seo_kw = str_replace(array('\'','`','‘','’'), $whitespace, $seo_kw);
		$seo_kw = str_replace(array('"','“','”','&','&amp;','+','?','/','%','#',',',':','&gt;','&lt;',';','<','>','(',')','™','®','©','&copy;','&reg;','&trade;'), '', $seo_kw);
    
    if ($whitespace) {
      if (function_exists('mb_ereg_replace')) {
        $seo_kw = mb_ereg_replace($whitespace.$whitespace.'+', $whitespace, $seo_kw);
      } else {
        $seo_kw = ereg_replace($whitespace.$whitespace.'+', $whitespace, $seo_kw);
      }
      $seo_kw = trim($seo_kw, $whitespace);
    }
		
		$exists = array(1);
    $number = '';
    $search_kw = $seo_kw;
    
    if ($type != 'image') {
      while(count($exists)) {
        /*
        if ($simulate && isset($this->session->data['kwCountArray']) && in_array($search_kw, (array) $this->session->data['kwCountArray'])) {
           $seo_kw .= '-[x]';
           break;
        }
        */
        
        if ($this->config->get('mlseo_absolute') && $this->config->get('mlseo_duplicate') && $type == 'category') break;
        elseif ($this->config->get('mlseo_absolute') && $type == 'category')
             $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = '".$type."_id=" . (int)$id . "' AND keyword='".$this->db->escape($search_kw)."'";
        elseif ($this->config->get('mlseo_duplicate'))
             $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE query != '".$type."_id=" . (int)$id . "' AND keyword='".$this->db->escape($search_kw)."'";
        else $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword='".$this->db->escape($search_kw)."'";
        
        $exists = $this->db->query($sql)->row;
        
        if (count($exists)) {
          $this->session->data['kwCountArray'][] = $seo_kw;
          $search_kw = $seo_kw .'-'. ++$number;
        }
        /*
        if ($this->config->get('mlseo_absolute')) break; // disable duplicate detection while absolute is enabled, todo: advanced detection of possible cases
        if ($this->config->get('mlseo_absolute') && $this->config->get('mlseo_duplicate') && $type == 'category') break;
        elseif ($this->config->get('mlseo_absolute') && $type == 'category')
             $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = '".$type."_id=" . (int)$id . "' AND keyword='".$this->db->escape($seo_kw)."'";
        elseif ($this->config->get('mlseo_duplicate'))
             $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE query != '".$type."_id=" . (int)$id . "' AND keyword='".$this->db->escape($seo_kw)."'";
        else $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword='".$this->db->escape($seo_kw)."'";
        $exists = $this->db->query($sql)->row;
        if (count($exists)) $seo_kw .= '_';
        */
      }
      
      if ($number) {
        if ($simulate) {
          $seo_kw .= '-[x]';
        } else {
          $seo_kw .= '-'.$number;
        }
      }
    }
		
	  // config here the max chars (will cut after the current word) ; 0 = unlimited
		$this->config->set('multilingual_seo_wordlimit', 0);
	    
    if ($this->config->get('mlseo_wordlimit')) {
      $pos = strpos($seo_kw, $whitespace, $this->config->get('mlseo_wordlimit'));
      if ($pos > $this->config->get('mlseo_wordlimit'))
        $seo_kw = substr($seo_kw, 0, $pos);
    }
    
		return $seo_kw;
	}
	
	public function getFullProductPaths($product_id) {
		$path = array();
		$categories = $this->db->query("SELECT c.category_id, c.parent_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id) WHERE product_id = '" . (int)$product_id . "'")->rows;
		
		foreach ($categories as $key => $category) {
			$path[$key] = '';
			if (!$category) continue;
			$path[$key] = $category['category_id'];
			
			while ($category['parent_id']) {
				$path[$key] = $category['parent_id'] . '_' . $path[$key];
				$category = $this->db->query("SELECT category_id, parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . $category['parent_id']. "'")->row;
			}
			
			$path[$key] = $path[$key];
		}
		
		if (!count($path)) return array();
		
		return $path;
	}
	
	public function getLangCode($lang_id) {
    $this->load->model('localisation/language');
    $languages = $this->model_localisation_language->getLanguages();
    
    foreach($languages as $language) {
      if ($language['language_id'] == $lang_id) {
        return  $language['code'];
      }
    }
    
    return '';
  }
  
	private function mb_strtr($str, $from, $to = null) {
		if (is_array($from) && is_array($to))
			return str_replace($from, $to, $str);
		elseif (is_array($from))
			return str_replace(array_keys($from), array_values($from), $str);
		return str_replace(preg_split('~~u', $from, null, PREG_SPLIT_NO_EMPTY), preg_split('~~u', $to, null, PREG_SPLIT_NO_EMPTY), $str);
	}
	
}

/**
 * A PHP port of URLify.js from the Django project
 * (https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js).
 * Handles symbols from Latin languages, Greek, Turkish, Bulgarian, Russian,
 * Ukrainian, Czech, Polish, Romanian, Latvian, Lithuanian, Vietnamese, Arabic,
 * Serbian, and Azerbaijani. Symbols it cannot transliterate
 * it will simply omit.
 *
 * Usage:
 *
 *     echo URLify::filter (' J\'étudie le français ');
 *     // "jetudie-le-francais"
 *
 *     echo URLify::filter ('Lo siento, no hablo español.');
 *     // "lo-siento-no-hablo-espanol"
 */
class URLify {
	public static $maps = array (
		'de' => array ( /* German */
			'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
			'ẞ' => 'SS'
		),
		'latin' => array (
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A','Ă' => 'A', 'Æ' => 'AE', 'Ç' =>
			'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
			'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' =>
			'O', 'Ő' => 'O', 'Ø' => 'O', 'Œ' => 'OE' ,'Ș' => 'S','Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U',
			'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' =>
			'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
			'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' =>
			'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 'ø' => 'o', 'œ' => 'oe', 'ș' => 's', 'ț' => 't', 'ù' => 'u', 'ú' => 'u',
			'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y'
		),
		'latin_symbols' => array (
			'©' => '(c)'
		),
		'el' => array ( /* Greek */
			'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
			'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
			'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
			'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
			'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
			'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
			'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
			'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
			'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
			'Ϋ' => 'Y'
		),
		'tr' => array ( /* Turkish */
			'ş' => 's', 'Ş' => 'S', 'ı' => 'i', 'İ' => 'I', 'ç' => 'c', 'Ç' => 'C', 'ü' => 'u', 'Ü' => 'U',
			'ö' => 'o', 'Ö' => 'O', 'ğ' => 'g', 'Ğ' => 'G'
		),
		'bg' => array( /* Bulgarian */
			"Щ" => 'Sht', "Ш" => 'Sh', "Ч" => 'Ch', "Ц" => 'C', "Ю" => 'Yu', "Я" => 'Ya',
			"Ж" => 'J',   "А" => 'A',  "Б" => 'B',  "В" => 'V', "Г" => 'G',  "Д" => 'D',
			"Е" => 'E',   "З" => 'Z',  "И" => 'I',  "Й" => 'Y', "К" => 'K',  "Л" => 'L',
			"М" => 'M',   "Н" => 'N',  "О" => 'O',  "П" => 'P', "Р" => 'R',  "С" => 'S',
			"Т" => 'T',   "У" => 'U',  "Ф" => 'F',  "Х" => 'H', "Ь" => '',   "Ъ" => 'A',
			"щ" => 'sht', "ш" => 'sh', "ч" => 'ch', "ц" => 'c', "ю" => 'yu', "я" => 'ya',
			"ж" => 'j',   "а" => 'a',  "б" => 'b',  "в" => 'v', "г" => 'g',  "д" => 'd',
			"е" => 'e',   "з" => 'z',  "и" => 'i',  "й" => 'y', "к" => 'k',  "л" => 'l',
			"м" => 'm',   "н" => 'n',  "о" => 'o',  "п" => 'p', "р" => 'r',  "с" => 's',
			"т" => 't',   "у" => 'u',  "ф" => 'f',  "х" => 'h', "ь" => '',   "ъ" => 'a'
		),
		'ru' => array ( /* Russian */
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
			'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
			'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
			'я' => 'ya',
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
			'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
			'Я' => 'Ya',
			'№' => ''
		),
		'uk' => array ( /* Ukrainian */
			'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G', 'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g'
		),
		'cs' => array ( /* Czech */
			'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
			'ž' => 'z', 'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
			'Ů' => 'U', 'Ž' => 'Z'
		),
		'pl' => array ( /* Polish */
			'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
			'ż' => 'z', 'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'O', 'Ś' => 'S',
			'Ź' => 'Z', 'Ż' => 'Z'
		),
		'ro' => array ( /* Romanian */
			'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'Ţ' => 'T', 'ţ' => 't'
		),
		'lv' => array ( /* Latvian */
			'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
			'š' => 's', 'ū' => 'u', 'ž' => 'z', 'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i',
			'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z'
		),
		'lt' => array ( /* Lithuanian */
			'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'š' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z',
			'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z'
		),
		'vn' => array ( /* Vietnamese */
			'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
			'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
			'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
			'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
			'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
			'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
			'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
			'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
			'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
			'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
			'Đ' => 'D', 'đ' => 'd'
		),
		'ar' => array ( /* Arabic */
			'أ' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'g', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
			'ذ' => 'th', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't',
			'ظ' => 'th', 'ع' => 'aa', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'k', 'ك' => 'k', 'ل' => 'l', 'م' => 'm',
			'ن' => 'n', 'ه' => 'h', 'و' => 'o', 'ي' => 'y'
		),
		'az' => array ( /* Azerbaijani */
			'ç' => 'c', 'ə' => 'e', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
			'Ç' => 'C', 'Ə' => 'E', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U'
		)
	);

	/**
	 * List of words to remove from URLs.
	 */
	public static $remove_list = array (
		'a', 'an', 'as', 'at', 'before', 'but', 'by', 'for', 'from',
		'is', 'in', 'into', 'like', 'of', 'off', 'on', 'onto', 'per',
		'since', 'than', 'the', 'this', 'that', 'to', 'up', 'via',
		'with'
	);

	/**
	 * The character map.
	 */
	private static $map = array ();

	/**
	 * The character list as a string.
	 */
	private static $chars = '';

	/**
	 * The character list as a regular expression.
	 */
	private static $regex = '';

	/**
	 * The current language
	 */
	private static $language = '';

	/**
	 * Initializes the character map.
	 */
	private static function init ($language = "") {
		if (count (self::$map) > 0 && (($language == "") || ($language == self::$language))) {
			return;
		}

		/* Is a specific map associated with $language ? */
		if (isset(self::$maps[$language]) && is_array(self::$maps[$language])) {
			/* Move this map to end. This means it will have priority over others */
			$m = self::$maps[$language];
			unset(self::$maps[$language]);
			self::$maps[$language] = $m;
		}
		/* Reset static vars */
		self::$language = $language;
		self::$map = array();
		self::$chars = '';

		foreach (self::$maps as $map) {
			foreach ($map as $orig => $conv) {
				self::$map[$orig] = $conv;
				self::$chars .= $orig;
			}
		}

		self::$regex = '/[' . self::$chars . ']/u';
	}

	/**
	 * Add new characters to the list. `$map` should be a hash.
	 */
	public static function add_chars ($map) {
		if (! is_array ($map)) {
			throw new LogicException ('$map must be an associative array.');
		}
		self::$maps[] = $map;
		self::$map = array ();
		self::$chars = '';
	}

	/**
	 * Append words to the remove list. Accepts either single words
	 * or an array of words.
	 */
	public static function remove_words ($words) {
		$words = is_array ($words) ? $words : array ($words);
		self::$remove_list = array_merge (self::$remove_list, $words);
	}

	/**
	 * Transliterates characters to their ASCII equivalents.
     * $language specifies a priority for a specific language.
     * The latter is useful if languages have different rules for the same character.
	 */
	public static function downcode ($text, $language = "") {
		self::init ($language);

		if (preg_match_all (self::$regex, $text, $matches)) {
			for ($i = 0; $i < count ($matches[0]); $i++) {
				$char = $matches[0][$i];
				if (isset (self::$map[$char])) {
					$text = str_replace ($char, self::$map[$char], $text);
				}
			}
		}
		return $text;
	}

	/**
	 * Filters a string, e.g., "Petty theft" to "petty-theft"
	 * @param string $text The text to return filtered
	 * @param int $length The length (after filtering) of the string to be returned
	 * @param string $language The transliteration language, passed down to downcode()
	 * @param bool $file_name Whether there should be and additional filter considering this is a filename
	 * @param bool $use_remove_list Whether you want to remove specific elements previously set in self::$remove_list
	 * @param bool $lower_case Whether you want the filter to maintain casing or lowercase everything (default)
	 * @param bool $treat_underscore_as_space Treat underscore as space, so it will replaced with "-"
	 */
	public static function filter ($text, $length = 999, $language = "", $file_name = false, $use_remove_list = true, $lower_case = true, $treat_underscore_as_space = true) {
		$text = self::downcode ($text,$language);

		if ($use_remove_list) {
			// remove all these words from the string before urlifying
			$text = preg_replace ('/\b(' . join ('|', self::$remove_list) . ')\b/i', '', $text);
		}

		// if downcode doesn't hit, the char will be stripped here
		$remove_pattern = ($file_name) ? '/[^_\-.\-a-zA-Z0-9\s]/u' : '/[^\s_\-a-zA-Z0-9]/u';
		$text = preg_replace ($remove_pattern, '', $text); // remove unneeded chars
		if ($treat_underscore_as_space) {
		    	$text = str_replace ('_', ' ', $text);             // treat underscores as spaces
		}
		$text = preg_replace ('/^\s+|\s+$/u', '', $text);  // trim leading/trailing spaces
		$text = preg_replace ('/[-\s]+/u', '-', $text);    // convert spaces to hyphens
		if ($lower_case) {
			$text = strtolower ($text);                        // convert to lowercase
		}

		return trim (substr ($text, 0, (int)$length), '-');     // trim to first $length chars
	}

	/**
	 * Alias of `URLify::downcode()`.
	 */
	public static function transliterate ($text) {
		return self::downcode ($text);
	}
}