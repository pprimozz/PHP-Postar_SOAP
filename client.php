<?php
class client{

    //konstruktor setira ustrezne podatke za Soap Clienta
    public function __construct(){
        $params= array('location' => 'http://localhost/postar/server.php',
                        'uri' => 'http://localhost/postar/server.php',
                        'trace' => 1);
        $this->instance= new SoapClient(NULL, $params);    //prvi parameter NULL -> brez WSDL
    }

    public function getCustomer($id){
        return $this->instance->__soapCall('getCustomerInfo', $id);
    }

    //Posodobi Customerja v CRM
    public function updateCustomerCRM($id){
        //Trenutni podatki v bazi
        $current_info = $this->getCustomer($id);

        //Pridobi in filtrira ustrezne podatke za update, v primeru da je prazno polje se ohrani trenutna vrednost polja v bazi
        $idForCRM['ids'] = $id['ids'];
        $idForCRM['ime'] = ($id['ime']!="" ? $id['ime'] : $current_info['ime']);
        $idForCRM['priimek']=($id['priimek']!="" ? $id['priimek'] : $current_info['priimek']);
        $idForCRM['naslov']=($id['naslov']!="" ? $id['naslov'] : $current_info['naslov']);
        $idForCRM['postna_stevilka']=($id['postna_stevilka']!="" ? $id['postna_stevilka'] : $current_info['postna_stevilka']);
        $idForCRM['davcna_stevilka']=($id['davcna_stevilka']!="" ? $id['davcna_stevilka'] : $current_info['davcna_stevilka']);
        $idForCRM['tel_stevilka']=($id['tel_stevilka']!="" ? $id['tel_stevilka'] : $current_info['tel_stevilka']);

        return $this->instance->__soapCall('updateCRM', array($idForCRM));
    }

        //Posodobi Customerja v BIL
        public function updateCustomerBil($id){
        $sys = array('system' =>'bil');

        //najprej pogleda v tabelo failed_attemps in poskusi poslati podatke ki se v prejšnjem updejtu niso izvedli zaradi izpada sistema
        $failedAtt = $this->instance->__soapCall('getFailedIds', array($sys));
        //v primeru da so zapisi v failed_attempts
        if(!is_null($failedAtt))
        {
            for ($i=0; $i<count($failedAtt); $i++)
            {
                //Pridobi podatke iz CRM in filtrira ustrezne podatke za update
                $idarr['ids']=$failedAtt[$i]['ids']; 
                $idarr['system']=$failedAtt[$i]['system']; 
                $current_info = $this->getCustomer($idarr);

                $idForBil['ids'] = $current_info['ids'];
                $idForBil['ime'] = $current_info['ime'];
                $idForBil['priimek']=$current_info['priimek'];
                $idForBil['naslov']=$current_info['naslov'];
                $idForBil['postna_st_naziv_poste']=$current_info['postna_stevilka'];
                $idForBil['davcna_stevilka']=$current_info['davcna_stevilka'];

                //Izvede update
                $res = $this->instance->__soapCall('updateBIL', array($idForBil));

                //V primeru uspešnega updejta se zapis izbriše iz tabele failed_attempts
                if ($res['success'] == 'true') {
                    $test= $this->instance->__soapCall('deleteFailedIds', array($idarr));
                }
            }
        }

        //Trenutni podatki v bazi
        $current_info = $this->getCustomer($id);

        //Pridobi in filtrira ustrezne podatke za update, v primeru da je prazno polje se ohrani trenutna vrednost polja v bazi
        $idForBil['ids'] = $id['ids'];
        $idForBil['ime'] = ($id['ime']!="" ? $id['ime'] : $current_info['ime']);
        $idForBil['priimek']=($id['priimek']!="" ? $id['priimek'] : $current_info['priimek']);
        $idForBil['naslov']=($id['naslov']!="" ? $id['naslov'] : $current_info['naslov']);
        $idForBil['postna_st_naziv_poste']=($id['postna_stevilka']!="" ? $id['postna_stevilka'] : $current_info['postna_stevilka']);
        $idForBil['davcna_stevilka']=($id['davcna_stevilka']!="" ? $id['davcna_stevilka'] : $current_info['davcna_stevilka']);

        $res = $this->instance->__soapCall('updateBIL', array($idForBil));

        //V primeru da pride do izpada sistema se podatek za update (ids in vrsta ciljnega sistema) shrani v tabelo failed_attempts in ga poizkusi poslati ob naslednjem klicu funkcije
        if ($res['success'] != 'true') {

            $idForInsert['ids'] = $idForBil['ids'];
            $idForInsert['system'] ='bil';

            $failed = $this->instance->__soapCall('insertFailedId', array($idForInsert));
            return $res['info'];  
        }
         return $res['info']; 
        
    }

        //Posodobi Customerja v AUT
      public function updateCustomerAut($id){

        $sys = array('system' =>'aut');

        //najprej pogleda v tabelo failed_attemps in poskusi poslati podatke ki se v prejšnjem updejtu niso izvedli zaradi izpada sistema
        $failedAtt = $this->instance->__soapCall('getFailedIds', array($sys));
        //v primeru da so zapisi v failed_attempts
        if(!is_null($failedAtt))
        {
            for ($i=0; $i<count($failedAtt); $i++)
            {
                //Pridobi podatke iz CRM in filtrira ustrezne podatke za update
                $idarr['ids']=$failedAtt[$i]['ids']; 
                $idarr['system']=$failedAtt[$i]['system']; 
                $current_info = $this->getCustomer($idarr);

                $idForAut['ids'] = $current_info['ids'];
                $ime = $current_info['ime'];
                $priimek=$current_info['priimek'];
                $idForAut['ime_priimek'] = $ime . ' ' . $priimek;

                //Izvede update
                $res = $this->instance->__soapCall('updateAUT', array($idForAut));

                //V primeru uspešnega updejta se zapis izbriše iz tabele failed_attempts
                if ($res['success'] == 'true') {
                    $test= $this->instance->__soapCall('deleteFailedIds', array($idarr));
                }
            }
        }


        //Trenutni podatki v bazi
        $current_info = $this->getCustomer($id);

        //Pridobi in filtrira ustrezne podatke za update, v primeru da je prazno polje se ohrani trenutna vrednost polja v bazi
        $idForAut['ids'] = $id['ids'];
        $ime = ($id['ime']!="" ? $id['ime'] : $current_info['ime']);
        $priimek=($id['priimek']!="" ? $id['priimek'] : $current_info['priimek']);
        $idForAut['ime_priimek'] = $ime . ' ' . $priimek;

        $res= $this->instance->__soapCall('updateAUT', array($idForAut));

        //V primeru da pride do izpada sistema se podatek za update (ids in vrsta ciljnega sistema) shrani v tabelo failed_attempts in ga poizkusi poslati ob naslednjem klicu funkcije
        if ($res['success'] != 'true') {

            $idForInsert['ids'] = $idForAut['ids'];
            $idForInsert['system'] ='aut';

            $failed = $this->instance->__soapCall('insertFailedId', array($idForInsert));
            return $res['info'];  
        }
         return $res['info']; 
    }

    //Posodobi Customerja v ERP
    public function updateCustomerErp($id){

        $sys = array('system' =>'erp');

        //najprej pogleda v tabelo failed_attemps in poskusi poslati podatke ki se v prejšnjem updejtu niso izvedli zaradi izpada sistema
        $failedAtt = $this->instance->__soapCall('getFailedIds', array($sys));
        //v primeru da so zapisi v failed_attempts
        if(!is_null($failedAtt))
        {
            for ($i=0; $i<count($failedAtt); $i++)
            {
                //Pridobi podatke iz CRM in filtrira ustrezne podatke za update
                $idarr['ids']=$failedAtt[$i]['ids']; 
                $idarr['system']=$failedAtt[$i]['system']; 
                $current_info = $this->getCustomer($idarr);

                $idForErp['ids'] = $current_info['ids'];
                $ime = $current_info['ime'];
                $priimek=$current_info['priimek'];
                $idForErp['priimek_in_ime'] = $priimek . ' ' . $ime;
                $idForErp['naslov']=$current_info['naslov'];
                $idForErp['postna_stevilka']=$current_info['postna_stevilka'];
                $idForErp['davcna_stevilka']=$current_info['davcna_stevilka'];

                //Izvede update
                $res = $this->instance->__soapCall('updateERP', array($idForErp));

                //V primeru uspešnega updejta se zapis izbriše iz tabele failed_attempts
                if ($res['success'] == 'true') {
                    $test= $this->instance->__soapCall('deleteFailedIds', array($idarr));
                }
            }
        }


        //Trenutni podatki v bazi
        $current_info = $this->getCustomer($id);

        //Pridobi in filtrira ustrezne podatke za update, v primeru da je prazno polje se ohrani trenutna vrednost polja v bazi
        $idForErp['ids'] = $id['ids'];
        $ime = ($id['ime']!="" ? $id['ime'] : $current_info['ime']);
        $priimek=($id['priimek']!="" ? $id['priimek'] : $current_info['priimek']);
        $idForErp['priimek_in_ime'] = $priimek . ' ' . $ime;
        $idForErp['naslov']=($id['naslov']!="" ? $id['naslov'] : $current_info['naslov']);
        $idForErp['postna_stevilka']=($id['postna_stevilka']!="" ? $id['postna_stevilka'] : $current_info['postna_stevilka']);
        $idForErp['davcna_stevilka']=($id['davcna_stevilka']!="" ? $id['davcna_stevilka'] : $current_info['davcna_stevilka']);

        $res = $this->instance->__soapCall('updateERP', array($idForErp));

        //V primeru da pride do izpada sistema se podatek za update (ids in vrsta ciljnega sistema) shrani v tabelo failed_attempts in ga poizkusi poslati ob naslednjem klicu funkcije
        if ($res['success'] != 'true') {

            $idForInsert['ids'] = $idForErp['ids'];
            $idForInsert['system'] ='erp';

            $failed = $this->instance->__soapCall('insertFailedId', array($idForInsert));
            return $res['info'];  
        }
         return $res['info']; 
    }
}

// objekt clienta ki je potreben za klic iz testne skripte
$client = new client;