<?php

declare(strict_types=1);

namespace Terpz710\PiggyOutpost;

use pocketmine\plugin\PluginBase;

use Terpz710\PiggyOutpost\Task\OutpostTask;

class Outpost extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->getScheduler()->scheduleRepeatingTask(new OutpostTask(), 20);
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}
