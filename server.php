<?php


class server 
{
    //DB info
    private $host = 'localhost';
    private $db_name ='ciljni_sistemi';
    private $username= 'root';
    private $password = 'root';
    private $conn;
//-------------------------------------------------

    //konstruktor izvede connect na bazo
    public function __construct()
    {
        $this->conn = $this->connect();
    }

    public function connect(){
        try {
        $this->conn = new PDO('mysql:host='. $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
        } catch (PDOException $e) {
        echo "Connection Error: " . $e->getMessage();
    }
        return $this->conn;
    }

    public function insertFailedId($id_array)
    {
        $ids= $id_array['ids'];
        $sistem= $id_array['system'];
        $query = 'INSERT INTO failed_attempts (ids,system) VALUES (:ids, :sistem)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('sistem', $sistem);

        if($stmt->execute()){
            
            return array('succes' =>'true');
        }
        return array('succes' =>'falseee');     
    }

    public function deleteFailedIds($id_array)
    {
        $ids= $id_array['ids'];
        $sistem= $id_array['system'];
        $query = 'DELETE FROM failed_attempts WHERE ids=:ids AND system=:sistem';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('sistem', $sistem);

        if($stmt->execute())
        {
        return 'zbrisano';
        }
        return 'ni zbrisano';
    }

    public function getFailedIds($sys)
    {
        $sistem= $sys['system'];
        $query = 'SELECT * from failed_attempts where system=:sistem';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('sistem', $sistem);
        $stmt->execute();


        // Preveri če so zapisi v failed_attempts
        if($stmt->rowCount() > 0){
            //array za rezultate
            $res_arr = array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
    
                  $item = array(
                    'ids' => $ids,
                    'system' => $system,
                );
    
                //Pushne v array za rezultate
                array_push($res_arr, $item);
            }
      return $res_arr;
    }
    //V primeru da ni rezultatov vrne null
    $res_arr=null;
    return $res_arr;
}

    public function getCustomerInfo($id_array)
    {
        $ids= $id_array['ids'];
        $query = 'SELECT * from crm where ids=:ids';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
    }

    public function updateCRM($id_array)
    {
        //Trenutni podatki v bazi
        $current_info = $this->getCustomerInfo($id_array);

        //Pridobi ustrezne podatke za update
        $ids = $id_array['ids'];
        $ime =  $id_array['ime'];
        $priimek= $id_array['priimek'];
        $naslov= $id_array['naslov'];
        $postna_stevilka= $id_array['postna_stevilka']; 
        $davcna_stevilka=$id_array['davcna_stevilka'];
        $tel_stevilka=$id_array['tel_stevilka']; 
        
        $query='UPDATE crm 
                SET 
                    ime=:ime,
                    priimek=:priimek,
                    naslov=:naslov,
                    postna_stevilka=:postna_stevilka,
                    davcna_stevilka=:davcna_stevilka,
                    tel_stevilka=:tel_stevilka
                   
                WHERE 
                    ids=:ids';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('ime', $ime);
        $stmt->bindParam('priimek', $priimek);
        $stmt->bindParam('naslov', $naslov);
        $stmt->bindParam('postna_stevilka', $postna_stevilka);
        $stmt->bindParam('davcna_stevilka', $davcna_stevilka);
        $stmt->bindParam('tel_stevilka', $tel_stevilka); 

        //če je update uspešen
        if($stmt->execute()){
            
            return array('succes' =>'true');
        }

        //Če je napaka izpiše error info
        return array('succes' =>'false');
    }

    public function updateBIL($id_array)
    {
        //Trenutni podatki v bazi
        $current_info = $this->getCustomerInfo($id_array);

        //Pridobi ustrezne podatke za update
        $ids = $id_array['ids'];
        $ime =  $id_array['ime'];
        $priimek= $id_array['priimek'] ;
        $naslov= $id_array['naslov'] ;
        $postna_st_naziv_poste= $id_array['postna_st_naziv_poste'] ;
        $davcna_stevilka= $id_array['davcna_stevilka'] ;
        
        $query='UPDATE bil 
        SET 
            ime=:ime, 
            priimek=:priimek, 
            naslov=:naslov, 
            postna_st_naziv_poste=:postna_st_naziv_poste,
            davcna_stevilka=:davcna_stevilka
        WHERE 
            ids=:ids';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('ime', $ime);
        $stmt->bindParam('priimek', $priimek);
        $stmt->bindParam('naslov', $naslov);
        $stmt->bindParam('postna_st_naziv_poste', $postna_st_naziv_poste);
        $stmt->bindParam('davcna_stevilka', $davcna_stevilka);
        

        //če je update uspešen
        if($stmt->execute()){
            
           // return "Podatki v sistemu BIL so bili uspešno posodobljeni <br>";
           return array(
               'success' => 'true',
               'info' => 'Podatki v sistemu BIL so bili uspešno posodobljeni <br>'
            );
        }

        //Če je napaka izpiše error info
        return array(
            'success' => 'false',
            'info' => 'Prišlo je do napake pri posodabljanju podatkov v sistemu BIL <br>'
         );
        
    }

    public function updateAUT($id_array)
    {
        //Trenutni podatki v bazi
        $current_info = $this->getCustomerInfo($id_array);

        //Pridobi ustrezne podatke za update
        $ids = $id_array['ids'];
        $ime_priimek = $id_array['ime_priimek'];

        $query='UPDATE aut 
                SET 
                    ime_priimek=:ime_priimek
                WHERE 
                    ids=:ids';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('ime_priimek', $ime_priimek);

        //če je update uspešen
        if($stmt->execute()){
            
            return array(
                'success' => 'true',
                'info' => 'Podatki v sistemu AUT so bili uspešno posodobljeni <br>'
             );
         }
 
         //Če je napaka izpiše error info
         return array(
             'success' => 'false',
             'info' => 'Prišlo je do napake pri posodabljanju podatkov v sistemu AUT <br>'
          );
        
    }

    public function updateERP($id_array)
    {
        //Trenutni podatki v bazi
        $current_info = $this->getCustomerInfo($id_array);

        //Pridobi ustrezne podatke za update
        $ids = $id_array['ids'];
        $priimek_in_ime = $id_array['priimek_in_ime'];
        $naslov=($id_array['naslov']!="" ? $id_array['naslov'] : $current_info['naslov']);
        $postna_stevilka=($id_array['postna_stevilka']!="" ? $id_array['postna_stevilka'] : $current_info['postna_stevilka']);
        $davcna_stevilka=($id_array['davcna_stevilka']!="" ? $id_array['davcna_stevilka'] : $current_info['davcna_stevilka']);
        
        $query='UPDATE erp 
                SET 
                    priimek_in_ime=:priimek_in_ime, 
                    naslov=:naslov, 
                    postna_stevilka=:postna_stevilka, 
                    davcna_stevilka=:davcna_stevilka
                WHERE 
                    ids=:ids';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('ids', $ids);
        $stmt->bindParam('priimek_in_ime', $priimek_in_ime);
        $stmt->bindParam('naslov', $naslov);
        $stmt->bindParam('postna_stevilka', $postna_stevilka);
        $stmt->bindParam('davcna_stevilka', $davcna_stevilka);

        //če je update uspešen
        if($stmt->execute()){
            
            return array(
                'success' => 'true',
                'info' => 'Podatki v sistemu ERP so bili uspešno posodobljeni <br>'
             );
         }
 
         //Če je napaka izpiše error info
         return array(
             'success' => 'false',
             'info' => 'Prišlo je do napake pri posodabljanju podatkov v sistemu ERP <br>'
          );
        
    }
}

//Parametri ki so potrebni za zagon Soap Serverja, handle funkcija dejansko omogoči da se lahko prejemajo requesti iz Clienta
$params = array('uri' => 'http://localhost/postar/server.php');
$server = new SoapServer(NULL, $params);        //prvi parameter NULL -> brez WSDL
$server->setClass('server');
$server->handle();

