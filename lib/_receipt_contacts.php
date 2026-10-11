<?php
// Input receipt contacts. Selected by the transaction's saved province.
function cashtrack_receipt_contacts($province){
    $common = array('Sales Email' => 'sales@viranawawaran.af', 'Finance Email' => 'finance@viranawawaran.af', 'Website' => 'www.viranawawaran.af');
    $profiles = array(
        'kabul' => array(
            'label' => 'Kabul',
            'contacts' => array_merge(array('Phone' => '0788676763'), $common, array(
                'Main Office' => 'Dasht-e-Barchi, Naqash Bus Stop',
                'Pol-e-Sorkh Branch' => 'Market Mili, Office No. G17',
                'Company Branch' => 'Benid Khali, Qandahar Market, Office No. 26'
            )),
            'bank' => array('Bank Name' => 'GHAZANFAR BANK', 'Account Name' => 'VIRA NAWAWARAN ICT', 'Account No. (AFN)' => 'I189110000085', 'SWIFT Code' => 'GHBKAFKAXXX')
        ),
        'ghazni' => array(
            'label' => 'Ghazni',
            'contacts' => array_merge(array('Phone' => '0794 15 32 20 / 0795 65 44 31 / 0795303498'), $common, array(
                'Angori Office' => 'Bazaar Angori, next to Sadaqat Market',
                'Ghojor Office' => 'Bazaar Ghojor, Sangmasha Line, Tank-e-tail Akbari',
                'Sangmasha Office' => 'Bazaar Sangmasha, Wedding Hall and Imperial Jaghori Restaurant, top floor'
            )),
            'bank' => array('Bank Name' => 'GHAZANFAR BANK', 'Account Name' => 'VIRA NAWAWARAN ICT', 'Account No. (AFN)' => 'I189110000085', 'SWIFT Code' => 'GHBKAFKAXXX')
        )
    );
    $key = strtolower(trim((string)$province));
    $aliases = array('کابل' => 'kabul', 'غزنی' => 'ghazni', 'غزني' => 'ghazni');
    $key = $aliases[$key] ?? $key;
    // Never show another province's bank details for an unconfigured province.
    return $profiles[$key] ?? array('label' => trim((string)$province), 'contacts' => $common, 'bank' => array());
}
