<?php 
    namespace Models;
    class campers{
        protected static $conn;
        protected static $columnsTbl=['idCamper','nombreCamper','apellidoCamper','fechaNac','idReg'];
        private $idCamper;
        private $nombreCamper;
        private $idReg;
        public function __construct($args=[]){
            $this->idCamper = $args['idCamper'] ?? '';
            $this->nombreCamper = $args['nombreCamper'] ?? '';
            $this->apellidoCamper = $args['apellidoCamper'] ?? '';
            $this->fechaNac = $args['fechaNac'] ?? '';
            $this->idReg = $args['idReg'] ?? '';            
        }
        public function saveData($data){
            $delimiter = ":";
            $dataBd = $this->sanitizarAttributos();
            $valCols = $delimiter . join(',:',array_keys($data));
            $cols = join(',',array_keys($data));
            $sql = "INSERT INTO campers ($cols) VALUES ($valCols)";
            $stmt= self::$conn->prepare($sql);
            try {
                $stmt->execute($data);
                $response=[[
                    'idCamper' => self::$conn->lastInsertId(),
                    'nombreCamper' => $data['nombreCamper'],
                    'apellidoCamper' => $data['apellidoCamper'],
                    'fechaNac' => $data['fechaNac'],
                    'idReg' => $data['idReg']
                ]];
            }catch(\PDOException $e) {
                return $sql . "<br>" . $e->getMessage();
            }
            return json_encode($response);
        }       
        public function loadAllData(){
            $sql = "SELECT idCamper,nombreCamper,idReg FROM campers";
            $stmt= self::$conn->prepare($sql);
            $stmt->execute();
            $countries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $countries;
        }
        public function deleteData($id){
            $sql = "DELETE FROM campers where idCamper = :id";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        public function updateData($data){
            $sql = "UPDATE campers SET nombreCamper = :nombreCamper, idReg = :idReg where idCamper = :idCamper";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':nombreCamper', $data['nombreCamper']);
            $stmt->bindParam(':idReg', $data['idReg']);
            $stmt->bindParam(':idCamper', $data['idCamper']);
            $stmt->execute();
        }
        public static function setConn($connBd){
            self::$conn = $connBd;
        }
        public function atributos(){
            $atributos = [];
            foreach (self::$columnsTbl as $columna){
                if($columna === 'idCamper') continue;
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