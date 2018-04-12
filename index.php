<?php 
define("COMPS_PATH", "/inc/compavail/");
$includes = array(
                "css" => array("computers.css"),
                "js" => array("computers.js")
);

switch ($_GET['view']){
  case 'TV':
    $includes["js"][] = 'tv.js';
    break;
  case 'helpdesk':
    $includes["js"][] = 'helpdesk.js';
    $includes["css"][] = 'helpdesk.css';
    break;
  case 'ACS':
    $includes["js"][] = 'acs.js';
    $includes["css"][] = 'acs.css';
    $includes["css"][] = 'helpdesk.css';
    break;
  default:
    $includes["js"][] = 'updatemap.js';

}

_include($includes,'css'); 
require_once('Computers.php');
_include($includes,'js'); 

function _include($a,$what){
    print '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>';
  foreach ($a[$what] as $inc){
    switch ($what){
      case 'js':
        print '<script type="text/javascript" src="/inc/compavail/'.$inc.'"></script>';
        break;
      case 'css':
        print '<link rel="stylesheet" type="text/css" href="/inc/compavail/'.$inc.'" />';
        break;
    }
  }
}

?>
