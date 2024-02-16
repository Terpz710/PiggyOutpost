<?php

declare(strict_types=1);

namespace Terpz710\PiggyOutpost;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

use Terpz710\PiggyOutpost\Task\OutpostTask;

class Outpost extends PluginBase
{
    use SingletonTrait;

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();

        $this->getScheduler()->scheduleRepeatingTask(new OutpostTask(), 20);
    }
}