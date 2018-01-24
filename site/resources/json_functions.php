<?php

// user database structure
class db_students
{
	var $ID;
	var $NAME;
	var $LOGIN;
	var $PASSWORD;
	var $STATUS;
	var $JOKER;
	var $GROUP;
	var $EMAIL;
	var $KARMA;
}

class db_groups
{
	var $ID;
	var $NAME;
	var $JOKER;
}

class db_placement_list
{
	var $ID;
	var $NAME;
	var $GROUPS;
	var $ACTIVE;
	var $DUE_DATE;
}

class db_placements
{
	var $ID;
	var $ESSENTIAL;
	var $NAME;
	var $DEPLOYMENT;
	var $LOCATION;
	var $TIMEFRAME_BEGIN;
	var $TIMEFRAME_END;
	var $PLACES_MIN;
	var $PLACES_MAX;
}

class db_wishlist
{
	var $ID;
	public $DEPLOYMENTS = array();
	var $CUSTOM_TIMEFRAME_UNAVAILABLE;
	public $TIMEFRAMES_UNAVAILABLE = array();
}

class timeframe
{
	var $begin;
	var $end;
}

class pw_reset_table
{
	var $ID;
	var $CODE;
	var $TIMESTAMP;
}

// GENERAL JSON functions
function fetch_json_table($file)
{
	$filepath = get_DB_PATH() . DIRECTORY_SEPARATOR . $file;
	if(!is_file($filepath))
		return false;
	$this_fetch = file_get_contents($filepath);
	if(($this_fetch === false) OR !(count(json_decode($this_fetch, true)) > 0))
	{
		return false;
	}
	else
	{
		return json_decode($this_fetch, true);
	}
}

function count_highest_ID($array)
{
	$array_count = 0;
	if(!empty($array))
	{
		$max_array = array();
		foreach($array as $key)
		{
			$max_array[$array_count] = $key->ID;
			$array_count++;
		}
		if(max($max_array) >= count($array))
		{
			return max($max_array);
		}
		else
		{
			return count($array);
		}
	}
	else
	{
		return 0;
	}
}

function encode_json_items(&$item, $key)
{
	$item = utf8_encode($item);
}

// JSON functions

function fetch_students()
{
	$student_table = fetch_json_table('students.json');
	if(!($student_table === false))
	{
		$student_count = 0;
		foreach($student_table as $this_table_student)
		{
			$students[$student_count] = new db_students;
			$students[$student_count]->ID = $this_table_student["ID"];
			$students[$student_count]->NAME = $this_table_student["NAME"];
			$students[$student_count]->LOGIN = $this_table_student["LOGIN"];
			$students[$student_count]->PASSWORD = $this_table_student["PASSWORD"];
			$students[$student_count]->STATUS = $this_table_student["STATUS"];
			$students[$student_count]->GROUP = $this_table_student["GROUP"];
			$students[$student_count]->EMAIL = $this_table_student["EMAIL"];
			$students[$student_count]->KARMA = $this_table_student["KARMA"];
			$students[$student_count]->JOKER = $this_table_student["JOKER"];
			$student_count = $student_count + 1;
		}
		return $students;
	}
}

function fetch_student_by_login($student_login)
{
	$student_table = fetch_json_table('students.json');
	if(!($student_table === false))
	{
		foreach($student_table as $this_table_student)
		{
			if($this_table_student["LOGIN"] == $student_login)
			{
				$student = new db_students;
				$student->ID = $this_table_student["ID"];
				$student->NAME = $this_table_student["NAME"];
				$student->LOGIN = $this_table_student["LOGIN"];
				$student->PASSWORD = $this_table_student["PASSWORD"];
				$student->STATUS = $this_table_student["STATUS"];
				$student->GROUP = $this_table_student["GROUP"];
				$student->EMAIL = $this_table_student["EMAIL"];
				$student->KARMA = $this_table_student["KARMA"];
				$student->JOKER = $this_table_student["JOKER"];
			}
		}
		if(isset($student))
		{
			return $student;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function fetch_student_by_id($student_id)
{
	$student_table = fetch_json_table('students.json');
	if(!($student_table === false))
	{
		foreach($student_table as $this_table_student)
		{
			if($this_table_student["ID"] == $student_id)
			{
				$student = new db_students;
				$student->ID = $this_table_student["ID"];
				$student->NAME = $this_table_student["NAME"];
				$student->LOGIN = $this_table_student["LOGIN"];
				$student->PASSWORD = $this_table_student["PASSWORD"];
				$student->STATUS = $this_table_student["STATUS"];
				$student->GROUP = $this_table_student["GROUP"];
				$student->EMAIL = $this_table_student["EMAIL"];
				$student->KARMA = $this_table_student["KARMA"];
				$student->JOKER = $this_table_student["JOKER"];
			}
		}
		if(isset($student))
		{
			return $student;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function insert_new_student($name, $login, $password, $email, $group)
{
	$placement_student = fetch_students();

	$new_key = count_highest_ID($placement_student) + 1;

	$joker = 0;
	$fetched_groups = fetch_groups();
	foreach($fetched_groups as $this_group)
	{
		if($this_group->ID == $group)
		{
			$joker = $this_group->JOKER;
		}
	}
	$placement_student[$new_key] = new db_students;
	$placement_student[$new_key]->ID = $new_key;
	$placement_student[$new_key]->NAME = $name;
	$placement_student[$new_key]->LOGIN = $login;
	$placement_student[$new_key]->PASSWORD = $password;
	$placement_student[$new_key]->STATUS = "UNCONFIRMED";
	$placement_student[$new_key]->GROUP = $group;
	$placement_student[$new_key]->JOKER = $joker;
	$placement_student[$new_key]->EMAIL = $email;
	$placement_student[$new_key]->KARMA = "0";

	if(file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'students.json', json_encode($placement_student)))
	{
		return $new_key;
	}
	else
	{
		return false;
	}
}

// GROUPS
function fetch_groups()
{
	$groups_table = fetch_json_table('groups.json');
	if(!($groups_table === false))
	{
		$group_count = 0;
		foreach($groups_table as $this_table_group)
		{
			$groups[$group_count] = new db_groups;
			$groups[$group_count]->ID = $this_table_group["ID"];
			$groups[$group_count]->NAME = $this_table_group["NAME"];
			$groups[$group_count]->JOKER = $this_table_group["JOKER"];
			$group_count++;
		}
		return $groups;
	}
}

function check_for_duplicate($field, $value, $database)
{
	$found = 0;
	$data_count = 0;
	foreach($database as $data)
	{
		if($data->$field == $value)
		{
			$found = 1;
		}
		$data_count = $data_count + 1;
	}
	if($found == 0)
	{
		return false;
	}
	else
	{
		return "field " . $field . " already exists in the database";
	}
}

// PLACEMENT LISTS

function fetch_placement_list()
{
	$placement_table = fetch_json_table('placements.json');
	if(!($placement_table === false))
	{
		$placement_count = 0;
		foreach($placement_table as $this_placement)
		{
			$placements[$placement_count] = new db_placement_list;
			$placements[$placement_count]->ID = $this_placement["ID"];
			$placements[$placement_count]->NAME = $this_placement["NAME"];
			$placements[$placement_count]->GROUPS = $this_placement["GROUPS"];
			$placements[$placement_count]->DUE_DATE = $this_placement["DUE_DATE"];
			$placements[$placement_count]->ACTIVE = $this_placement["ACTIVE"];
			$placement_count++;
		}
		return $placements;
	}
	else
	{
		file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'placements.json', "");
		return false;
	}
}

function insert_new_placement_list($name, $groups, $id, $due_date)
{
	$placements = fetch_placement_list();
	if($placements === false)
	{
		$new_key = 0;
	}
	else
	{
		$new_key = count($placements) + 1;
	}
	$placements[$new_key] = new db_placement_list;
	$placements[$new_key]->ID = $id;
	$placements[$new_key]->NAME = $name;
	$placements[$new_key]->GROUPS = $groups;
	$placements[$new_key]->DUE_DATE = $due_date;
	$placements[$new_key]->ACTIVE = 0;
	file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'placements.json', json_encode($placements));
	file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'placement_' . $id . '.json', "");
	file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'wishlist_' . $id . '.json', "");

	return true;
}

function check_for_placement_validity($placement_id, $user_group_id)
{
	$placement_exists = false;
	$valid = false;
	$valid_groups = array();
	$placement_table = fetch_json_table('placements.json');
	if(!($placement_table === false))
	{
		foreach($placement_table as $this_placement)
		{
			if($this_placement["ID"] == $placement_id)
			{
				$placement_exists = true;
				$valid_groups = explode(";", $this_placement["GROUPS"]);
				$this_active = $this_placement["ACTIVE"];
			}
		}
	}
	if($placement_exists)
	{
		foreach($valid_groups as $group)
		{
			if(($group == $user_group_id) && !(is_dir(get_DB_PATH() . DIRECTORY_SEPARATOR . 'calculation_' . $placement_id . DIRECTORY_SEPARATOR)))
			{
				$valid = true;
			}
		}
	}
	if($valid && $this_active == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

// PLACEMENTS

function fetch_placements($id)
{
	$placement_table = fetch_json_table('placement_' . $id . '.json');
	if(!($placement_table === false))
	{
		$placement_count = 0;
		foreach($placement_table as $this_placement)
		{
			$placements[$placement_count] = new db_placements;
			$placements[$placement_count]->ID = $this_placement["ID"];
			$placements[$placement_count]->ESSENTIAL = $this_placement["ESSENTIAL"];
			$placements[$placement_count]->NAME = $this_placement["NAME"];
			$placements[$placement_count]->DEPLOYMENT = $this_placement["DEPLOYMENT"];
			$placements[$placement_count]->LOCATION = $this_placement["LOCATION"];
			$placements[$placement_count]->TIMEFRAME_BEGIN = $this_placement["TIMEFRAME_BEGIN"];
			$placements[$placement_count]->TIMEFRAME_END = $this_placement["TIMEFRAME_END"];
			$placements[$placement_count]->PLACES_MIN = $this_placement["PLACES_MIN"];
			$placements[$placement_count]->PLACES_MAX = $this_placement["PLACES_MAX"];
			$placement_count++;
		}
		return $placements;
	}
	else
	{
		return false;
	}
}

function insert_new_placement($placement_id, $essential, $name, $deployment, $location, $begin, $end, $min, $max)
{
	$placements = fetch_placements($placement_id);
	$this_id = count_highest_ID($placements) + 1;

	$placements[$this_id] = new db_placements;
	$placements[$this_id]->ID = $this_id;
	$placements[$this_id]->ESSENTIAL = $essential;
	$placements[$this_id]->NAME = $name;
	$placements[$this_id]->DEPLOYMENT = $deployment;
	$placements[$this_id]->LOCATION = $location;
	$placements[$this_id]->TIMEFRAME_BEGIN = $begin;
	$placements[$this_id]->TIMEFRAME_END = $end;
	$placements[$this_id]->PLACES_MIN = $min;
	$placements[$this_id]->PLACES_MAX = $max;
	file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'placement_' . $placement_id . '.json', json_encode($placements));

	return true;
}

function fetch_placement_item($placement_id, $item)
{
	$deployments = array();
	$placements = fetch_placements($placement_id);
	$deployment_count = 0;
	foreach($placements as $placement)
	{
		$in_database = false;
		foreach($deployments as $deployment)
		{
			if($placement->$item == $deployment)
			{
				$in_database = true;
			}
		}
		if($in_database == false)
		{
			$deployments[$deployment_count] = $placement->$item;
			$deployment_count++;
		}
	}
	return $deployments;
}

function fetch_placement_by_id($placement_id, $clinic_id)
{
	$deployments = array();
	$placements = fetch_placements($placement_id);
	$deployment_count = 0;
	foreach($placements as $placement)
	{
		if($placement->ID == $clinic_id)
		{
			return $placement;
		}
	}
}

// Wishes

function fetch_user_wishes($student_id, $placement_id)
{
	$enrolled = false;
	$wishlist_table = fetch_json_table('wishlist_' . $placement_id . '.json');
	if(!($wishlist_table === false))
	{
		foreach($wishlist_table as $wishlist)
		{
			if($student_id == $wishlist["ID"])
			{
				$enrolled = true;
				$this_wishlist = new db_wishlist;
				$this_wishlist->DEPLOYMENTS = $wishlist["DEPLOYMENTS"];
				$this_wishlist->TIMEFRAMES_UNAVAILABLE = $wishlist["TIMEFRAMES_UNAVAILABLE"];
				$this_wishlist->CUSTOM_TIMEFRAME_UNAVAILABLE = $wishlist["CUSTOM_TIMEFRAME_UNAVAILABLE"];
			}
		}
		if($enrolled === true)
		{
			return $this_wishlist;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function insert_new_wishes($student_id, $placement_id, $deployments)
{
	$already_exists = false;
	$wishlist_table = fetch_json_table(get_DB_PATH() . DIRECTORY_SEPARATOR . 'wishlist_' . $placement_id . '.json');
	if($wishlist_table === false)
	{
		$new_key = 1;
	}
	else
	{
		$new_key = count($wishlist_table) + 1;
		foreach($wishlist_table as $wishlist)
		{
			if($student_id == $wishlist["ID"])
			{
				$already_exists = true;
			}
		}
	}
	if($already_exists === false)
	{
		// use default values to create a pseudo-wishlist
		$priority_types = array(
			"1",
			"2",
			"3",
		);
		$deployments_insert = array();
		foreach($deployments as $deployment)
		{
			$deployments_insert[$deployment] = new stdClass();
			foreach($priority_types as $priority)
			{
				$deployments_insert[$deployment]->$priority = "";
			}
		}
		$wishlist_table[$new_key] = new db_wishlist;
		$wishlist_table[$new_key]->ID = $student_id;
		$wishlist_table[$new_key]->DEPLOYMENTS = $deployments_insert;
		file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'wishlist_' . $placement_id . '.json', json_encode($wishlist_table));

		return true;
	}
	else
	{
		return false;
	}
}


function insert_wishes($student_id, $placement_id, $deployments, $custom_timeframe_unavailable, $timeframes_unavailable)
{
	$found = false;
	$wishlist_table = fetch_json_table(get_DB_PATH() . DIRECTORY_SEPARATOR . 'wishlist_' . $placement_id . '.json');
	$new_wishlist_table = array();
	$new_wishlist_table_count = 0;
	foreach($wishlist_table as $wishlist)
	{
		$new_wishlist_table[$new_wishlist_table_count] = new db_wishlist;
		$new_wishlist_table[$new_wishlist_table_count]->ID = $wishlist["ID"];
		if($student_id == $wishlist["ID"])
		{
			$found = true;
			$new_wishlist_table[$new_wishlist_table_count]->DEPLOYMENTS = $deployments;
			$new_wishlist_table[$new_wishlist_table_count]->CUSTOM_TIMEFRAME_UNAVAILABLE = $custom_timeframe_unavailable;
			$new_wishlist_table[$new_wishlist_table_count]->TIMEFRAMES_UNAVAILABLE = $timeframes_unavailable;
		}
		else
		{
			$new_wishlist_table[$new_wishlist_table_count]->DEPLOYMENTS = $wishlist["DEPLOYMENTS"];
			$new_wishlist_table[$new_wishlist_table_count]->CUSTOM_TIMEFRAME_UNAVAILABLE = $wishlist["CUSTOM_TIMEFRAME_UNAVAILABLE"];
			$new_wishlist_table[$new_wishlist_table_count]->TIMEFRAMES_UNAVAILABLE = $wishlist["TIMEFRAMES_UNAVAILABLE"];
		}
		$new_wishlist_table_count++;
	}
	if($found)
	{
		return (file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'wishlist_' . $placement_id . '.json', json_encode($new_wishlist_table)));
	}
	else
	{
		return false;
	}
}

// Calculation functions

function insert_calculation_file($placement_id, $filename, $array)
{
	if(file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'calculation_' . $placement_id . DIRECTORY_SEPARATOR . $filename . '.json', json_encode($array)))
	{
		return true;
	}
}

// Security checks

function check_special_characters($string, $excludes = array())
{
	if(is_array($excludes) && !empty($excludes))
	{
		foreach($excludes as $exclude)
		{
			$string = str_replace($exclude, '', $string);
		}
	}
	if(preg_match('/[^a-z0-9 ]+/i', $string))
	{
		return true;
	}
	return false;
}

function check_array_special_chars($array)
{
	foreach($array as $item)
	{
		if(check_special_characters($item, get_PLACEMENT_SPECIAL_CHARS()))
		{
			return false;
		}
	}
	return true;
}

function check_post_special_chars($input)
{
	if(!check_special_characters($input, get_PLACEMENT_SPECIAL_CHARS()))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// pw reset functions
function identifier_matches($identfier)
{
	$students = fetch_students();
	foreach($students as $student)
	{
		if($student->LOGIN == $identfier || $student->EMAIL == $identfier)
		{
			return $student->ID;
		}
	}
	return false;
}

function fetch_reset_password_codes()
{
	$pw_resets_table = fetch_json_table('pw_resets.json');
	if(!($pw_resets_table))
	{
		return false;
	}
	else
	{
		$pw_reset_codes = array();
		foreach($pw_resets_table as $this_pw_reset)
		{
			$pw_reset_codes[$this_pw_reset["ID"]] = new pw_reset_table;
			$pw_reset_codes[$this_pw_reset["ID"]]->ID = $this_pw_reset["ID"];
			$pw_reset_codes[$this_pw_reset["ID"]]->CODE = $this_pw_reset["CODE"];
			$pw_reset_codes[$this_pw_reset["ID"]]->TIMESTAMP = $this_pw_reset["TIMESTAMP"];
		}
		return $pw_reset_codes;
	}
}

function insert_reset_password_code($user_id, $random_string)
{
	$pw_resets_table = fetch_reset_password_codes();
	if(!($pw_resets_table))
	{
		$old_table = array();
	}
	else
	{
		$old_table = $pw_resets_table;
	}
	$new_table = $old_table;
	$new_table[$user_id] = new pw_reset_table;
	$new_table[$user_id]->ID = $user_id;
	$new_table[$user_id]->CODE = $random_string;
	$new_table[$user_id]->TIMESTAMP = time();
	return (file_put_contents(get_DB_PATH() . DIRECTORY_SEPARATOR . 'pw_resets.json', json_encode($new_table)));
}

?>