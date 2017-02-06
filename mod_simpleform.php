<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simpleform
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the menu functions only once
require_once __DIR__ . '/helper.php';

$formContents	= $params->get('formcontents');
$useLabels		= $params->get('uselabels');
$usePlaceholders= $params->get('useplaceholders');
$showCaptcha	= $params->get('showcaptcha');
$hideAfterSubmit= $params->get('hideaftersubmit');

if ($showCaptcha){
	JPluginHelper::importPlugin('captcha');
	$ed = JEventDispatcher::getInstance();
	$ed->trigger('onInit', 'dynamic_recaptcha_1');
}

$form = ModSimpleFormHelper::createForm($formContents, $useLabels, $usePlaceholders, $showCaptcha);

require JModuleHelper::getLayoutPath('mod_simpleform', $params->get('layout', 'default'));
