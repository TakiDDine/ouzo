<?php

namespace Ouzo\Utilities\Iterator;


class FluentIterator extends ForwardingIterator
{
    /**
     * Returns a fluent iterator that wraps $iterator
     * @param $iterator
     * @return FluentIterator
     */
    public static function from($iterator)
    {
        return new self($iterator);
    }

    /**
     * Returns a fluent iterator for $array
     * @param array $array
     * @return FluentIterator
     */
    public static function fromArray($array)
    {
        return new self(Iterators::forArray($array));
    }

    /**
     * Returns a fluent iterator that uses $function to generate elements
     * $function takes one argument which is the current position of the iterator.
     * @param $generator
     * @return FluentIterator
     */
    public static function fromGenerator($generator)
    {
        return new self(Iterators::generate($generator));
    }

    /**
     * Returns a fluent iterator that applies function to each element of this fluent iterator.
     * @param $function
     * @return $this
     */
    public function map($function)
    {
        $this->iterator = Iterators::map($this->iterator, $function);
        return $this;
    }

    /**
     * Returns the current element or defaultValue if the current position is not valid.
     * @param $default
     * @return mixed
     */
    public function currentOr($default)
    {
        return Iterators::currentOr($this->iterator, $default);
    }

    /**
     * Returns a fluent iterator returning the first $number elements of of this fluent iterator.
     * @param $number
     * @return $this
     */
    public function limit($number)
    {
        $this->iterator = Iterators::limit($this->iterator, $number);
        return $this;
    }

    /**
     * Returns a fluent iterator returning all but first $number elements of this fluent iterator.
     * @param $number
     * @return $this
     */
    public function skip($number)
    {
        $this->iterator = Iterators::skip($this->iterator, $number);
        return $this;
    }

    /**
     * Returns a fluent iterator returning elements of this fluent iterator grouped in chunks of $chunkSize
     * Returns the
     * @param $chunkSize
     * @return $this
     */
    public function batch($chunkSize)
    {
        $this->iterator = Iterators::batch($this->iterator, $chunkSize);
        return $this;
    }

    /**
     * Returns a fluent iterator that cycles indefinitely over the elements of this fluent iterator.
     * @return $this
     */
    public function cycle()
    {
        $this->iterator = Iterators::cycle($this->iterator);
        return $this;
    }

    /**
     * Returns an iterator that indexes elements numerically starting from 0
     * @return $this
     */
    public function reindex()
    {
        $this->iterator = Iterators::reindex($this->iterator);
        return $this;
    }

    /**
     * Returns a fluent iterator returning elements of this fluent iterator that satisfy a predicate.
     * @param $predicate
     * @return $this
     */
    public function filter($predicate)
    {
        $this->iterator = Iterators::filter($this->iterator, $predicate);
        return $this;
    }

    /**
     * Copies elements of this fluent iterator into an array.
     * @return array
     */
    public function toArray()
    {
        return Iterators::toArray($this->iterator);
    }
}