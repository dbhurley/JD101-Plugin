<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 *
 * @copyright   Copyright (C) 2013 David Hurley. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Link Protect Content Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 * @since  		3.0
 */
class PlgContentLinkprotect extends JPlugin
{
	private $callbackFunction;

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		require_once __DIR__ . '/helper/helper.php';
		$helper = new LinkProtectHelper($this->params);
		$this->callbackFunction = array($helper, 'replaceLinks');
	}

	/**
	 * Initiate the plugin
	 * @param  string $context The context of the content passes to the plugin
	 * @param  object $article The article object
	 * @param  object $params  The article params
	 *
	 * @return boolean         True if the function is bypassed. Else True/False based on the replacement action
	 */
	public function onContentBeforeDisplay($context, $article, $params)
	{
		$parts 	= explode(".", $context);
		if ($parts[0] != "com_content")
		{
			return;
		}

		if (stripos($article->text, '{linkprotect=off}') === true)
		{
			$article->text = str_ireplace('{linkprotect=off}', '', $article->text);
		}

		$app = JFactory::getApplication();
		$external = $app->input->get('external', NULL);

		if ($external)
		{
			LinkProtectHelper::leaveSite($article, $external);
		} else 
		{
			$pattern = '@href=("|\')(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)("|\')@';
			$article->text = preg_replace_callback($pattern, $this->callbackFunction, $article->text);
		}

	}
}