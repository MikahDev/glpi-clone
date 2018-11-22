<?php
class PluginCloneitemsClone extends CommonDBTM {

    // Define actions :
    static function showMassiveActionsSubForm(MassiveAction $ma) {

        switch ($ma->getAction()) {
            case 'CloneTicketTemplate':
                echo "Name";
                echo "</br>";
                    echo Html::input('name');
                echo "</br>";
                    echo Html::submit(__('Duplicate Item(s)'), array('name' => 'massiveaction'))."</span>";
                return true;
            case 'CloneNetworkEquipment':
                echo "<label>";
                echo __('Name : ');
                echo "</label>";
                    echo Html::input('name');
                echo "</br>";
                echo "<label>";
                echo __('Clone Operating system : ');
                echo "</label>";
                    echo Html::select('operatingSystem', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                    echo "<label>";
                    echo __('Clone Volumes : ');
                    echo "</label>";
                    echo Html::select('volumes', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                echo "<label>";
                echo __('Clone Network Ports/names : ');
                echo "</label>";
                    echo Html::select('networkPort', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                echo "<label>";
                echo __('Link Contracts : ');
                echo "</label>";
                    echo Html::select('linkContract', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                echo "<label>";
                echo __('Link Documents : ');
                echo "</label>";
                    echo Html::select('linkDocument', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                echo "<label>";
                echo __('Link knowledge base : ');
                echo "</label>";
                    echo Html::select('linkKnowledge', [1 => __('Yes'),0 => __('No')]);
                echo "</br>";
                    echo Html::submit(__('Duplicate Item(s)'), array('name' => 'massiveaction'))."</span>";
                return true;
        }
        return parent::showMassiveActionsSubForm($ma);
    }

    static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item,
                                                        array $ids) {
        global $DB;

        switch ($ma->getAction()) {

            case 'CloneTicketTemplate' :
                if ($item->getType() == 'TicketTemplate') {

                    //Get value for the input
                    $input = $ma->getInput();
                    $name = $input['name'];

                    //For each selected object, duplicate the row
                    foreach ($ids as $id) {
                        if ($item->getFromDB($id)) {
                            // Insert row for new ticket tempate
                            $DB->insert(
                                'glpi_tickettemplates', [
                                    'name'      => addslashes($name),
                                    'entities_id'  =>  $item->getEntityID($id),
                                    'is_recursive' => $item->isRecursive($id),
                                    'comment' => addslashes($item->getField('comment'))
                                ]
                            );

                            //Get ID for this row add
                            $id_insert = $DB->insert_id();

                            //Duplicate Mandatory fields
                            foreach ($DB->request("SELECT * FROM `glpi_tickettemplatemandatoryfields` WHERE tickettemplates_id='$id'") as $idMandatory => $row) {
                                $DB->insert(
                                    'glpi_tickettemplatemandatoryfields', [
                                        'tickettemplates_id'      => $id_insert,
                                        'num'  => $row['num']
                                    ]
                                );
                            }

                            //Duplicate Hidden fields
                            foreach ($DB->request("SELECT * FROM `glpi_tickettemplatehiddenfields` WHERE tickettemplates_id='$id'") as $idHidden => $row) {
                                $DB->insert(
                                    'glpi_tickettemplatehiddenfields', [
                                        'tickettemplates_id'      => $id_insert,
                                        'num'  => $row['num']
                                    ]
                                );
                            }

                            //Duplicate Mandatory fields
                            foreach ($DB->request("SELECT * FROM `glpi_tickettemplatepredefinedfields` WHERE tickettemplates_id='$id'") as $idPredefined => $row) {
                                $DB->insert(
                                    'glpi_tickettemplatepredefinedfields', [
                                        'tickettemplates_id'      => $id_insert,
                                        'num'  => $row['num'],
                                        'value' => $row['value']
                                    ]
                                );
                            }

                            $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                        } else {
                           $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_KO);
                           $ma->addMessage(__("Something went wrong"));
                        }
                    }
                    return;

                }
            case 'CloneNetworkEquipment' :
                if ($item->getType() == 'NetworkEquipment') {

                    //Get value for the input
                    $input = $ma->getInput();
                    $name = $input['name'];
                    $itemType = 'NetworkEquipment';

                    //For each selected object, duplicate the row
                    foreach ($ids as $id) {
                        if ($item->getFromDB($id)) {
                            // Insert row for new ticket tempate
                            $DB->insert(
                                'glpi_networkequipments', [
                                    'name'      => $name,
                                    'entities_id'  =>  $item->getEntityID($id),
                                    'is_recursive' => $item->isRecursive($id),
                                    'ram' => addslashes($item->getField('ram')),
                                    'serial' => addslashes($item->getField('serial')),
                                    'otherserial' => addslashes($item->getField("otherserial")),
                                    'contact' => addslashes($item->getField('contact')),
                                    'contact_num' => addslashes($item->getField('contact_num')),
                                    'users_id_tech' => $item->getField('users_id_tech'),
                                    'groups_id_tech' => $item->getField('groups_id_tech'),
                                    'date_mod' => NULL,
                                    'comment' => addslashes($item->getField('comment')),
                                    'locations_id' => $item->getField('locations_id'),
                                    'domains_id' => $item->getField('domains_id'),
                                    'networks_id' => $item->getField('networks_id'),
                                    'networkequipmenttypes_id' => $item->getField('networkequipmenttypes_id'),
                                    'networkequipmentmodels_id' => $item->getField('networkequipmentmodels_id'),
                                    'manufacturers_id' => $item->getField('manufacturers_id'),
                                    'is_deleted' => $item->getField('is_deleted'),
                                    'users_id' => $item->getField('users_id'),
                                    'groups_id' => $item->getField('groups_id'),
                                    'states_id' => $item->getField('states_id'),
                                    'ticket_tco' => '0.0000',
                                    'is_dynamic' => '0',
                                    'date_creation' => date("Y-m-d H:i:s")
                                ]
                            );

                            //Get ID for this row add
                            $id_insert = $DB->insert_id();

                            //Clone Operating Systems
                            if($input['operatingSystem'] == 1){

                                $req = $DB->request("SELECT * FROM `glpi_items_operatingsystems` WHERE items_id='$id' AND itemtype='NetworkEquipment'");
                                if ($row = $req->next()) {
                                    $DB->insert(
                                        'glpi_items_operatingsystems', [
                                            'items_id'      => $id_insert,
                                            'itemtype'      => 'NetworkEquipment',
                                            'operatingsystems_id'      => $row['operatingsystems_id'],
                                            'operatingsystemversions_id'      => $row['operatingsystemversions_id'],
                                            'operatingsystemservicepacks_id'      => $row['operatingsystemservicepacks_id'],
                                            'operatingsystemarchitectures_id'      => $row['operatingsystemarchitectures_id'],
                                            'operatingsystemkernelversions_id'      => $row['operatingsystemkernelversions_id'],
                                            'license_number'  => addslashes($row['license_number']),
                                            'license_id'  => addslashes($row['license_id']),
                                            'operatingsystemeditions_id'  => $row['operatingsystemeditions_id'],
                                            'date_mod'  => NULL,
                                            'date_creation'  => date("Y-m-d H:i:s"),
                                            'is_deleted'  => $row['is_deleted'],
                                            'is_dynamic' => '0',
                                            'entities_id' => $row['entities_id'],
                                            'is_recursive' => $row['is_recursive']
                                        ]
                                    );
                                }
                            }

                            //Clone Volumes
                            if($input['volumes'] == 1) {
                                foreach ($DB->request("SELECT * FROM `glpi_items_disks` WHERE items_id='$id' AND itemtype='NetworkEquipment'") as $idVolumes => $row) {
                                    $DB->insert(
                                        'glpi_items_disks', [
                                            'entities_id' => $row['entities_id'],
                                            'itemtype' => 'NetworkEquipment',
                                            'items_id' => $id_insert,
                                            'name' => addslashes($row['name']),
                                            'device' => addslashes($row['device']),
                                            'mountpoint' => addslashes($row['mountpoint']),
                                            'filesystems_id' => $row['filesystems_id'],
                                            'totalsize' => $row['totalsize'],
                                            'freesize' => $row['freesize'],
                                            'date_mod' => NULL,
                                            'date_creation' => date("Y-m-d H:i:s"),
                                            'is_deleted' => $row['is_deleted'],
                                            'is_dynamic' => '0'
                                        ]
                                    );
                                }
                            }

                            //Clone Network port
                            if($input['networkPort'] == 1) {
                                foreach ($DB->request("SELECT * FROM `glpi_networkports` WHERE items_id='$id' AND itemtype='NetworkEquipment'") as $idNetworkPort => $row) {
                                    $DB->insert(
                                        'glpi_networkports', [
                                            'items_id' => $id_insert,
                                            'itemtype' => 'NetworkEquipment',
                                            'entities_id' => $row['entities_id'],
                                            'is_recursive' => $row['is_recursive'],
                                            'logical_number' => $row['logical_number'],
                                            'name' => addslashes($row['name']),
                                            'instantiation_type' => addslashes($row['instantiation_type']),
                                            'mac' => addslashes($row['mac']),
                                            'comment' => addslashes($row['comment']),
                                            'is_deleted' => $row['is_deleted'],
                                            'is_dynamic' => '0',
                                            'date_mod' => NULL,
                                            'date_creation' => date("Y-m-d H:i:s")
                                        ]
                                    );

                                    $networkPortId = $DB->insert_id();

                                    //Clone Vlan associations
                                    $req = $DB->request("SELECT * FROM `glpi_networkports_vlans` WHERE networkports_id='$idNetworkPort'");
                                    if ($rowVlan = $req->next()) {
                                        $DB->insert(
                                            'glpi_networkports_vlans', [
                                                'networkports_id' => $networkPortId,
                                                'vlans_id' => $rowVlan['vlans_id'],
                                                'tagged' => $rowVlan['tagged']
                                            ]
                                        );
                                    }

                                    //Clone Port name
                                    $req = $DB->request("SELECT * FROM `glpi_networknames` WHERE items_id='$idNetworkPort'");
                                    if ($rowName = $req->next()) {
                                        $DB->insert(
                                            'glpi_networknames', [
                                                'entities_id' => $rowName['entities_id'],
                                                'items_id' => $networkPortId,
                                                'itemtype' => $rowName['itemtype'],
                                                'name' => $rowName['name'],
                                                'comment' => addslashes($rowName['comment']),
                                                'fqdns_id' => $rowName['fqdns_id'],
                                                'is_deleted' => $rowName['is_deleted'],
                                                'is_dynamic' => '0',
                                                'date_mod' => NULL,
                                                'date_creation' => date("Y-m-d H:i:s")
                                            ]
                                        );

                                        $networkNameId = $DB->insert_id();
                                        $cloneNetworkNameId = $rowName['id'];

                                        foreach ($DB->request("SELECT * FROM `glpi_ipaddresses` WHERE items_id='$cloneNetworkNameId' AND itemtype='NetworkName'") as $idNetworkName => $rowIp) {
                                            $DB->insert(
                                                'glpi_ipaddresses', [
                                                    'entities_id' => $rowIp['entities_id'],
                                                    'items_id' => $networkNameId,
                                                    'itemtype' => $rowIp['itemtype'],
                                                    'version' => $rowIp['version'],
                                                    'name' => addslashes($rowIp['name']),
                                                    'binary_0' => $rowIp['binary_0'],
                                                    'binary_1' => $rowIp['binary_1'],
                                                    'binary_2' => $rowIp['binary_2'],
                                                    'binary_3' => $rowIp['binary_3'],
                                                    'is_deleted' => $rowIp['is_deleted'],
                                                    'is_dynamic' => '0',
                                                    'mainitems_id' => $id_insert,
                                                    'mainitemtype' => $rowIp['mainitemtype']
                                                ]
                                            );

                                        }


                                    }

                                    //If the network port is an Wifi port clone wifi info
                                    if ($row['instantiation_type'] == 'NetworkPortWifi') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportwifis` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowWifi = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportwifis', [
                                                    'networkports_id' => $networkPortId,
                                                    'items_devicenetworkcards_id' => $rowWifi['items_devicenetworkcards_id'],
                                                    'wifinetworks_id' => $rowWifi['wifinetworks_id'],
                                                    'networkportwifis_id' => $rowWifi['networkportwifis_id'],
                                                    'version' => addslashes($rowWifi['version']),
                                                    'mode' => addslashes($rowWifi['mode']),
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an local port clone local info
                                    if ($row['instantiation_type'] == 'NetworkPortLocal') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportlocals` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowLocal = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportlocals', [
                                                    'networkports_id' => $networkPortId,
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an ethernet port clone ethernet info
                                    if ($row['instantiation_type'] == 'NetworkPortEthernet') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportethernets` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowEthernet = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportethernets', [
                                                    'networkports_id' => $networkPortId,
                                                    'items_devicenetworkcards_id' => $rowEthernet['items_devicenetworkcards_id'],
                                                    'netpoints_id' => $rowEthernet['netpoints_id'],
                                                    'type' => $rowEthernet['type'],
                                                    'speed' => $rowEthernet['speed'],
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an Fiber Channel port clone Fiber info
                                    if ($row['instantiation_type'] == 'NetworkPortFiberchannel') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportfiberchannels` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowFiber = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportfiberchannels', [
                                                    'networkports_id' => $networkPortId,
                                                    'items_devicenetworkcards_id' => $rowFiber['items_devicenetworkcards_id'],
                                                    'netpoints_id' => $rowFiber['netpoints_id'],
                                                    'wwn' => $rowFiber['wwn'],
                                                    'speed' => $rowFiber['speed'],
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an Dialup port clone Dialup info
                                    if ($row['instantiation_type'] == 'NetworkPortDialup') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportdialups` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowDialup = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportdialups', [
                                                    'networkports_id' => $networkPortId,
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an Alias port clone Alias info
                                    if ($row['instantiation_type'] == 'NetworkPortAlias') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportaliases` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowAlias = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportaliases', [
                                                    'networkports_id' => $networkPortId,
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }

                                    //If the network port is an Aggregate port clone Aggregate info
                                    if ($row['instantiation_type'] == 'NetworkPortAggregate') {
                                        $req = $DB->request("SELECT * FROM `glpi_networkportaggregates` WHERE networkports_id='$idNetworkPort'");
                                        if ($rowAggregate = $req->next()) {
                                            $DB->insert(
                                                'glpi_networkportaggregates', [
                                                    'networkports_id' => $networkPortId,
                                                    'networkports_id_list' => '[]',
                                                    'date_mod' => NULL,
                                                    'date_creation' => date("Y-m-d H:i:s")
                                                ]
                                            );
                                        }
                                    }
                                }
                            }

                            //Link contract
                            if($input['linkContract'] == 1) {
                                $req = $DB->request("SELECT * FROM `glpi_contracts_items` WHERE items_id='$id' AND itemtype='$itemType'");
                                if ($rowContract = $req->next()) {
                                    $DB->insert(
                                        'glpi_contracts_items', [
                                            'contracts_id' => $rowContract['contracts_id'],
                                            'items_id' => $id_insert,
                                            'itemtype' => $itemType
                                        ]
                                    );
                                }
                            }

                            //Link documents
                            if($input['linkDocument'] == 1) {
                                $req = $DB->request("SELECT * FROM `glpi_documents_items` WHERE items_id='$id' AND itemtype='$itemType'");
                                if ($rowDocument = $req->next()) {
                                    $DB->insert(
                                        'glpi_documents_items', [
                                            'documents_id' => $rowDocument['documents_id'],
                                            'items_id' => $id_insert,
                                            'itemtype' => $itemType,
                                            'entities_id' => $rowDocument['entities_id'],
                                            'is_recursive' => $rowDocument['is_recursive'],
                                            'date_mod' => $rowDocument['date_mod'],
                                            'users_id' => $rowDocument['users_id'],
                                            'timeline_position' => $rowDocument['timeline_position']
                                        ]
                                    );
                                }
                            }

                            // TODO : Knowledge
                            // TODO : Notes
                            // TODO : Certificates

                            $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                        }else{
                            $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_KO);
                            $ma->addMessage(__("Something went wrong"));
                        }
                    }
                    return;

                }

        }
        parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
    }

}
