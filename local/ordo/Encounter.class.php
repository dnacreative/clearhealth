<?php
/**
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.clearhealth
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/ORDataObject.class.php';
/**#@-*/

/**
 * Object Relational Persistence Mapping Class for table: encounter
 *
 * @package	com.uversainc.clearhealth
 */
class Encounter extends ORDataObject {

	/**#@+
	 * Fields of table: encounter mapped to class members
	 */
	var $id				= '';
	var $encounter_reason		= '';
	var $patient_id			= '';
	var $building_id		= '';
	var $date_of_treatment		= '';
	var $treating_person_id		= '';
	var $timestamp			= '';
	var $last_change_user_id	= '';
	var $status			= 'open';
	var $occurence_id		= '';
	var $_erCache = false;
	/**#@-*/


	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Encounter($db = null) {
		parent::ORDataObject($db);	
		$this->_table = 'encounter';
		$this->_sequence_name = 'sequences';
		$this->date_of_treatment = date("Y-m-d");	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of Encounter with this
	 */
	function setup($id = 0,$patient_id=0) {
		if ($patient_id > 0) {
			$this->set('patient_id',$patient_id);
		}
		if ($id > 0) {
			$this->set('id',$id);
			$this->populate();
		}
	}

	/**
	 * Populate the class from the db
	 */
	function populate() {
		parent::populate('encounter_id');
	}

	/**#@+
	 * Getters and Setters for Table: encounter
	 */

	
	/**
	 * Getter for Primary Key: encounter_id
	 */
	function get_encounter_id() {
		return $this->id;
	}

	/**
	 * Setter for Primary Key: encounter_id
	 */
	function set_encounter_id($id)  {
		$this->id = $id;
	}

	function set_date_of_treatment($date) {
		$this->date_of_treatment = $this->_mysqlDate($date);
	}

	/**#@-*/

	
	function encounterList($patient_id) {
		settype($patient_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "date_format(date_of_treatment,'%Y-%m-%d') date_of_treatment, encounter_reason, b.name building, concat_ws(' ',p.first_name,p.last_name) treating_person, status, encounter_id",
				'from' 	=> "$this->_table e left join buildings b on b.id = e.building_id left join person p on e.treating_person_id = p.person_id",
				'where' => " patient_id = $patient_id"
			),
			array('date_of_treatment' => 'Date of Treatment','encounter_reason' => 'Reason', 'building' => 'Building', 'treating_person' => 'Treated By', 'status' => 'Status'/*,'encounter_id' => "Encounter Id"*/));

		$ds->registerFilter('encounter_reason',array(&$this,'lookupEncounterReason'));
		//echo $ds->preview();
		return $ds;
	}

	/**#@+
	 * Enumeration getters
	 */
	function getEncounterReasonList() {
		$list = $this->_load_enum('encounter_reason',false);
		return array_flip($list);
	}
	/**#@-*/

	
	/**
	 * Cached lookup for encounter_reason
	 */
	function lookupEncounterReason($id) {
		if ($this->_erCache === false) {
			$this->_erCache = $this->getEncounterReasonList();
		}
		if (isset($this->_erCache[$id])) {
			return $this->_erCache[$id];
		}
	}

}
?>
