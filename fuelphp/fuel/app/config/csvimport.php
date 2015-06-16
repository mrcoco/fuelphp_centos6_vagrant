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
            'model_name' => '\Model_Category',
            'file_name'  => 'category.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '\Model_Subcategory',
            'file_name'  => 'subcategory.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '\Model_Prefecture',
            'file_name'  => 'prefecture.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '\Model_Ngword',
            'file_name'  => 'ngword.csv',
            'is_relation'   => '',
        ),
        array(
            'model_name' => '\Model_Replace',
            'file_name'  => 'replace.csv',
            'is_relation'   => '',
        ),
        /*
        array(
            'model_name' => '',
            'file_name'  => 'category_subcategory.csv',
            'is_relation'   => 'has_many',
            'from'       => 'category',
            'to'         => 'subcategory',
        ),
        */
    ),
);