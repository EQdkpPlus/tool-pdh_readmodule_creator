<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date: 2014-01-27 19:11:52 +0100 (Mo, 27 Jan 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13960 $
 * 
 * $Id: index.php 13960 2014-01-27 18:11:52Z godmod $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class pdh_readmodule_generator extends gen_class {
	public static $shortcuts = array('db');
	
	public function __construct() {
		//Options
		$strTablename = "__clanwars_fightus"; //Insert Tablename here
		$strModuleName = "clanwars_fightus";
		$strIDName	= "intClanID";
		
		
		//Do not change after here - exept you know what you are doing ;)
		$arrTableInformation = $this->db->listFields($strTablename);
		$out = "<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			\$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
* -----------------------------------------------------------------------
* @author		\$Author: wallenium $
* @copyright	2006-2014 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.eu
* @package		eqdkpplus
* @version		\$Rev: 12937 $
*
* \$Id: pdh_r_articles.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}
				
if ( !class_exists( \"pdh_r_".$strModuleName."\" ) ) {
	class pdh_r_".$strModuleName." extends pdh_r_generic{
		public static function __shortcuts() {
		\$shortcuts = array();
		return array_merge(parent::\$shortcuts, \$shortcuts);
	}				
	
	public \$default_lang = 'english';
	public \$".$strModuleName." = null;

	public \$hooks = array(
		'".$strModuleName."_update',
	);		
			
	public \$presets = array(
";
		
	foreach($arrTableInformation as $val) {
		if ($val['name'] == "PRIMARY") break;
		
		$out .= "		'".$strModuleName."_".$val['name']."' => array('".$val['name']."', array('%".$strIDName."%'), array()),\n";
	}	
	
				
				
	$out .="	);
				
	public function reset(){
			\$this->pdc->del('pdh_".$strModuleName."_table');
			
			\$this->".$strModuleName." = NULL;
	}
					
	public function init(){
			\$this->".$strModuleName."	= \$this->pdc->get('pdh_".$strModuleName."_table');				
					
			if(\$this->".$strModuleName." !== NULL){
				return true;
			}		

			\$objQuery = \$this->db->query('SELECT * FROM ".$strTablename."');
			if(\$objQuery){
				while(\$drow = \$objQuery->fetchAssoc()){
					//TODO: Check if id Column is available
					\$this->".$strModuleName."[(int)\$drow['id']] = array(
";
	foreach ($arrTableInformation as $val){
		if ($val['name'] == "PRIMARY") break;
		
		$cast = "";
		if (strpos($val['type'], 'int') !== false){
			$cast = "(int)";
		} elseif($val['type'] == 'double' || $val['type'] == 'float'){
			$cast = "(float)";
		}
		$out .= "						'".$val['name']."'			=> ".$cast."\$drow['".$val['name']."'],\n";
	}					
	$strID = "\$".$strIDName;	
	
	$out .=					"
					);
				}
				
				\$this->pdc->put('pdh_".$strModuleName."_table', \$this->".$strModuleName.", null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if (\$this->".$strModuleName." === null) return array();
			return array_keys(\$this->".$strModuleName.");
		}
		
		/**
		 * Get all data of Element with \$strID
		 * @return multitype: Array with all data
		 */				
		public function get_data(".$strID."){
			if (isset(\$this->".$strModuleName."[".$strID."])){
				return \$this->".$strModuleName."[".$strID."];
			}
			return false;
		}
				
";
	
	
	foreach ($arrTableInformation as $val){
		if ($val['name'] == "PRIMARY") break;
		
		
		$out .="		/**
		 * Returns ".$val['name']." for ".$strID."				
		 * @param integer ".$strID."
		 * @return multitype ".$val['name']."
		 */
		 public function get_".$val['name']."(".$strID."){
			if (isset(\$this->".$strModuleName."[".$strID."])){
				return \$this->".$strModuleName."[".$strID."]['".$val['name']."'];
			}
			return false;
		}\n\n";
	}
	
	$out .= "	}//end class
}//end if
?>";
	
	file_put_contents("pdh_r_".$strModuleName.".class.php", $out);
	echo "Build of pdh_r_".$strModuleName.".class.php finished";	
		
		
	}
	
}
registry::register('pdh_readmodule_generator');
?>