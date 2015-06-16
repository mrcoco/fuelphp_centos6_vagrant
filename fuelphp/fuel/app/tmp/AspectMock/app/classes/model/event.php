<?php
class Model_Event extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'description',
		'katakana',
		'hiragana',
		'alphabet',
		'date',
		'time',
		'venue_id',
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
		$val->add_field('date', 'Data', 'required|max_length[30]');
		$val->add_field('time', 'Time', 'required|max_length[45]');
		$val->add_field('venue_id', 'Venue Id', 'required|valid_string[numeric]');

		return $val;
	}

}
