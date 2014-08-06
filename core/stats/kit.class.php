<?php
/*
 * Xibo - Digitial Signage - http://www.xibo.org.uk
 * Copyright (C) 2006,2007,2008 Daniel Garner and James Packer
 *
 * This file is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version. 
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('XIBO') or die("Sorry, you are not allowed to directly access this page.<br /> Please press the back button in your browser.");
  
define('_SESSION', "session");
define('_POST', "post");
define('_GET', "get");
define('_REQUEST', "request");

define('_STRING', "string");
define('_HTMLSTRING', 'htmlstring');
define('_PASSWORD', "password");
define('_INT', "int");
define('_DOUBLE', "double");
define('_BOOL', "bool");
define('_WORD', "word");
define('_ARRAY', "array");
define('_USERNAME', "username");
define('_CHECKBOX', "checkbox");
define('_FILENAME', "filename");
define('_URI', "uri");

class Kit 
{
	// Ends the current execution and issues a redirect - should only be called before headers have been sent (i.e. no output)
	static function Redirect($page, $message = '', $pageIsUrl = false)
	{
		$url 	= $page;
		$ajax 	= Kit::GetParam('ajax', _REQUEST, _BOOL, false);
		
		if ($ajax)
		{
			echo json_encode($page);
			die();
		}
		
		// Header or JS redirect
		if (headers_sent()) 
		{
			echo "<script>document.location.href='$url';</script>\n";
		} 
		else 
		{
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: ' . $url );
		}
		
		die();
	}
	
	/**
	 * Gets the appropriate Param, making sure its valid
	 * Based on code from Joomla! 1.5
	 * @return 
	 * @param $param Object
	 * @param $source Object[optional]
	 * @param $type Object[optional]
	 * @param $default Object[optional]
	 */
	static public function GetParam($param, $source = _POST, $type = _STRING, $default = '')
	{
		if (is_array($source))
		{
			if(!isset($source[$param])) 
			{
				$return = $default;
			}
			else 
			{
				$return = $source[$param];	
			}
		}
		else
		{
			switch ($source)
			{
				case 'session':
				
					if(!isset($_SESSION[$param])) 
					{
						$return = $default;
					}
					else if ($type == _CHECKBOX)
					{
						// this means that it was defined correctly and it was set
						$return = 1;
					}
					else 
					{
						$return = $_SESSION[$param];	
					}
				
					break;
				
				case 'request':
				
					if(!isset($_REQUEST[$param])) 
					{
						$return = $default;
					}
					else 
					{
						$return = $_REQUEST[$param];	
					}
				
					break;
					
				case 'get':
				
					if(!isset($_GET[$param])) 
					{
						$return = $default;
					}
					else 
					{
						$return = $_GET[$param];	
					}
				
					break;
					
				case 'post':
		
					if(!isset($_POST[$param])) 
					{
						$return = $default;
					}
					else if ($type == _CHECKBOX)
					{
						// this means that it was defined correctly and it was set
						$return = 1;
					}
					else 
					{
						$return = $_POST[$param];	
					}
				
					break;
				
				default:
					return $default;
			}
		}
		
		// Validate this param	
		return Kit::ValidateParam($return, $type);
	}
	
	/**
	 * Validates a Parameter
	 * Based on code from Joomla! 1.5
	 * @return 
	 * @param $param Object
	 * @param $type Object
	 */
	static function ValidateParam($param, $type)
	{
		// If we are a NULL always return a null
		if ($param == "") return "";
		
		$return = $param;
		
		// Validate
		// Handle the type constraint
		switch ($type)
		{
			case _INT :
				// Only use the first integer value
				@ preg_match('/-?[0-9]+/', $return, $matches);
				$return = @ (int) $matches[0];
				break;

			case _DOUBLE :
				// Only use the first floating point value
				@ preg_match('/-?[0-9]+(\.[0-9]+)?/', $return, $matches);
				$return = @ (float) $matches[0];
				break;

			case _BOOL :
				$return = (bool) $return;
				break;

			case _ARRAY :
				if (!is_array($return)) 
				{
					$return = array ($return);
				}
				break;

			case _STRING :
			case _PASSWORD :
				$return = htmlentities($return);
				$return = preg_replace('/&#(\d+);/me', "chr(\\1)", $return); // decimal notation
				// convert hex
				$return = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $return); // hex notation
				$return = (string) $return;
				break;
				
			case _HTMLSTRING :
				$return = preg_replace('/&#(\d+);/me', "chr(\\1)", $return); // decimal notation
				// convert hex
				$return = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $return); // hex notation
				$return = (string) $return;
				break;

			case _WORD :
				$return = (string) preg_replace( '/[^A-Z_]\\-/i', '', $return );
				break;
				
			case _USERNAME :
				$return = (string) preg_replace( '/[\x00-\x1F\x7F<>"\'%&]/', '', $return );
				$return	= strtolower($return);
				break;
				
			case _FILENAME :
				// Remove non alphanumerics
				$return = strtolower($return); 
				$code_entities_match 	= array('&quot;' ,'!' ,'@' ,'#' ,'$' ,'%' ,'^' ,'&' ,'*' ,'(' ,')' ,'+' ,'{' ,'}' ,'|' ,':' ,'"' ,'<' ,'>' ,'?' ,'[' ,']' ,'' ,';' ,"'" ,',' ,'_' ,'/' ,'*' ,'+' ,'~' ,'`' ,'=' ,' ' ,'---' ,'--','--'); 
				$code_entities_replace 	= array('' ,'-' ,'-' ,'' ,'' ,'' ,'-' ,'-' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'-' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'-' ,'-' ,'-' ,'' ,'' ,'' ,'' ,'' ,'-' ,'-' ,'-','-'); 
	
				$return = str_replace($code_entities_match, $code_entities_replace, $return);
				break;
				
			case _URI :
				$return = (string) str_replace(' ', '-', $return);
				$return = (string) str_replace("'", '', $return);
				break;
				
			case _CHECKBOX:
				if ($return == 'on') $return = 1;
				if ($return == 'off') $return = 0;

			default :
				// No casting necessary
				break;
		}
		
		return $return;
	}
	
	/**
	 * Gets a formatted Url
	 * @return 
	 * @param $page Object[optional]
	 */
	public static function GetURL($page = "")
	{
		$page = ValidateParam($page, _WORD);
		$fullUrl = 'http';
		
		if($_SERVER['HTTPS']=='on')
		{
			$fullUrl .=  's';
		}
		
		$fullUrl .=  '://';
		
		if($_SERVER['SERVER_PORT']!='80')
		{
			$fullUrl .=  $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
		}
		else
		{
			$fullUrl .=  $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		}
		
		// Append the page if its not empty
		if ($page != '')
		{
			$fullUrl .= '?p=' . $page;
		}
		
		return $fullUrl;
	}
}
?>
