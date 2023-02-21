<?php

        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");
        define("_FILENAME",  "database.json");

        class Config{
            public $database;
            private $table = "users";

            public function __construct()
            {
                
                $host = '177.72.160.174';
                $database = 'livypradocom_teste_api';
                $user = 'livypradocom_tester';
                $pass = 'bQ#U[gt83Jj%';
          
                try
                {
                    $connect = new PDO('mysql:host='.$host.';dbname='.$database, $user, $pass);
                    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $this->database = $connect;
                    return $connect;
                }
                catch(PDOException $error)
                {
                    echo 'ERROR: ' . $error->getMessage();
                }
            }

            public function List(){
                
                $sql = 'SELECT * FROM '.$this->table.'';
                $data =  $this->database->query($sql);
                
                $models = $this->returnSerialzed($data);
        
                return $models;
            }
        
        
            public function Where($param){
                $data = $this->database->query('SELECT * FROM '.$this->table.' WHERE '.$param.'');
                $sereliazed =  self::returnSerialzed($data);
                return $sereliazed;
            }
        
            public function Insert($user){
                
               
                if($this->findObjectByEmail($user['email']))
                {
                    return $this->BadRequest("E-mail já cadastrado"); 
                }
                unset($user['id']);
                $fiels = "";
                foreach(array_keys($user) as $row){
                    $fiels .= $row.',';
                }
                $fiels = rtrim($fiels,',');
        
                $values = "";
                foreach(array_values($user) as $row){
                    $values .= "'$row',";
                }
                 $values = rtrim($values,',');
        
                try{
                    $sql = 'INSERT INTO '.$this->table.' ('.$fiels.') VALUES ('.$values.')';
        
        
                    // Prepare statement
                    $stmt =  $this->database->prepare($sql);
        
                    // execute the query
                    $stmt->execute();

                    header("HTTP/1.1 201 Created");
                    $param = "email = '".$user['email']."'";
                    $data = self::Where($param);
                    echo json_encode($data);
                   // return $this->findObjectByEmail($user->email);
                }
                catch(PDOException $e)
                {
                    return $this->BadRequest( $e->getMessage()); 
                    
                }
        
            }
        
            public function Update($data, $key, $keyvalue){
        
                $fiels = "";
        
                foreach(array_keys($data) as $row){
                    $fiels .= $row.' = "'. $data[$row].'",';
                }
                $fiels = rtrim($fiels,',');
        
                try{
                    $sql = 'UPDATE '.$this->table.' SET '.$fiels.' WHERE '.$key.' = '.$keyvalue;
                    $stmt =  $this->database->prepare($sql);
                    $stmt->execute();
                    header("HTTP/1.1 204 No Content");
                    return true;
                }
                catch(PDOException $e)
                {
                    return $this->BadRequest( $e->getMessage()); 
                }
        
            }
        
            public function Delete($key,$keyvalue){
        
                try{
                    $sql = 'DELETE FROM '.$this->table.' WHERE '.$key.' = '.$keyvalue;
                    $stmt =  $this->database->prepare($sql);

                    $stmt->execute();
                    header("HTTP/1.1 200");
                    return true;
                }
                catch(PDOException $e)
                {
                    return $this->BadRequest( $e->getMessage()); 
                }
        
            }
            
           

            function findObjectByEmail($email){
                
                $param = "email = '".$email."'";
                $data = self::Where($param);
               
                if(count($data) > 0)
                    return true;
            }

            function findObjectByID($id){
                
                $param = "id = '".$id."'";
                $data = self::Where($param);
               
                return ( (array)$data);
            }

            public function NoFound(){
                http_response_code(404);
               
                exit();
            }

            public function BadRequest($mesage){
                http_response_code(400);
                 echo json_encode(array("error" => $mesage));
                exit();
            }


            private function returnSerialzed($data){
                $usr = new User();
                $models = $usr->Serializer($data);
        
                return $models;
            }
        }
    