<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simpleform
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
	if (!empty($_POST)){
		ModSimpleFormHelper::processForm($params);
		if ($hideAfterSubmit) return; 
	}

?>
<form class="simpleform" method="post" action="#system-message" enctype="multipart/form-data">
	<fieldset>
		<input type="hidden" value="submit" name="simpleform" />
		<?php echo $form; ?>
	</fieldset>
</form>

