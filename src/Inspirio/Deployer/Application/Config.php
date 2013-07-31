<?php
namespace Inspirio\Deployer\Application;


class Config implements \ArrayAccess {

	/**
	 * @var array
	 */
	private $data = array();

	/**
	 * Constructor.
	 *
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		foreach ($data as $key => $item) {
			if (is_array($item)) {
				$this->data[$key] = new self($item);
			} else {
				$this->data[$key] = $item;
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset)
	{
		$offset = explode('.', $offset);
		$local  = array_shift($offset);

		if (!array_key_exists($local, $this->data)) {
			return false;
		}

        $data = $this->data[$local];

		if (count($offset) === 0) {
			return true;
		}

		if (! $data instanceof self) {
			return false;
		}

		return $data->offsetExists(implode('.', $offset));
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset)
	{
		$offset = explode('.', $offset);
		$local  = array_shift($offset);

		if (!array_key_exists($local, $this->data)) {
			return null;
		}

		$data = $this->data[$local];

		if (count($offset) === 0) {
			return $data;
		}

		if (! $data instanceof self) {
			return null;
		}

		return $data->offsetGet(implode('.', $offset));
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value)
	{
		$offset = explode('.', $offset);
		$local  = array_shift($offset);

        if (is_array($value)) {
            $value = new self($value);
        }

		if (count($offset) === 0) {
			$this->data[$local] = $value;
			return;
		}

        if (!isset($this->data[$local])) {
            $this->data[$local] = new self();
        }

        $this->data[$local][implode('.', $offset)] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset)
	{
        $offset = explode('.', $offset);
        $local  = array_shift($offset);

        if (!array_key_exists($local, $this->data)) {
            return;
        }

        if (count($offset) === 0) {
            unset($this->data[$local]);
            return;
        }

        $data = $this->data[$local];

        if (! $data instanceof self) {
            return;
        }

        unset($data[implode('.', $offset)]);
	}
}
