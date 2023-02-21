<?php


    class User{
        public $id;
        public $nome;
        public $email;
        public $senha;


        public function Serializer($data){
            $models = new ArrayObject();
            foreach($data as $row) {
    
                $cs = new User();
    
                $cs->id = $row['id'];
                $cs->nome = $row['nome'];
                $cs->email = $row['email'];
                $cs->senha = $row['senha'];
                $models->append($cs);
    
            }
            return (array)$models;
        }

    }

