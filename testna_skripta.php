<?php
include './client.php';

//Tukaj se vnesejo dejanski podatki za TEST, IDS  mora biti pravilna vrednost kot je v bazi za customerja, ostali podatki se posodabljajo
// v primeru da se določen podatek ne želi posodobiti (se želi ohranit trenutna vrednost v bazi), se polje pusti prazno -> prazen string
$userInfoForUpdate = array(
            'ids' => '1',
            'ime' => 'Primoz',
            'priimek' => 'Pucko',
            'naslov' => 'Beltinci 123',
            'postna_stevilka' => '12366',
            'davcna_stevilka' => '4444',
            'tel_stevilka' => '041589654'
        );

//Posodobi podatke najprej v CRM, če je uspešno še v ostale sisteme
if($client->updateCustomerCRM($userInfoForUpdate)['succes']=='true')
    { 
        echo "Podatki v CRM so bili uspešno posodobljeni <br>";
        echo $client->updateCustomerBil($userInfoForUpdate);
        echo $client->updateCustomerAut($userInfoForUpdate);
        echo $client->updateCustomerErp($userInfoForUpdate);
    }
    //v Primeru da update v CRM ni uspešen se podatki v ostale ciljne sisteme ne pošiljajo
else {
    echo "Prišlo je do napake pri posodabljanju podatkov v CRM";
} 
