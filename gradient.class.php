<?php
class Gradient implements ArrayAccess, SeekableIterator
{
    private $_items = array();
    private $_posIndex = array();
    private $_iSet = 0;
    private $_iGet = 0;
    private $_count = 0;
    private $_orderedItems;
    private $_getCache;
    private $_range = array(0, 100);
    public function __construct($array = null)
    {
        if ($array !== null) {
            foreach ($array as $k => $v)
                $this[$k] = $v;
        }
        $this->_Flush();
    }
    public function SetRange($min, $max)
    {
        foreach ($this->_items as $k => $v) {
            unset($this->_posIndex[$v['pos']]);
            $this->_items[$k]['pos'] = self::_Rescale($v['pos'], $this->_range[0], $this->_range[1], $min, $max);
            $this->_posIndex[$this->_items[$k]['pos']] = $k;
        }
        $this->_range = array(
            $min,
            $max
        );
    }
    public function rewind()
    {
        $this->_iGet = 0;
    }
    public function current()
    {
        if ($this->offsetExists($this->_iGet))
            return $this->offsetGet($this->_iGet);
        else
            return false;
    }
    public function key()
    {
        return $this->_iGet;
    }
    public function next()
    {
        $this->_iGet++;
        return $this->current();
    }
    public function valid()
    {
        return $this->offsetExists($this->_iGet);
    }
    public function seek($index)
    {
        $index = self::_Rescale($index, $this->_range[0], $this->_range[1], 0, 100);
        $this->_iGet = $index;
        if (!$this->valid())
            throw new OutOfBoundsException('Invalid seek position');
    }
    public function offsetExists($offset)
    {
        return $this->_count > 0 && $offset >= $this->_range[0] && $offset <= $this->_range[1];
    }
    public function offsetGet($offset)
    {
        $val = null;
        if (isset($this->_posIndex[$offset]))
            $val = $this->_items[$this->_posIndex[$offset]]['val'];
        elseif (isset($this->_getCache["$offset"]))
            $val = $this->_getCache["$offset"];
        else {
            $this->_Order();
            $first = $this->_orderedItems[0];
            $last = end($this->_orderedItems);
            if ($offset <= $first['pos'])
                $val = $first['val'];
            elseif ($offset >= $last['pos'])
                $val = $last['val'];
            else
                for ($i = 1, $prev = $first; $i < $this->_count && $val === null; $i++, $prev = $next) {
                    $next = $this->_orderedItems[$i];
                    if ($offset >= $prev['pos'] && $offset <= $next['pos'])
                        $val = $this->_Rescale($offset, $prev['pos'], $next['pos'], $prev['val'], $next['val']);
                }
            $this->_getCache["$offset"] = $val;
        }
        return $val;
    }
    public function offsetSet($offset, $value)
    {
        if ($offset < 0 || $offset > 100)
            throw new OutOfBoundsException("Offset $offset is 
            outside of valid range: 0-100.");
        if (isset($this->_posIndex[$offset])) {
            $id = $this->_posIndex[$offset];
            $this->_items[$id] = array(
                'val' => $value,
                'pos' => $offset
            );
        } else {
            $this->_Flush();
            $this->_items[$this->_iSet] = array(
                'val' => $value,
                'pos' => $offset
            );
            $this->_posIndex[$offset] = $this->_iSet;
            $this->_iSet++;
            $this->_count++;
        }
    }
    public function offsetUnset($offset)
    {
        if (isset($this->_posIndex[$pos])) {
            $this->_Flush();
            unset($this->_items[$this->_posIndex[$pos]]);
            unset($this->_posIndex[$pos]);
            $this->_count--;
        }
    }
    public function count()
    {
        return $this->_count;
    }
    private function _Flush()
    {
        $this->_orderedItems = null;
        $this->_getCache = array();
    }
    private function _Order()
    {
        if ($this->_orderedItems === null) {
            $this->_orderedItems = array();
            foreach ($this->_items as $k => $v)
                $this->_orderedItems[] = &$this->_items[$k];
            usort(
                $this->_orderedItems,
                function ($a, $b) {
                    return $a['pos'] - $b['pos'];
                }
            );
        }
    }
    protected static function _Rescale($n, $a, $b, $c, $d)
    {
        return ($n - $a) / ($b - $a) * ($d - $c) + $c;
    }
}
?>