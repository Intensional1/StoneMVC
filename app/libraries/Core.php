<?php
  /*
   * App Core Class
   * Creates URL & loads core controller
   * URL FORMAT - /controller/method/params
   */
  class Core {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
      //print_r($this->getUrl());

      $url = $this->getUrl();
      // Note as of PHP 8 there is an error with the following code:
      // Notice: Trying to access array offset on value of type null in C:\laragon\www\testmvc\app\libraries\Core.php on line 18
      // First tried adding additional qualifier to if statement to make sure url is not null(such as on homepage), however its too late so wrapped the whole function with a check

      // REQUIRED BUG FIX
      if($url) {
        // Look in controllers for first value
        if(file_exists('../app/controllers/' . ucwords($url[0]). '.php')) {
          // If exists, set as controller
          $this->currentController = ucwords($url[0]);
          // Unset 0 Index
          unset($url[0]);
        }
      }
      // Require the controller
      require_once '../app/controllers/'. $this->currentController . '.php';

      // Instantiate controller class
      $this->currentController = new $this->currentController;

      // Check for second part of url
      if(isset($url[1])){
        // Check to see if method exists in controller
        if(method_exists($this->currentController, $url[1])){
          $this->currentMethod = $url[1];
          // Unset 1 index
          unset($url[1]);
        }
      }

      // Get params
      $this->params = $url ? array_values($url) : [];

      // Call a callback with array of params
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
      if(isset($_GET['url'])){
        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
      }
    }
  } 
  
  