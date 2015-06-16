<?php
class Model_Performer extends \Orm\Model
{
	// relation の設定
	protected static $_many_many = array(
		'events' => array(
			'key_from' => 'id',
			'key_through_from' => 'performer_id',
			'table_through' => 'events_performer',
			'key_through_to' => 'event_id',
			'model_to' => 'Model_Event',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	protected static $_properties = array(
		'id',
		'name',
		'description',
		'katakana',
		'hiragana',
		'alphabet',
		'nickname1',
		'nickname2',
		'nickname3',
		'subcategory_id',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	public static function validate($factory)
	{ if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($factory), true)) !== __AM_CONTINUE__) return $__am_res; 
		$val = Validation::forge($factory);
		$val->add_field('name', 'Name', 'required|max_length[256]');
		$val->add_field('description', 'Description', 'required|max_length[512]');
		$val->add_field('katakana', 'Katakana', 'required|max_length[256]');
		$val->add_field('hiragana', 'Hiragana', 'required|max_length[256]');
		$val->add_field('alphabet', 'Alphabet', 'required|max_length[256]');
		$val->add_field('nickname1', 'Nickname1', 'required|max_length[256]');
		$val->add_field('nickname2', 'Nickname2', 'required|max_length[256]');
		$val->add_field('nickname3', 'Nickname3', 'required|max_length[256]');
		$val->add_field('subcategory_id', 'Subcategory Id', 'required|valid_string[numeric]');

		return $val;
	}

}
