<?php

/*
 * demo of REST producer/consumer
 * Jim Baldwin
 * 2015-02-25
 * todo:
 *  map autoloads to PHPLIB/* directories
 *
 */

$debug = false;
require_once('../PHPLIB/services/config.php');
function __autoload($classname) {
  require strtolower($classname) . '.php';
}

// initialise the request object and store the requested URL
$debug = new Debug();
$request = new Request();

$request->url_elements = array();

// get routing data
if(isset($_SERVER['REQUEST_URI'])) {
  if($_SERVER['REQUEST_URI'] == '/'){
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: /books"); 
    exit;
  }
  $request->url_elements = explode('/', $_SERVER['REQUEST_URI']);
}

// get data based on request method
$request->verb = $_SERVER['REQUEST_METHOD'];
switch($request->verb) {
  case 'GET':
    $request->parameters = $_GET;
    break;
  case 'POST':
  case 'PUT':
    $request->parameters = json_decode(file_get_contents('php://input'), 1);
    break;
  case 'DELETE':
  default:
    // we won't set any parameters in these cases
    $request->parameters = array();
}

// route the request
if($request->url_elements) {
  $controller_name = ucfirst($request->url_elements[1]) . 'Controller';
  if(class_exists($controller_name)) {
    $controller = new $controller_name($db);
    $action_name = ucfirst($request->verb) . "Action";
    $response = $controller->$action_name($request);
  } else {
    header('HTTP/1.0 400 Bad Request');
    $response = "Unknown Request for " . $request->url_elements[1];
  }
} else {
  header('HTTP/1.0 400 Bad Request');
  $response = "Unknown Request";
}

$oResponse = json_decode($response,false);


  echo "
    <style type=\"text/css\">
      body {font-family: arial, sans-serif;}
      h1{
        font-size: 20px;
      }
      th{
        text-align:left;
        border-bottom: 1px solid black;
      }
      td,th{
        padding: 0 5px;
        border-right: 1px solid black;
      }
      .rtcol{
        border-right: none !important;
      }
    </style>
    
  <h1>REST demo</h1>
    <p><strong>URL: http://services.jimbaldwin.net/books</strong> -- displays all books.<br>
    <strong>URL: http://services.jimbaldwin.net/books/[id]</strong> -- displays a single book.</p>
    ";
    
  if(!empty($oResponse)){
    echo "
    <table cellspacing=\"0\" cellpadding=\"0\">
    <tr><th>ID</th><th>Title</th><th>Date</th><th class='rtcol'>Pages</th></tr>\n
  ";
  foreach($oResponse as $row){
    $display_date = date('m/d/Y',$row->date);
    echo "<tr><td>$row->id</td><td>$row->title</td><td>$display_date</td><td class='rtcol'>$row->pages</td></tr>\n";
  }
  echo "</table>\n";
} else{
  echo "<p>No book matching that search.</p>\n";
}

echo "<br><br><div>Server Response:</div><div>$debug->pre($response)</div>\n";



