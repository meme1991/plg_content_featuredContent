<?php
# @Author: SPEDI srl
# @Date:   30-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 30-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (c) SPEDI srl

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge')){
	jimport('joomla.html.parameter');
}

class plgContentFeaturedContent extends JPlugin {

	var $plg_name = "featuredcontent";
	var $plg_tag  = "featuredContent";

	function plgContentArticleGallery( &$subject, $params ){
		parent::__construct( $subject, $params );

		// Define the DS constant under Joomla! 3.0+
		if (!defined('DS')){
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	// Joomla! 2.5+
	function onContentPrepare($context, &$row, &$params, $page = 0){
		$this->renderPhGallery($row, $params, $page = 0);
	}

	// The main function
	function renderPhGallery(&$row, &$params, $page = 0){

		// API
		jimport('joomla.filesystem.file');
		$mainframe    = JFactory::getApplication();
		$document     = JFactory::getDocument();
		$db           = JFactory::getDbo();
		//$siteTemplate = $mainframe->getTemplate();
		//$menu           = $mainframe->getMenu();
		//$active         = $mainframe->getMenu()->getActive();

		//var_dump($active);

		// Check se il plugin è attivato
		if (JPluginHelper::isEnabled('content', $this->plg_name) == false) return;

		// Salvare se il formato della pagina non è quello che vogliamo
		$allowedFormats = array('', 'html', 'feed', 'json');
		if (!in_array(JRequest::getCmd('format'), $allowedFormats)) return;

		// Controllo semplice delle prestazioni per determinare se il plugin dovrebbe elaborare ulteriormente
		if (JString::strpos($row->text, $this->plg_tag) === false) return;

		// Start Plugin
		$regex_one		= '/({featuredContent\s*)(.*?)(})/si';
		$regex_all		= '/{featuredContent\s*.*?}/si';
		// $regex_one		= '/({'.$plg_tag.'\s*)(.*?)(})/si';
		// $regex_all		= '/{'.$plg_tag.'\s*.*?}/si';
		//$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$row->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		//var_dump($matches, $count_matches);

		for($i=0;$i<$count_matches;$i++){
			$tag	= $matches[0][$i][0];
			preg_match($regex_one,$tag,$phocagallery_parts);
			$parts = explode("|", $phocagallery_parts[2]);
			//var_dump($parts);
			foreach($parts as $value){
				$values = explode("=", $value, 2);
				//var_dump($values);
				//es. $values[0] -> catid
				//es. $values[1] -> 1
				/*************************
								CONTENUTO
				*************************/
				if($values[0] == 'content_id') {
					$param[$i]['content_id'] = $values[1];
				}
				/*************************
								COLORE
				*************************/
				if($values[0] == 'color') {
					$param[$i]['color'] = $values[1];
				}

				/*************************
								 IMMAGINI
				*************************/
				// if($values[0] == 'image') {
				// 	$imageId = explode(",", $values[1]);
				// 	$param[$i]['image'] = $imageId;
				// 	foreach ($param[$i]['image'] as $key => $img) {
				// 		$param[$i]['image'][$key] = 'id = '.$img;
				// 	}
				// 	$param[$i]['image'] = "(".implode(' OR ', $param[$i]['image']).")";
				// }
				/*************************
								 TEMPLATE
				*************************/
				// if($values[0] == 'tmpl') {
				// 	$param[$i]['tmpl'] = $values[1];
				// }
				/*************************
				LIMITE IMMAGINI DA ESTRARRE
				*************************/
				// if($values[0] == 'limit' AND $values[1] != '') {
				// 	$param[$i]['limit'] = (int)$values[1];
				// }
				/*************************
					   HEIGHT CONTAINER
				*************************/
				// if($values[0] == 'hSlider' AND $values[1] != '') {
				// 	$param[$i]['hSlider'] = $values[1].'px';
				// }
				/*************************
					   IMMAGINI PER RIGA
				*************************/
				// if($values[0] == 'col' AND $values[1] != '') {
				// 	$param[$i]['col'] = $values[1];
				// }

				/*************************
				MOSTRARE IL LINK ALLA CATEGORIA
				*************************/
				// if($values[0] == 'catLink' AND $values[1] != '') {
				// 	$param[$i]['catLink'] = $values[1];
				// }

				/*************************
							 WIDTH THUMB
				*************************/
				// if($values[0] == 'wThumb' AND $values[1] != '') {
				// 	$param[$i]['wThumb'] = $values[1].'px';
				// }
				/*************************
							 HEIGHT THUMB
				*************************/
				// if($values[0] == 'hThumb' AND $values[1] != '') {
				// 	$param[$i]['hThumb'] = $values[1].'px';
				// }
				/*************************
									 TAG
				*************************/
				$param[$i]['tag'] = $matches[0][$i][0];
			}

			// Create a new query object.
			$query = $db->getQuery(true);
			// set query
			$query->select($db->quoteName(array('id', 'title', 'catid', 'language')));
			$query->from($db->quoteName('#__content'));
			$query->where($db->quoteName('id') . ' = '. $db->quote($param[$i]['content_id']));
			// if(isset($param[$i]['image']))
			// 	$query->where($param[$i]['image']);
			$query->order('ordering ASC');
			// if($param[$i]['limit'])
			// 	$query->setLimit($param[$i]['limit']);
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			if(empty($results))
				return;

			//var_dump($results);

				//JRoute::_("index.php?Itemid=$id")

			// paramentri della categoria per il link alla fine
			// if($param[$i]['catLink']){
			// 	$query = $db->getQuery(true);
			// 	$query->select($db->quoteName(array('id', 'alias')));
			// 	$query->from($db->quoteName('#__phocagallery_categories'));
			// 	$query->where($db->quoteName('id') . ' = '. $db->quote($param[$i]['catid']));
			// 	$db->setQuery($query);
			// 	$catResult = $db->loadObjectList();
			// }

			/*************************
						QUAERY RESULT
			*************************/
			$param[$i]['result'] = $results;

			//var_dump($param[$i]);

			/*************************
			TEST DEI PARAMETRI OPZIONALI
			*************************/
			if(!isset($param[$i]['tmpl'])) $param[$i]['tmpl'] = 'default';
			// if(!isset($param[$i]['hSlider'])) $param[$i]['hSlider'] = '400px';
			// if(!isset($param[$i]['col'])) $param[$i]['col'] = '4';
			// if(!isset($param[$i]['wThumb'])) $param[$i]['wThumb'] = '100px';
			// if(!isset($param[$i]['hThumb'])) $param[$i]['hThumb'] = '80px';
		}

		// Number of plugins
		//$count = count($param[0]['result']);

		// Il plugin funziona solo se ci sono istanze del plugin nel testo
		//if (!$count) return;

		//********************* PLUGIN PARAMS ********************************
		// Get plugin info
		/*$plugin = JPluginHelper::getPlugin('content', $this->plg_name);

		// Control external parameters and set variable for controlling plugin layout within modules
		if (!$params) $params = class_exists('JParameter') ? new JParameter(null) : new JRegistry(null);
		$parsedInModule = $params->get('parsedInModule');

		$pluginParams = class_exists('JParameter') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

		$galleries_rootfolder = ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder', $defaultImagePath);
		$popup_engine = 'jquery_fancybox';
		$jQueryHandling = $pluginParams->get('jQueryHandling', '1.8.3');
		$thb_template = 'Classic';
		$thb_width = (!is_null($params->get('thb_width', null))) ? $params->get('thb_width') : $pluginParams->get('thb_width', 200);
		$thb_height = (!is_null($params->get('thb_height', null))) ? $params->get('thb_height') : $pluginParams->get('thb_height', 160);
		$smartResize = 1;
		$jpg_quality = $pluginParams->get('jpg_quality', 80);
		$showcaptions = 0;
		$cache_expire_time = $pluginParams->get('cache_expire_time', 1440) * 60; // Cache expiration time in minutes
*/

		// ----------------------------------- Prepare the output -----------------------------------

		for($k=0;$k<$count_matches;$k++){
			// Fetch the template
			$item        = $param[$k];
			$PlgTmplName = $item['tmpl'];

			$galleryId = substr(md5($k.$item['tag']), 1, 5);

			// recupero del template
			ob_start();
			$templatePath = __DIR__.DS.'tmpl'.DS.$PlgTmplName.'/default.php';
			include ($templatePath);
			$getTemplate = ob_get_contents();
			ob_end_clean();

			// Output
			$plg_html = $getTemplate;
			// Do the replace
			$row->text = str_replace($item['tag'], $plg_html, $row->text);
		}

	} // END FUNCTION

} // END CLASS
