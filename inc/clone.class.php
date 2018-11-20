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
                echo "Name";
                echo "</br>";
                    echo Html::input('name');
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
                            foreach ($DB->request("SELECT * FROM `glpi_tickettemplatehiddenfields` WHERE tickettemplates_id='$id'") as $idMandatory => $row) {
                                $DB->insert(
                                    'glpi_tickettemplatehiddenfields', [
                                        'tickettemplates_id'      => $id_insert,
                                        'num'  => $row['num']
                                    ]
                                );
                            }

                            //Duplicate Mandatory fields
                            foreach ($DB->request("SELECT * FROM `glpi_tickettemplatepredefinedfields` WHERE tickettemplates_id='$id'") as $idMandatory => $row) {
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

                            //Add Operating Systems
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

                            //Add Volumes
                            $req = $DB->request("SELECT * FROM `glpi_items_disks` WHERE items_id='$id' AND itemtype='NetworkEquipment'");
                            if ($row = $req->next()) {
                                $DB->insert(
                                    'glpi_items_disks', [
                                        'entities_id'      => $row['entities_id'],
                                        'itemtype'      => 'NetworkEquipment',
                                        'items_id'      => $id_insert,
                                        'name'      => addslashes($row['name']),
                                        'device'      => addslashes($row['device']),
                                        'mountpoint'      => addslashes($row['mountpoint']),
                                        'filesystems_id'      => $row['filesystems_id'],
                                        'totalsize'      => $row['totalsize'],
                                        'freesize'  => $row['freesize'],
                                        'date_mod'  => NULL,
                                        'date_creation'  => date("Y-m-d H:i:s"),
                                        'is_deleted'  => $row['is_deleted'],
                                        'is_dynamic' => '0'
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

        }
        parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
    }

}
