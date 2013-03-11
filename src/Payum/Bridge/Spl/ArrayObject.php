<?php
namespace Payum\Bridge\Spl;

use Payum\Exception\InvalidArgumentException;
use Payum\Exception\LogicException;

class ArrayObject extends \ArrayObject
{
    protected $input;

    /**
     * {@inheritdoc}
     */
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        if ($input instanceof \ArrayAccess && false == $input instanceof \ArrayObject) {
            $this->input = $input;

            if (false ==$input instanceof \Traversable) {
                throw new LogicException('Traversable interface must be implemented in case custom ArrayAccess instance given. It is becase some php limitations.');
            }

            $input = iterator_to_array($input);
        }

        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * @param array|\Traversable $input
     *
     * @throws \Payum\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function replace($input)
    {
        if (false == (is_array($input) || $input instanceof \Traversable)) {
            throw new InvalidArgumentException('Invalid input given. Should be an array or instance of \Traversable');
        }

        foreach ($input as $index => $value) {
            $this[$index] = $value;
        }
    }

    /**
     * Checks that all given keys a present and contains not empty value
     *
     * @param array $indexes
     *
     * @return boolean
     */
    public function offsetsExists(array $indexes)
    {
        foreach ($indexes as $index) {
            if (false == $this[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($index, $value)
    {
        if ($this->input) {
            $this->input[$index] = $value;
        }

        return parent::offsetSet($index, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($index)
    {
        if ($this->input) {
            unset($this->input[$index]);
        }

        return parent::offsetUnset($index);
    }

    /**
     * This simply returns NULL when an array does not have this index.
     * It allows us not to do isset all the time we want to access something.
     *
     * {@inheritdoc}
     */
    public function offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return parent::offsetGet($index);
        }

        return null;
    }

    /**
     * @param mixed $input
     * 
     * @return ArrayObject
     */
    public static function ensureArrayObject($input)
    {
        return $input instanceof static ? $input : new static($input);
    }
}