<?php

include "mainLogic.php";

class RequestHandler {
    private $method;
    private $request_method;

    public function __construct() {
        session_start();
        $this->request_method = $_SERVER["REQUEST_METHOD"];

        switch ($this->request_method) {
            case "POST":
                isset($_POST["method"]) ? $this->method = htmlspecialchars($_POST["method"]) : $this->method = null;
                break;
            case "GET":
                isset($_GET["method"]) ? $this->method = htmlspecialchars($_GET["method"]) : $this->method = null;
                break;
            default:
                http_response_code(405);
                die("Method not supported yet!");
        }
    }

    public function process() {    
        try {
            $this->response(200, MainLogic::handleRequest($this->method));
        } catch (Exception $exception) {
            $this->response(400, "Error - " . $exception->getMessage());
        }
    }

    private function response(int $httpStatus, $data) {
        header('Content-Type: application/json');
        http_response_code($httpStatus);
        echo(json_encode($data));
    }
}
