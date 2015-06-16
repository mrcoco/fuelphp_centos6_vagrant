<?php

return array(

    /**
     * model_name: Model name. empty if relation
     * file_name: csv file nmae
     * is_relation: empty if not relation. 'has_many' is only supported currently
     * from: entity that has something. null when not relation
     * to: entity that is being had by from entity. null when not relation.
     */

    'configs' => array(
        array(
            'model_name' => '\Model_Event',
            'file_name'  => 'event.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '\Model_Performer',
            'file_name'  => 'performer.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '',
            'file_name'  => 'event_performer.csv',
            'is_relation'   => 'has_many',
            'from'       => 'performer',
            'to'         => 'event',
        ),
    ),
);