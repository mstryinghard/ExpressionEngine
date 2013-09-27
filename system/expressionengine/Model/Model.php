<?php
namespace EllisLab\ExpressionEngine\Model;

/**
 * The base Model class
 */
abstract class Model {

	protected static $meta = array();

	/**
	 * The database entity object for the related database table.
	 */
	protected $entities = array();

	/**
	 *
	 */
	protected $related_models = array();

	/**
	 * An array storing the names of modified properties. Used in validation.
	 */
	private $dirty = array();

	/**
	 * Initialize this model with a set of data to set on the entity.
	 *
	 * @param	mixed[]	$data	An array of initial property values to
	 * 						set on this model.  The array indexes must
	 * 						be valid properties on this model's entity.
	 */
	public function __construct(array $data = array())
	{
		foreach (static::getMetaData('entity_names') as $entity_name)
		{
			$entity = QueryBuilder::getQualifiedClassName($entity_name);
			$this->entities[$entity_name] = new $entity($data);
		}
	}

	public static function getMetaData($key = NULL)
	{
		if (empty(static::$meta))
		{
			throw new \UnderflowException('No meta data set for this class!');
		}

		if ( ! isset($key))
		{
			return static::$meta;
		}

		return static::$meta[$key];
	}


	public function oneToOne($model, $entity, $key)
	{
		$has_id = $this->getId() !== NULL;
		$has_data = isset($this->related_models[$model]);

		if ( ! $has_id && ! $has_data)
		{
			$keys = $model::getKeys();

			return array(
				'entity' => $keys[$that_key],
				'key' => $this_key,
				'type' => 'one-to-one'
			);
		}

		if ( $has_id && ! $has_data)
		{
			die('lazy');
			return ee()->query_builder->get($model, $this->{$key})->run();
		}

		if ( $has_data)
		{
			return $this->related_models[$model];
		}

		throw new ModelUndefinedStateException();
	}

	public function manyToOne($to_model_name, $this_key, $that_key)
	{
		$has_id = $this->getId() !== NULL;
		$has_data = isset($this->related_models[$to_model_name]);

		$relationship = new Relationship($this, $to_model_name, 'many-to-one');

		if ( ! $has_id && ! $has_data)
		{
			return $relationship->eagerLoad($this_key, $that_key);
		}

		if ( $has_id && ! $has_data)
		{
			return array();
			die('lazy');
			return ee()->query_builder->get($to_model_name, $this->{$key})->run();
		}

		if ( $has_data)
		{
			return $this->related_models[$to_model_name];
		}

		throw new UndefinedStateException();
	}

	public function oneToMany($to_model_name, $this_key, $that_key)
	{
		$has_id = $this->getId() !== NULL;
		$has_data = isset($this->related_models[$to_model_name]);

		$relationship = new Relationship($this, $to_model_name, 'one-to-many');

		if ( ! $has_id && ! $has_data)
		{
			return $relationship->eagerLoad($this_key, $that_key);
		}


		if ( $has_id && ! $has_data)
		{
			return array();
			die('lazy');
			return ee()->query_builder->get($to_model_name, $this->{$key})->run();
		}

		if ( $has_data)
		{
			return $this->related_models[$to_model_name];
		}

		throw new UndefinedStateException();
	}

	public function manyToMany($model, $entity, $key)
	{}

	public function setRelationship($name, $value)
	{
		$this->related_models[$name] = $value;
	}

	/**
	 * Pass through getter that allows properties to be gotten from this model
	 * but stored in the wrapped entity.
	 *
	 * @param	string	$name	The name of the property to be retrieved.
	 *
	 * @return	mixed	The property being retrieved.
	 *
	 * @throws	NonExistentPropertyException	If the property doesn't exist,
	 * 					an appropriate exception is thrown.
	 */
	public function __get($name)
	{
		$method = 'get' . ucfirst($name);
		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		foreach ($this->entities as $entity)
		{
			if (property_exists($entity, $name))
			{
				return $entity->{$name};
			}
		}


		throw new NonExistentPropertyException('Attempt to access a non-existent property on ' . __CLASS__);
	}

	/**
	 * Pass through setter that allows properties to be set on this model,
	 * but stored in the wrapped entity.
	 *
	 * @param	string	$name	The name of the property being set. Must be
	 * 						a valid property on the wrapped entity.
	 * @param	mixed	$value	The value to set the property to.
	 *
	 * @return	void
	 *
	 * @throws	NonExistentPropertyException	If the property doesn't exist,
	 * 					and appropriate exception is thrown.
	 */
	public function __set($name, $value)
	{
		$method = 'set' . ucfirst($name);
		if (method_exists($this, $method))
		{
			return $this->$method($value);
		}

		foreach($this->entities as $entity)
		{
			if (property_exists($entity, $name))
			{
				$entity->{$name} = $value;
				$entity->dirty[$name] = TRUE;
				return;
			}
		}

		throw new NonExistentPropertyException('Attempt to access a non-existent property on ' . __CLASS__);
	}

	public function getId()
	{
		$primary_key = static::getMetaData('primary_key');
		$key_map = static::getMetaData('key_map');
		$entity_name = $key_map[$primary_key];
		return $this->entities[$entity_name]->{$primary_key};
	}

	/**
	 * Validate this model's data for saving.
	 *
	 * @return	Errors	A class containing the errors resulting from validation.
	 */
	public function validate()
	{
		return new Errors();
	}

	/**
	 * Save this model. Calls validation before saving to ensure that invalid
	 * data doesn't get saved, however, expects validation to have been called
	 * already and the errors handled.  Thus, if validation returns errors,
	 * save will throw an exception.
	 *
	 * @return 	void
	 *
	 * @throws	InvalidDataException	If the model fails to validate, an
	 * 						exception is thrown.  Validation should be called
	 * 						and any errors handled before attempting to save.
	 */
	public function save()
	{
		if ( ! $this->validate())
		{
			throw new ModelException('Model failed to validate on save call!');
		}

		foreach($this->entities as $entity)
		{
			$entity->save();
		}
	}


	/**
	 * Delete this model.
	 *
	 * @return	void
	 */
	public function delete()
	{
		foreach($this->entities as $entity)
		{
			$entity->delete();
		}
	}

	/**
	 * Retrieve the model as an array
	 *
	 * @return Array Merged values of all entities.
	 */
	public function toArray()
	{
		// extract all public vars from our entities and flatten them
		$keys = array_keys(call_user_func_array(
			'array_merge',
			array_map('get_object_vars', $this->entities)
		));

		// Combine the keys with their value as controlled by __get
		// Without array_keys the above gives us our values, but we
		// need to be consistent with any potential getters.
		return array_combine(
			$keys,
			array_map(array($this, '__get'), $keys)
		);
	}
}
