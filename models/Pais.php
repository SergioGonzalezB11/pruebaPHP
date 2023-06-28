<?php
    namespace Models;
    class Country{
        protected static $conn;
        protected static $columnsTbl=['idPais','nombrePais'];
        private $idPais;
        private $nombrePais;
        public function __construct($args = []){
            $this->idPais = $args['idPais'] ?? ''; 
            $this->nombrePais = $args['nombrePais'] ?? '';
        }
        public function saveData($data){
            $delimiter = ":";
            $dataBd = $this->sanitizarAttributos();
            $valCols = $delimiter . join(',:',array_keys($data));
            $cols = join(',',array_keys($data));
            $sql = "INSERT INTO pais ($cols) VALUES ($valCols)";
            $stmt= self::$conn->prepare($sql);
            try {
                $stmt->execute($data);
                $response=[[
                    'idPais' => self::$conn->lastInsertId(),
                    'nombrePais' => $data['nombrePais']
                ]];
            }catch(\PDOException $e) {
                return $sql . "<br>" . $e->getMessage();
            }
            return json_encode($response);
        }       
        public function loadAllData(){
            $sql = "SELECT idPais,nombrePais FROM pais";
            $stmt= self::$conn->prepare($sql);
            $stmt->execute();
            $pais = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $pais;
        }
        public function deleteData($id){
            $sql = "DELETE FROM pais where idPais = :id";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        public function updateData($data){
            $sql = "UPDATE pais SET nombrePais = :nombrePais where idPais = :id";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':nombrePais', $data['nombrePais']);
            $stmt->bindParam(':id', $data['idPais']);
            $stmt->execute();
        }
        public static function setConn($connBd){
            self::$conn = $connBd;
        }
        public function atributos(){
            $atributos = [];
            foreach (self::$columnsTbl as $columna){
                if($columna === 'idPais') continue;
                $atributos [$columna]=$this->$columna;
            }
            return $atributos;
        }
        public function sanitizarAttributos(){
            $atributos = $this->atributos();
            $sanitizado = [];
            foreach($atributos as $key => $value){
                $sanitizado[$key] = self::$conn->quote($value);
            }
            return $sanitizado;
        }
    }

?>