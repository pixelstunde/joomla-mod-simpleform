<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simpleform
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class ModSimpleFormHelper
{
	/**
	 * create array from string
	 * 
	 * @param string $formContents string of contents
	 * @return array contents of form
	 */
	private static function toArray($formContents){
		return eval('return '.$formContents.';');
	}
	
	/**
	 * create form
	 * 
	 * @param string $formContents string of contents
	 * @param bool $labels show labels
	 * @param bool $placeholders show placeholders
	 * @param bool $showCaptcha show Captcha
	 * @return array contents of form
	 */
	public static function createForm($formContents, $labels, $placeholders, $showCaptcha){
		//add csrf-token
		$output = JHtml::_('form.token');
		
		$formContents = ModSimpleFormHelper::toArray($formContents);
		$number = 0;
		
		foreach ($formContents as $item){
			$type = array_keys($item)[0];
			$values = array_values($item)[0];
			$temp = '';
			$name = is_string($values) ? $values : $values['name'];
			
			//add content before input
			if (!empty($values['before'])){
				$temp .= '<div class="before">'.$values['before'].'</div>';
			}
			
			switch($type){
				case 'file':
					//no break here, since we treat it as normal input file
					$maxSize = $values['maxSize'] ? $values['maxSize'] : 500000;
					$temp .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxSize.'" />';
				case 'text':
				case 'color':
				case 'date':
				case 'datetime':
				case 'datetime-local':
				case 'email':
				case 'month':
				case 'number':
				case 'range':
				case 'search':
				case 'tel':
				case 'time':
				case 'url':
				case 'week':
					$temp .= ModSimpleFormHelper::input($name, $type, $placeholders, $labels);
					break;
				case 'textarea':
					$temp .= ModSimpleFormHelper::textarea($name, $placeholders, $labels);
					break;
				case 'checkbox':
				case 'radio':
					$temp .= ModSimpleFormHelper::checkbox($values, $type);
					break;
				case 'submit':
				case 'reset':
				case 'button':
					if ($showCaptcha && (("submit" == $type || "reset" == $type) && !strstr($output,'dynamic_recaptcha_1'))){
						$showCaptcha = false;
						$temp .= '<div class="solve-captcha">'.JText::_('MOD_SIMPLEFORM_PLEASE_SOLVE_CAPTCHA').'</div>';
						$temp .= (JEventDispatcher::getInstance())->trigger('onDisplay', array(null, 'dynamic_recaptcha_1', 'class=""'))[0];
					}
					$temp .= ModSimpleFormHelper::button($name, $type);
					break;
				case 'select':
					$temp .= ModSimpleFormHelper::select($values);
					break;
				case 'copy':
					$temp .= ModSimpleFormHelper::checkbox(array('values' => array($name)), 'checkbox');
					break;
			}
			
			//add content after input
			if (!empty($values['after'])){
				$temp .= '<div class="after">'.$values['after'].'</div>';
			}
			
			$output .= '<div
							class="row el-'.$number++.' '.
							$type.'"
						>'.
							$temp.
						'</div>';
		}
		return preg_replace('/[\s\r\n\t]+/', ' ', $output);
	}
	
	/**
	 * Create html label
	 * 
	 * @param string $name name of input element
	 * @return string html label
	 */
	private static function label($name, $content = null){
		$content = $content ? $content : $name;
		return '<label
					for="id_'.ModSimpleFormHelper::fix($name).'"
				>'.
					$content.
				'</label>';
	}
	
	/**
	 * Create simple input element
	 * 
	 * @param string $name name of input element
	 * @param string $type type of input element
	 * @param bool $placeholders use placeholder
	 * @return string input html
	 */
	private static function input($name, $type="text", $placeholders, $labels){
		if ($labels) $output = ModSimpleFormHelper::label($name);
		if ('*' == substr($name,-1)){
			$required = ' required="required" ';
			$name = substr($name,0,-1);
		}
		else {
			$required = ' ';
		} 
		$placeholder = $placeholders ? 'placeholder="'.$name.'"' :'';
		$output .= '<input
						type="'.$type.'" '.
						$placeholder.
						$required.
						'id="id_'.ModSimpleFormHelper::fix($name).'"
						name="'.ModSimpleFormHelper::fix($name).'"
					/>';
	    return $output;
	}
	
	/**
	 * Create simple textarea element
	 * 
	 * @param string $name name of input element
	 * @param bool $placeholders use placeholder
	 * @return string textarea html
	 */
	private static function textarea($name, $placeholders, $labels){
		if ($labels) $output = ModSimpleFormHelper::label($name);
		if ('*' == substr($name,-1)){
			$required = ' required="required" ';
			$name = substr($name,0,-1);
		}
		else {
			$required = ' ';
		} 
		$placeholder = $placeholders ? ' placeholder="'.$name.'" ' :' ';
		$output .= '<textarea'.
						$placeholder.
						$required.
						'id="id_'.ModSimpleFormHelper::fix($name).'"
						name="'.ModSimpleFormHelper::fix($name).'"></textarea>';
		return $output;
	}
	
	/**
	 * Create simple checkbox/radio element
	 *
	 * @param string $value value of input element
	 * @param string $group group of input element
	 * @param string $type type of input element
	 * @return string checkbox html
	 */
	private static function checkbox($params, $type){
		$output = '<div class="selection-header">'.$params['name'].'</div>';
		
		if (is_array($params['values'])){
			foreach($params['values'] as $value => $name){
				$active = '';
				if (!empty($params['active'])){
					foreach($params['active'] as $a){
						if ($a == $name){
							$active = ' checked="checked" ';
						}
					}
				}
				$origname = $name;
				if ('*' == substr($name,-1)){
					$required = ' required="required" ';
					$name = substr($name,0,-1);
				}
				else {
					$required = ' ';
				}
				$value = !empty($value) ? ModSimpleFormHelper::fix($value) : ModSimpleFormHelper::fix($name);
				$checkbox = '<input
								type="'.$type.'"'.
								$active.
								$required.
								'id="id_'.ModSimpleFormHelper::fix($name).'"
								name="'.ModSimpleFormHelper::fix($params['name']).'[]"
								value="'.$value.'"
							/>';
				$output .= ModSimpleFormHelper::label($origname, $origname.' '.$checkbox);
			}
		}	
		return $output;
	}
	
	/**
	 * Create simple button/reset/submit
	 *
	 * @param string $value value of input element
	 * @param string $type type of input element
	 * @return string button html
	 */
	private static function button($name, $type){
		$id = 'id_'.ModSimpleFormHelper::fix($name);
		$output = '<button
						class="btn btn-primary"
						type="'.$type.'"'.
						' id="'.$id.'"'.
					'>'.
						$name.
					'</button>';
	    return $output;
	}
	
	/**
	 * Create simple checkbox/radio element
	 *
	 * @param mixed $params params to process
	 * @return string checkbox html
	 */
	private static function select($params){
		$name = $params['name'];
		$values = $params['values'];
		$active = empty($params['active']) ? false : $params['active'];
		$multiple = empty($params['multiple']) ? '': ' multiple="multiple"';
		
		$output = ModSimpleFormHelper::label($name);
		
		$name = ModSimpleFormHelper::fix($name);
		if ('*' == substr($name,-1)){
			$required = ' required="required" ';
			$name = substr($name,0,-1);
		}
		else {
			$required = ' ';
		} 
		$output.= '<select
					style="color:#000"
					name="'.$name.'"
					id="id_'.$name.'"'.
					$multiple.
					$required.'
					>';
		
		foreach($values as $value => $name){
			$selected = '';
			if (!empty($params['active'])){
				foreach($params['active'] as $a){
					if ($a == $name){
						$selected = ' selected="selected" ';
					}
				}
			}
			if ($name != $active){
				$output .= '<option';
			}
			else{
				$output .= '<option
								selected="selected"';
			}
			$value = !empty($value) ? ModSimpleFormHelper::fix($value) : ModSimpleFormHelper::fix($name);
			$output .=  $selected.
						' value="'.$value.'">'.
							$name.'
						</option>';
		}
		$output .= '</select>';
	    return $output;
	}

	/**
	 * Remove spaces, replace all but alphanumerical characters
	 * 
	 * @param string $string input string
	 * @return string cleaned string
	 */
	private static function fix($string){
		$search = array('@[,\+/]+@',
						'@\s+@',
						'@[^\.a-zA-Z0-9_-]+@'
						);
		$replace = array('-',
						 '_',
						 '',
						);
		$string = preg_replace($search,$replace,$string);
		return $string;
	}
	
	/**
	 * Process form
	 *
	 * @todo check for required fields on server side
	 * @param mixed $params module params
	 * @return null
	 */
	public static function processForm($params){
		
		$showCaptcha = $params->get('showcaptcha');
		
		JSession::checkToken() or die( 'Invalid Token' );
		if ('submit' === $_POST['simpleform']){
			$captchaSolved = false;
			if ($showCaptcha){
				$res = (JEventDispatcher::getInstance())->trigger('onCheckAnswer',$_POST['recaptcha_response_field']);
				if($res[0]){
					$captchaSolved = true;
				}
			}
			if($captchaSolved || !$showCaptcha){
				$content='';
				$formContents = ModSimpleFormHelper::toArray($params->get('formcontents'));
				
				foreach ($formContents as $item){
					//make sure to process only the things that should have been requested
					//someone could have been manipulating the form, adding files or something else
					
					$result = null;
					$type = array_keys($item)[0];
					$values = array_values($item)[0];
					$name = is_string($values) ? $values : $values['name'];
					$name = ModSimpleFormHelper::fix($name);
					
					if (in_array($type,array('submit', 'button', 'reset'))){
						continue;
					}
					
					if ('file' == $type){
						$result = ModSimpleFormHelper::processUploadFile($name, $params->get('uploaddir', 'images'),@$values['allowedExtensions']);
						if ($result !== null) //no file
							{if ('error' == $result[0]){
								JFactory::getApplication()->enqueueMessage(JText::_('MOD_SIMPLEFORM_FILE_ERROR').':'.$result[1], 'error');
								return false;
							}
							else{
								$result = $result[1];
							}
						}
					}
					
					if (('simpleform' !== $name) && ("g-recaptcha-response" !== $name)){
						$curval = $result ? $result : $_POST[$name]; //needed when overriding value (e.g. file uploads)
						if (is_array($curval)){
							$curval = '('.implode(',',$curval).')';
						}
						$content.= '<b>'.$name .':</b> '.nl2br($curval)."<br/>";
					}
				}
				if (true === ModSimpleFormHelper::sendMail($params->get('receiver'), $params->get('subject'), $content)
					){
					$message = empty($params->get('successmessage')) ? JText::_('MOD_SIMPLEFORM_MAIL_SENT'): $params->get('successmessage');
					JFactory::getApplication()->enqueueMessage($message);
					return true;
				}
				else {
					JFactory::getApplication()->enqueueMessage(JText::_('MOD_SIMPLEFORM_MAIL_SENT_ERROR'), 'error');
				}
			}
			else{
				$message = empty($params->get('errormessage')) ? JText::_('MOD_SIMPLEFORM_CAPTCHA_ERROR'): $params->get('errormessage');
				JFactory::getApplication()->enqueueMessage($message, 'error');
			}
		}
		else {
			//something nasty happened
			JFactory::getApplication()->enqueueMessage(JText::_('MOD_SIMPLEFORM_POST_ERROR'), 'error');
		}
		return false;
	}
	
	/**
	 * Send email
	 * 
	 * @param string $recipient email recipient
	 * @param string $subject subject of email
	 * @param string $content content of email
	 * @return bool status
	 */	
	private static function sendMail($recipient, $subject, $content){		
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get('mailfrom'),
			$config->get('fromname') 
		);
		$mailer = JFactory::getMailer();
		$mailer->setSender($sender);
		$mailer->addRecipient(explode(',',$recipient));
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($content);
		$send = $mailer->Send();
		if ( true !== $send ) {
			return false;
		}
		return true;
	}
	
	
	private static function processUploadFile($name, $dir='images', $allowedExtensions = null){
		$file = $_FILES[$name];
		$error = $file['error'];
		
		if (4 == $error) return null; //no file uploaded, skip the rest
		
		if (empty($error)){
			$filename = $file['name'];
			$tmp = $file['tmp_name'];
			
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			
			if (!empty($allowedExtensions)){
				if (!in_array($extension, $allowedExtensions)){
						return array('error',JText::_('MOD_SIMPLEFORM_CHECK_FILE_EXTENSIONS'));
					}
			}
			else{
                            $banned = array('php', 'html', 'htaccess', 'htpasswd');
                            foreach($banned as $current){
                                if (stristr($extension, $current)){
                                    return array('error',JText::_('MOD_SIMPLEFORM_CHECK_FILE_EXTENSIONS'));
                                }
                            }
			}

			$uploaddir = JPATH_ROOT.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
			$uploadfile = time().'-'. ModSimpleFormHelper::fix(basename($filename));
			
			$extern = '<a href="'.JURI::base().$dir.'/'.$uploadfile.'">Anhang öffnen</a>';
			if (move_uploaded_file($file['tmp_name'], $uploaddir.$uploadfile)){				
				return array('success', $extern);
			}
			else{
				return array ('error', JText::_('MOD_SIMPLEFORM_CANNOT_MOVE_UPLOADED_FILE'));
			}
		}
		return array('error',$error);
	}
}
