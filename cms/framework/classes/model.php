<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

use Event;
use Arr;

class Model extends \Orm\Model {

    protected static $_has_many = array();

	public $media;
	public $wysiwyg;

    public static function _init() {
        Event::trigger(get_called_class().'.properties', get_called_class());

		static::$_has_many['wysiwygs'] = array(
			'key_from' => static::$_primary_key[0],
			'model_to' => 'Cms\Model_Wysiwyg',
			'key_to' => 'wysiwyg_foreign_id',
            'cascade_save' => true,
			'cascade_delete' => false,
			'conditions'     => array(
				'where' => array(
					array('wysiwyg_join_table', '=', \DB::expr('"'.static::$_table_name.'"') ),
				),
			),
		);

		static::$_has_many['medias'] = array(
			'key_from' => static::$_primary_key[0],
			'model_to' => 'Cms\Model_Media_Link',
			'key_to' => 'medil_foreign_id',
			'cascade_save' => true,
			'cascade_delete' => false,
			'conditions'     => array(
				'where' => array(
					array('medil_from_table', '=', \DB::expr('"'.static::$_table_name.'"') ),
				),
			),
		);
    }

	public function __construct() {
		$this->media   = new Model_Media_Provider($this);
		$this->wysiwyg = new Model_Wysiwyg_Provider($this);
		call_user_func_array('parent::__construct', func_get_args());
	}

    public static function add_properties($properties) {
        static::$_properties = Arr::merge(static::$_properties, $properties);
    }

    /**
     * Method for use with Fieldset::add_model()
     *
     * @param   Fieldset     Fieldset instance to add fields to
     * @param   array|Model  Model instance or array for use to repopulate
     */
    public static function build_fieldset($form, $instance = null)
    {
        //Observer_Validation::set_fields($instance instanceof static ? $instance : get_called_class(), $form);
        //$instance and $form->repopulate($instance);
    }

    public function __set($name, $value)
	{
        $arr_name = explode('->', $name);

        if (count($arr_name) > 1)
		{
            if ($arr_name[0] == 'wysiwyg')
			{
                $key = $arr_name[1];
                $w_keys = array_keys($this->wysiwygs);
                for ($j = 0; $j < count($this->wysiwygs); $j++)
				{
                    $i = $w_keys[$j];
                    if ($this->wysiwygs[$i]->wysiwyg_key == $key)
					{
                        array_splice ($arr_name, 0, 2);
						if (empty($arr_name))
						{
							return $this->wysiwygs[$i];
						}
                        return $this->wysiwygs[$i]->{implode('->', $arr_name)} = $value;
                    }
                }
				// Create a new relation if it doesn't exist yet
                $wysiwyg                        = new Model_Wysiwyg();
                $wysiwyg->wysiwyg_text          = $value;
                $wysiwyg->wysiwyg_join_table    = static::$_table_name;
                $wysiwyg->wysiwyg_key           = $key;
                $wysiwyg->wysiwyg_foreign_id    = $this->id;
				// Don't save the link here, it's done with cascade_save = true
                //$wysiwyg->save();
                $this->wysiwygs[] = $wysiwyg;

				return $value;
            }

            if ($arr_name[0] == 'media')
			{
                $key = $arr_name[1];
                $w_keys = array_keys($this->medias);
                for ($j = 0; $j < count($this->medias); $j++)
				{
                    $i = $w_keys[$j];
                    if ($this->medias[$i]->medil_key == $key)
					{
                        array_splice ($arr_name, 0, 2);
						if (empty($arr_name))
						{
							return $this->medias[$i];
						}
                        return $this->medias[$i]->{implode('->', $arr_name)} = $value;
                    }
                }

				// Create a new relation if it doesn't exist yet
				$medil                   = new Model_Media_Link();
				$medil->medil_from_table = static::$_table_name;
				$medil->medil_key        = $key;
				$medil->medil_foreign_id = $this->id;
				$medil->medil_media_id   = $value;
				// Don't save the link here, it's done with cascade_save = true
				$this->medias[] = $medil;

				return $value;
            }

            $obj = $this;

			// We need to access the relation and not the final object
			// So we don't want to use the provider but the __get({"media->key"}) instead
			//$arr_name[0] = $arr_name[0].'->'.$arr_name[1];
			for ($i = 0; $i < count($arr_name); $i++)
			{
                $obj = &$obj->{$arr_name[$i]};
            }
            return $obj = $value;
        }

		// No special setter for ID: immutable

        return parent::__set($name, $value);
    }

    public function & __get($name)
	{
        $arr_name = explode('->', $name);
        if (count($arr_name) > 1)
		{
            if ($arr_name[0] == 'wysiwyg')
			{
                $key = $arr_name[1];
                $w_keys = array_keys($this->wysiwygs);
                for ($j = 0; $j < count($this->wysiwygs); $j++)
				{
                    $i = $w_keys[$j];
                    if ($this->wysiwygs[$i]->wysiwyg_key == $key)
					{
                        array_splice ($arr_name, 0, 2);
						if (empty($arr_name))
						{
							return $this->wysiwygs[$i];
						}
                        return $this->wysiwygs[$i]->__get(implode('->', $arr_name));
                    }
                }
				$ref = null;
                return $ref;
            }

            if ($arr_name[0] == 'media')
			{
                $key = $arr_name[1];
                $w_keys = array_keys($this->medias);
                for ($j = 0; $j < count($this->medias); $j++) {
                    $i = $w_keys[$j];
                    if ($this->medias[$i]->medil_key == $key) {
                        array_splice ($arr_name, 0, 2);
						if (empty($arr_name))
						{
							return $this->medias[$i];
						}
                        return $this->medias[$i]->__get(implode('->', $arr_name));
                    }
                }
				$ref = null;
                return $ref;
            }

            $obj = $this;
            for ($i = 0; $i < count($arr_name); $i++)
			{
                $obj = $obj->{$arr_name[$i]};
            }
            return $obj;
        }

		// Special getter for ID without prefix
        if ($name == 'id')
		{
            $name = static::$_primary_key[0];
        }

        return parent::__get($name);
    }

	public function __isset($name) {
		try {
			$this->__get($name);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
}


class Model_Media_Provider
{
	protected $parent;

	public function __construct($parent)
	{
		$this->parent = $parent;
	}

	public function & __get($value)
	{
		// Reuse the getter and fetch the media directly
		return $this->parent->{'media->'.$value}->media;
	}

	public function __set($property, $value)
	{
		// Check existence of the media, the ORM will throw an exception anyway upon save if it doesn't exists
		$media_id = (string) ($value instanceof \Cms\Model_Media_Media ? $value->media_id : $value);
		$media = \Cms\Model_Media_Media::find($media_id);
		if (is_null($media))
		{
			$pk = $this->parent->primary_key();
			throw new \Exception("The media with ID $media_id doesn't exists, cannot assign it as \"$property\" for ".\Inflector::denamespace(get_class($this->parent))."(".$this->parent->{$pk[0]}.")");
		}

		// Reuse the getter
		$media_link = $this->parent->{'media->'.$property};

		// Create the new relation if it doesn't exists yet
		if (is_null($media_link))
		{
			$this->parent->{'media->'.$property} = $media_id;
			return;
		}

		// Update an existing relation
		$media_link->medil_media_id = $media_id;

		// Don't save the link here, it's done with cascade_save = true
	}
}



class Model_Wysiwyg_Provider
{
	protected $parent;

	public function __construct($parent)
	{
		$this->parent = $parent;
	}

	public function & __get($value)
	{
		return $this->parent->{'wysiwyg->'.$value}->get('wysiwyg_text');
	}

	public function __set($property, $value)
	{
		$value = (string) ($value instanceof \Cms\Model_Wysiwyg ? $value->wysiwyg_text : $value);

		// Reuse the getter
		$wysiwyg = $this->parent->{'wysiwyg->'.$property};

		// Create the new relation if it doesn't exists yet
		if (is_null($wysiwyg))
		{
			$this->parent->{'wysiwyg->'.$property} = $value;
			return;
		}

		// Update an existing relation
		$wysiwyg->wysiwyg_text = $value;

		// Don't save the link here, it's done with cascade_save = true
	}
}
