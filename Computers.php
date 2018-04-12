<?php
require_once('MysqlPDO.php');

/**
 * Defined constance for prepared SQL statements for PHP PDO (http://php.net/manual/en/book.pdo.php)
 */
define ("NAV",  "SELECT parent.id as 'id',"
    ." parent.parent_id as parent_id,"
    ." parent.label as 'label',"
    ." parent.description as 'description',"
    ." SUM(CASE WHEN comps.status=0 AND comps.computer_type LIKE 'p%' THEN 1 ELSE 0 END) as 'pc_a',"
    ." SUM(CASE WHEN comps.computer_type LIKE 'p%' THEN 1 ELSE 0 END) as 'pc_t',"
    ." SUM(CASE WHEN comps.status=0 AND comps.computer_type LIKE 'm%' THEN 1 ELSE 0 END) as 'mac_a',"
    ." SUM(CASE WHEN comps.computer_type LIKE 'm%' THEN 1 ELSE 0 END) as 'mac_t',"
    ." (SELECT count(1) FROM location c WHERE c.parent_id = parent.id) as 'num_children',"
    ." (CASE WHEN parent.map = '' THEN 0 ELSE 1 END) as 'map'"
    ." FROM compstatus as comps"
    ." INNER JOIN location as loc ON comps.loc_id = loc.id"
    ." INNER JOIN location as parent ON loc.parent_id = parent.id || comps.loc_id = parent.id"
    ." WHERE parent.parent_id=:loc_id AND active=1"
    ." GROUP BY label"
    ." ORDER BY parent.weight");

define ("NAV_UPDATE", "SELECT loc.id as 'id',"
    ." SUM(CASE WHEN comps.status=0 AND comps.computer_type LIKE 'p%' THEN 1 ELSE 0 END) as 'pc_a',"
    ." SUM(CASE WHEN comps.computer_type LIKE 'p%' THEN 1 ELSE 0 END) as 'pc_t',"
    ." SUM(CASE WHEN comps.status=0 AND comps.computer_type LIKE 'm%' THEN 1 ELSE 0 END) as 'mac_a',"
    ." SUM(CASE WHEN comps.computer_type LIKE 'm%' THEN 1 ELSE 0 END) as 'mac_t'"
    ." FROM compstatus as comps"
    ." INNER JOIN location as loc ON comps.loc_id = loc.id"
    ." INNER JOIN location as parent ON loc.parent_id = parent.id || comps.loc_id = parent.id"
    ." WHERE parent.parent_id=:loc_id AND active=1"
    ." GROUP BY id"
    ." ORDER BY parent.weight");

define ("MAP", "SELECT comp.computer_name, comp.status, comp.left_pos, comp.top_pos, comp.computer_type"
    ." FROM compstatus as comp"
    ." WHERE loc_id=:loc_id AND active=1"
		." ORDER BY computer_name");

define ("MAP_UPDATE", "SELECT comp.computer_name, comp.status"
    ." FROM compstatus as comp"
    ." WHERE loc_id=:loc_id AND active=1");

define ("MAP_IMG", "SELECT map FROM location WHERE id=:loc_id");

define ("LABEL", "SELECT label FROM location WHERE id=:parent_id");

/**
 * Defined constant for queries that are dynamically generated
 * See query_db()
 */
define ("DYNAMIC_QUERY", TRUE);

// Load new Computers class instance when Computers.php is called/included
$comp = new Computers(); 

class Computers{
  // @String - Name of the defined query - defaults to 'nav'
  protected $query = 'nav';
  /**
   * @Array - Location array that holds the parent/child tree for the current page
   * - Tree path is listed parent->child from top down in array
   * - Array members are the ID of the location in the 'location' table in the DB
   * EX: array for location of Floor 3 of Gorgas
   * $loc = array(
   *         1, //--> ID of Gorgas location
   *         8 //--> ID of Floor 3 location
   *      );
   */
  protected $loc = array(0);
  // @Array - Holdes variables to be passed to the template files

  protected $view;
  
  protected $tpl_vars;
  
  function __construct(){
    // Populate $query and $loc Class vars
    if (!empty($_GET['query'])){
      $this->query = $_GET['query'];
    }
    if (!empty($_GET['view'])){
      $this->view = $_GET['view'];
    }    
    if (!empty($_GET['loc'])){
      // GET variable for loc is passed as a comma separated parent->child path
      // Explode string into an array
      $this->loc = explode(',',$_GET['loc']);
    }
    
    // Detect if Ajax request
    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strcasecmp('XMLHttpRequest', $_SERVER['HTTP_X_REQUESTED_WITH']) === 0)) || isset($_GET['json'])) {
      // Make DB query, encode result array to json, die - printing json and exiting script
      die(json_encode($this->query_db($this->query, array(':loc_id' => end($this->loc)))));
    }
    else{
      $this->render();
    }
  }
  
  /**
   * preprocesses()
   * 
   * - Query DB
   * - populate template variables
   */
  protected function preprocesses(){
    $params = array(':loc_id' => end($this->loc));
    $this->tpl_vars['comps'] = $this->query_db($this->query, $params);
    $this->tpl_vars['loc'] = implode(",", $this->loc);
    $this->tpl_vars['title'] = $this->get_title($this->loc);
    $this->tpl_vars['back_loc'] = $this->get_back_loc($this->loc);
    $this->tpl_vars['view'] = $this->view;
    
    if ($this->query == 'map'){
      $this->tpl_vars['map'] = $this->get_map_info($this->loc);
      $this->set_comps_map_info($this->tpl_vars['comps']);
    }
    else{
      $this->set_nav_link_info($this->tpl_vars['comps']);
    }
  }
  
  /**
   * render()
   * - Print template
   * - Print PHP variables that need to be shared with Javascript
   * -- see js_context()
   */
  protected function render(){
    $this->preprocesses();
    print $this->load_template($this->query, $this->tpl_vars);
    print $this->js_context();
  }
   
  /**
   * load_template()
   * 
   * @param String $tpl_name
   *     -  Name of template, without extension
   *     - Supported template extension: '.tpl.php'
   *     
   * @param Array $vars
   *     - Variables to pass to the template
   *     - Uses PHP function extract() (http://www.php.net/manual/en/function.extract.php)
   *     
   * Method lifted from Drupal source-code (http://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_render_template/6)
   */
  protected function load_template($tpl_name, $vars){
    extract($vars, EXTR_SKIP); // Extract the variables to a local namespace
    
    $path = '/srv/web/www/inc/compavail/templates/'.$tpl_name.'.tpl.php';
    
    ob_start(); // Start output buffering
    include $path; // Include the template file
    $contents = ob_get_contents(); // Get the contents of the buffer
    ob_end_clean(); // End buffering and discard
    return $contents; // Return the contents
  }
  
  /**
   * query_db()
   * 
   * @param String $query
   *   - Can be a String representation of the name of a defined constant
   *   - Can be a MySQL query string from a custom fuction
   *   - Must be prepared statement with named palceholders (http://www.php.net/manual/en/pdo.prepared-statements.php - See Example #1)
   *   -- Requires $dynamic_query set to TRUE
   *   
   * @param Array $params
   *   - Associative array of the prepared statment's (i.e., query) place holders and values 
   *   -- EX: array( ':placeholder_name' => value )
   *   
   * @param Boolean $dynamic_query
   *   - Determines if the $query parameter is referencing the name of a defined constant or a query string generated from a function
   *   
   * @return Array $result:
   *   - Associative Array of all rows returned with query
   *   -- array( 
   *         array('db_column_name' => 'row1_id'),
   *         array('db_column_name' => 'row2_id')
   *       );
   */
  protected function query_db($query, $params, $dynamic_query = FALSE){
    $result = array();
    $dbh = MysqlPDO::connect();
    $q = $dynamic_query ? $query : constant(strtoupper($query));
   
    $stmt = $dbh->prepare($q);
    foreach ($params as $place_holder => &$param){
      $stmt->bindParam($place_holder, $param);
    }
    
    if ($stmt->execute()){
      /*while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result[] = $row;
      }*/
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $result;
  }
  
  /**
   * set_nav_link_info()
   * 
   * @param Array $comps
   * 
   * Updates array with linking info to print in the template to help make Ajax calls
   */
  protected function set_nav_link_info(&$comps){
    foreach ($comps as &$comp){
      if ($comp['map']){
        $comp['linked'] = TRUE;
        $comp['q'] = 'map';
      }
      else if ($comp['num_children'] > 0){
        $comp['linked'] = TRUE;
        $comp['q'] = 'nav';
      }
      else{
        $comp['linked'] = FALSE;
        $comp['q'] = 'nav';
      }
    }
  }
  
  /**
   * get_title()
   * 
   * @param Array $loc
   * @return boolean OR String
   * 
   * Generated the title for the current location, including parent names for context (i.e., "Gorgas, Floor3)
   */
  protected function get_title($loc){
    $title = FALSE;
    $current = end($loc);
    reset($loc);
    if ($current != 0){
      $query = 'SELECT';
      $params = array();
      $count = count($loc);
      $i=0;
      
      while (($i+1) < $count){
        $query .= ' (SELECT label FROM location WHERE id = :param_'.$i.') label'.$i.',';
        $params[':param_'.$i] = $loc[$i];
        $i++;
      }
      
      $query .=' label label'.$i.' FROM location WHERE id = :param_'.$i;
      $params[':param_'.$i] = $loc[$i];
      $result = $this->query_db($query, $params, DYNAMIC_QUERY);
      $title = implode(", ", $result[0]);
    }
    return $title;
  }
  
  /**
   * get_back_loc()
   * 
   * @param Array $loc
   * 
   * @return String
   *   - The location string for the "Back" link
   */
  protected function get_back_loc($loc){
    array_pop($loc);
    return implode(",",$loc);
  }
  
  /**
   * get_map_info()
   * @param Array $loc
   * @return Array
   * 
   * Gathers information the map being called
   *   - Path to image
   *   - Image width and height
   *   - CSS Styles to apply to the DIV that holds the map image
   */
  protected function get_map_info($loc){
    $r = $this->query_db("MAP_IMG", array(':loc_id' => end($loc)));
    $map = $r[0]['map'];
    
    if (substr($map, 0, 1) != '/'){
      $map = '/'.$map;
    }
    $map_size = getimagesize('/srv/web/www'.$map);
    $info = array(
        'image' => $map,
        'w' => $map_size[0],
        'h' => $map_size[1],
        'style' => 'background: url('.$map.') no-repeat 0 0; width: '.$map_size[0].'px; height: '.$map_size[1].'px;'
    );
    return $info;
  }
  
  /**
   * set_comps_map_info()
   * @param Array $comps
   * 
   * Updates $comps Array with CSS class and style info for each computer on the map
   */
  protected function set_comps_map_info(&$comps){
    foreach ($comps as &$comp){
 #     $classes = $comp['computer_type'].' '.($comp['status'] == 0 ? 'open' : 'closed');
      $classes = $comp['computer_type'].' ';
      switch ($comp['status']) {
        case 0:
          $classes.='open';
          break;
        case 1:
          $classes.='closed';
          break;
        case 2:
          $classes.='help';
          break;
        case 3:
          $classes.='OoO';
          break;
      }
      $styles = 'left: '.($comp['left_pos']+2).'px; top: '.($comp['top_pos']+2).'px;';
      
      $comp['classes'] = $classes;
      $comp['styles'] = $styles;
    }
  }
  
  /**
   * js_context()
   * 
   * Generates JSON Array to share PHP variables with Javascript to help Ajax calls
   */
  protected function js_context(){
    $js_vars = array('q' => $this->query, 'loc' => end($this->loc) , 'view' => $this->view);
    return '<script>Computers='.json_encode($js_vars).';</script>';
  }
  
}

//#### HELP/DEBUG FUNCTIONS --- will be deleted ####//
function print_ar($data, $label=NULL){
  print '<div class="status messages" style="text-align: left; margin: 10px;">';
  if (isset($label)) print '<h2>'.$label.'</h2>';
  print '<pre>';
  
  if (is_array($data)){
    print_r($data);
  }
  else if (is_object($data)){
    var_dump($data);
  }
  else{
    print $data;
  }
  print '</pre></div>';
}

function var_name ($iVar, &$aDefinedVars)
{
  foreach ($aDefinedVars as $k=>$v)
    $aDefinedVars_0[$k] = $v;

  $iVarSave = $iVar;
  $iVar     =!$iVar;

  $aDiffKeys = array_keys (array_diff_assoc ($aDefinedVars_0, $aDefinedVars));
  $iVar      = $iVarSave;

  return $aDiffKeys[0];
}