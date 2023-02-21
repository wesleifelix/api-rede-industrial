<?php

require_once "config.php";
require_once "model.php";



$bae = new User();

$bae->nome = "Weslei Felix";
$bae->email = "wesleifelix". rand(0,100)."@gmail.com";
$bae->senha = "123456";

function CallAPI($method = "GET", $url = "listAll", $data = false, $params = null)
{

    $con = new Config();
    switch($method){
        case "GET":
            if($url == "listAll"){
                http_response_code(200);
               
                $list = (array)$con->List();
                echo json_encode( array_values($list));
                exit();
            }
            else if($url == "list" ){

                if($data ){
                    $return = ($con->findObjectByID($params['id']));
                    
                    if(!$return){
                      $con->NoFound();
                      exit;
                    }
                    echo json_encode( $return);
                }
                else{
                    $con->BadRequest('Id não informada');
                }
            }
            break;
        
            case "POST":
                if($url == "create"){
                    $json = file_get_contents('php://input');
                    $user = ValidUser($json);
                    if($user)
                        $con->Insert($user);
                }
                break;
            
            case "PUT":
                if($url == "update"){
                    $json = file_get_contents('php://input');
                    $user = ValidUser($json);

                    if($user['id'] == $params['id'])
                        $con->Update($user, 'id', $params['id']);
                }
                break;
            
            case "DELETE":

                if($url == "delete"){
                   
                    $con->Delete('id',$params['id']);
                }
                break;

            default:
                $con->NoFound();
                break;
    }
}

function Validuser($data){
    $data = json_decode($data,false);
    $user = new User();
    $errors = 0;
    $error_mesage = null;
    
    $user->id = $data->id;

    if(empty($data->nome)){
        $errors++;
        $error_mesage .= 'O nome deve ser informado';
    }else $user->nome = $data->nome;


    if(empty($data->email)){
        $errors++;
        $error_mesage .= 'O email deve ser informado';
    }else $user->email = $data->email;


    if(empty($data->senha)){
        $errors++;
        $error_mesage .= 'A senha deve ser informada';
    }else $user->senha = $data->senha;

    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        $errors++;
        $error_mesage .= 'Informe um e-mail valido';
      }

    if($errors > 0){

        echo json_encode($error_mesage);
        return false;
    }
      
   return (array)$user;
    
}

function ValidRoutes(){
    $routes = [
        '',
        'list',
        'listAll',
        'create',
        'update',
        'delete'
    ];
    $url = $_SERVER['REQUEST_URI'];

    $valid_url = str_replace('/api/','',$url);
    $valid_url = explode('?', $valid_url)[0];
    $method = $_SERVER['REQUEST_METHOD'];
    $params['id'] = isset($_GET['id']) ? ( $_GET['id']) : null;
   
    if(in_array( $valid_url, $routes)){
        CallAPI($method, $valid_url, ($params['id']) ? true  : false, $params);
        exit;
    }
   
}

ValidRoutes();
/*
$con->Insert($bae);

$con->ListAll();

*/