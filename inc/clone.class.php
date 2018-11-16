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
                                    'name'      => $name,
                                    'entities_id'  =>  $item->getEntityID($id),
                                    'is_recursive' => $item->isRecursive($id),
                                    'comment' => NULL
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

        }
        parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
    }

}
