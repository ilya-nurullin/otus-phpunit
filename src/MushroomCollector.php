<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;

class MushroomCollector
{
    private $name;
    private $count;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->count = 0;
    }

    public function collect(int $mushroomCount): void
    {
        $this->count += $mushroomCount;
    }

    public function goHome(): string
    {
        $result = $this->prepareResultString();
        $this->count = 0;
        return $result;
    }

    private function prepareResultString(): string
    {
        $result = "{$this->name} принёс домой {$this->count}";
        if ($this->count > 1000) {
            throw new InvalidArgumentException("Слишком много грибов, {$this->name} надорвался");
        }
//        if (intdiv($this->count % 100, 10) === 1) {
//            return "$result грибов";
//        }
        switch($this->count % 10) {
            case 1:
                return "$result гриб";
            case 2:
            case 3:
            case 4:
                return "$result гриба";
        }
        return "$result грибов";
    }

    public function ex($pr): string {
        return $pr->get();
    }
}

class StringPrefix {
    private string $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    public function get(): string {
        return '__prefix__'.$this->str;
    }
}