<?php

declare(strict_types=1);

namespace Terpz710\PiggyOutpost\Task;

use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\world\World;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use Terpz710\PiggyOutpost\Outpost;

class OutpostTask extends Task
{
    private PiggyFactions $piggyFactions;
    private ?Faction $faction;
    private Config $config;
    private int $timeWin;
    private int $time;

    public function __construct()
    {
        $this->piggyFactions = PiggyFactions::getInstance();
        $this->config = Outpost::getInstance()->getConfig();
        $this->timeWin = $this->config->get("timeWin");
        $this->time = $this->config->get("time");
        $this->faction = null;
    }

    public function onRun(): void
    {
        $world = $this->getCurrentWorld();

        if ($world === null) {
            return;
        }

        $factions = $this->getFactionInArea();
        $factionCount = count($factions);
        if (!(empty($factions)) and ($factionCount === 1)) {
            $factionName = is_null($this->faction) ? "" : $this->faction->getName();
            if (reset($factions)->getName() === $factionName) {
                if ($this->timeWin !== 0) {
                    $this->timeWin--;
                    $this->getTime($minutes, $seconds, true);
                    $this->sendPopupInArea("You still have $minutes minutes and $seconds seconds before your next reward!");
                } else {
                    $this->giveReward($money, $power);
                    $this->sendPopupInArea("You received {$money} dollars and $power power!");
                    $this->timeWin = $this->config->get("timeWin");
                    $this->time = $this->config->get("time");
                }
            } else {
                if ($this->time !== 0) {
                    $this->time--;
                    $this->getTime($minutes, $seconds);
                    $this->sendPopupInArea("You still have $minutes minute and $seconds seconds before capturing the area!", false);
                } else {
                    $this->faction = reset($factions);
                    $this->sendPopupInArea('You have captured the area!');
                }
            }
        } else {
            $this->sendPopupInArea("Too much or too little faction in the arena!", false);
            $this->timeWin = $this->config->get("timeWin");
            $this->time = $this->config->get("time");
        }
    }

    private function getTime(&$min, &$sec, bool $win = false): void
    {
        $time = $win ? $this->timeWin : $this->time;
        [$min, $sec] = [floor($time / 60), $time % 60];
    }

    public function giveReward(&$money, &$power): void
    {
        $money = $this->config->get("money");
        $power = $this->config->get("power");
        $this->faction->addMoney($money);
        Server::getInstance()->getCommandMap()->dispatch(
            new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()),
            "f powerboost faction {$this->faction->getName()} $power"
        );
    }

    private function sendPopupInArea(string $message, $verification = true): void
    {
        $players = $this->getPlayersInArea();
        foreach ($players as $player) {
            if ($verification) {
                if ($this->getPlayerFaction($player) === $this->faction) {
                    $player->sendPopup($message);
                }
            } else {
                $player->sendPopup($message);
            }
        }
    }

    /** @return Faction[] */
    private function getFactionInArea(): array
    {
        $players = $this->getPlayersInArea();
        foreach ($players as $player) {
            if (is_null($this->getPlayerFaction($player))) {
                unset($players[array_search($player, $players)]);
            }
        }
        return array_unique(array_map([$this, 'getPlayerFaction'], $players));
    }

    private function getPlayerFaction(Player $player): ?Faction
    {
        $currentPlayer = $this->piggyFactions->getPlayerManager()->getPlayer($player);
        return $currentPlayer ? $currentPlayer->getFaction() : null;
    }

    private function isInArea(Player $player): bool
    {
        $pos1 = $this->config->get("area")[0];
        $pos2 = $this->config->get("area")[1];

        $world = $this->getCurrentWorld();

        if ($world === null) {
            return false;
        }

        $chunkX1 = $pos1[0] >> 4;
        $chunkZ1 = $pos1[2] >> 4;
        $chunkX2 = $pos2[0] >> 4;
        $chunkZ2 = $pos2[2] >> 4;

        $world->loadChunk($chunkX1, $chunkZ1);
        $world->loadChunk($chunkX2, $chunkZ2);

        $x_in_range = $player->getPosition()->x >= min($pos1[0], $pos2[0]) && $player->getPosition()->x <= max($pos1[0], $pos2[0]);
        $y_in_range = $player->getPosition()->y >= min($pos1[1], $pos2[1]) && $player->getPosition()->y <= max($pos1[1], $pos2[1]);
        $z_in_range = $player->getPosition()->z >= min($pos1[2], $pos2[2]) && $player->getPosition()->z <= max($pos1[2], $pos2[2]);

        return $x_in_range && $y_in_range && $z_in_range;
    }

    /** @return Player[] * */
    private function getPlayersInArea(): array
    {
        $world = $this->getCurrentWorld();
        
        if ($world === null) {
            return [];
        }

        return array_filter($world->getPlayers(), [$this, 'isInArea']);
    }

    private function getCurrentWorld(): ?World
    {
        $worldName = $this->config->get("world");
        $world = Server::getInstance()->getWorldManager()->getWorldByName($worldName);

        if ($world === null) {
            Server::getInstance()->getLogger()->warning("The world {$worldName} was not found! Make sure it exists/defined correctly within the config.yml!");
        }

        return $world;
    }
}
