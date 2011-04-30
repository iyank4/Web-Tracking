<?php
/**
 * Curl: psuedo implements cUrl for posting data to td ameritrade api
 * @author cnizz.com
 * @param void
 * @uses *cUrl
 */
class Curl
{
	/**
	 * constructor
	 * @author cn
	 * @param url(str),paramArr(array),$jsession_id(str)
	 */
	function __construct(){

	}

	/**
	 * post: posts parameters to url
	 * @author cnizz.com
	 * @param url(str), paramArr(array), returnString((bool - forces return as string)
	 * @return array(default)
	 */
	public function post($url,$paramArr=array(),$returnsString=0,$url_encode=0)
	{
		$string = $this->returnPostParamStr($paramArr);
		if($url_encode==1){
			$string=urlencode($string)."\n\n";
		}
		$ch = curl_init(); /// initialize a cURL session
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
		curl_setopt($ch, CURLOPT_TIMEOUT, '60');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_PROXY, 'i-wirelessinnovation.com:1877');
		
		$cUrlResponse = curl_exec($ch);
		$httpResponseArr = curl_getinfo($ch);
		curl_close($ch);

		$return = $this->xml2array($cUrlResponse);

		if(!is_array($return) || $return['result']=='FAIL' || $httpResponseArr['http_code']!=200)
		{
			$return['custome_error'] = $cUrlResponse;
			$return['custom_http_code'] = $httpResponseArr['http_code'];
		}

		return $return;
	}

	/**
	 * returnPostParamStr: accepts an array and returns a parameter string
	 * @author cnizz.com
	 * @param array (key is post name, value is post attribute)
	 * @return string
	 */
	public function returnPostParamStr($val)
	{
		$string='';
		if(is_array($val))
		{
			foreach($val as $k => $i)
			{
				if($j==0){
					$string.="$k=$i";
					$j=1;
				}
				else if($k=='|S'){
					$string.="$k=$i";
				}
				else{
					$string.="&$k=$i";
				}
			}
		}
		return $string;
	}

	/**
	 * xml2array: converts xml to an array
	 * @author unknown, modified by cn
	 * @link http://us.php.net/manual/en/function.xml-parse.php#87920
	 * @param xmlStr(string)
	 * @return array
	 */
	public function xml2array($xmlStr, $get_attributes = 1, $priority = 'tag')
	{
		// I renamed $url to $xmlStr, $url was the first parameter in the method if you
		// want to load from a URL then rename $xmlStr to $url everywhere in this method
	    $contents = "";
	    if (!function_exists('xml_parser_create'))
	    {
	        return array ();
	    }
	    $parser = xml_parser_create('');
		// commented out since I already have the xml text stored in memory
		// this reads XML in from a URL
		/*
		if (!($fp = @ fopen($url, 'rb')))
	    {
	        return array ();
	    }
	    while (!feof($fp))
	    {
	        $contents .= fread($fp, 8192);
	    }
	    fclose($fp);
		*/
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($xmlStr), $xml_values);
	    xml_parser_free($parser);
	    if (!$xml_values)
	        return; //Hmm...
	    $xml_array = array ();
	    $parents = array ();
	    $opened_tags = array ();
	    $arr = array ();
	    $current = & $xml_array;
	    $repeated_tag_index = array ();
	    foreach ($xml_values as $data)
	    {
	        unset ($attributes, $value);
	        extract($data);
	        $result = array ();
	        $attributes_data = array ();
	        if (isset ($value))
	        {
	            if ($priority == 'tag')
	                $result = $value;
	            else
	                $result['value'] = $value;
	        }
	        if (isset ($attributes) and $get_attributes)
	        {
	            foreach ($attributes as $attr => $val)
	            {
	                if ($priority == 'tag')
	                    $attributes_data[$attr] = $val;
	                else
	                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	            }
	        }
	        if ($type == "open")
	        {
	            $parent[$level -1] = & $current;
	            if (!is_array($current) or (!in_array($tag, array_keys($current))))
	            {
	                $current[$tag] = $result;
	                if ($attributes_data)
	                    $current[$tag . '_attr'] = $attributes_data;
	                $repeated_tag_index[$tag . '_' . $level] = 1;
	                $current = & $current[$tag];
	            }
	            else
	            {
	                if (isset ($current[$tag][0]))
	                {
	                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
	                    $repeated_tag_index[$tag . '_' . $level]++;
	                }
	                else
	                {
	                    $current[$tag] = array (
	                        $current[$tag],
	                        $result
	                    );
	                    $repeated_tag_index[$tag . '_' . $level] = 2;
	                    if (isset ($current[$tag . '_attr']))
	                    {
	                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
	                        unset ($current[$tag . '_attr']);
	                    }
	                }
	                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
	                $current = & $current[$tag][$last_item_index];
	            }
	        }
	        elseif ($type == "complete")
	        {
	            if (!isset ($current[$tag]))
	            {
	                $current[$tag] = $result;
	                $repeated_tag_index[$tag . '_' . $level] = 1;
	                if ($priority == 'tag' and $attributes_data)
	                    $current[$tag . '_attr'] = $attributes_data;
	            }
	            else
	            {
	                if (isset ($current[$tag][0]) and is_array($current[$tag]))
	                {
	                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
	                    if ($priority == 'tag' and $get_attributes and $attributes_data)
	                    {
	                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
	                    }
	                    $repeated_tag_index[$tag . '_' . $level]++;
	                }
	                else
	                {
	                    $current[$tag] = array (
	                        $current[$tag],
	                        $result
	                    );
	                    $repeated_tag_index[$tag . '_' . $level] = 1;
	                    if ($priority == 'tag' and $get_attributes)
	                    {
	                        if (isset ($current[$tag . '_attr']))
	                        {
	                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
	                            unset ($current[$tag . '_attr']);
	                        }
	                        if ($attributes_data)
	                        {
	                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
	                        }
	                    }
	                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
	                }
	            }
	        }
	        elseif ($type == 'close')
	        {
	            $current = & $parent[$level -1];
	        }
	    }
	    return ($xml_array);
	}
}
?>
