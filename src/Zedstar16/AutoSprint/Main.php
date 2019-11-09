<?php

declare(strict_types=1);

namespace Zedstar16\AutoSprint;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener
{

    public $sprinting = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->sprinting = [];
    }

    public function isInSprintMode($player): bool
    {
        return isset($this->sprinting[$player]) ? true : false;
    }

    public function sprint($player)
    {
        if (isset($this->sprinting[$player])) {
            unset($this->sprinting[$player]);
        } else {
            $this->sprinting[$player] = $player;
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() == "autosprint") {
            if (!isset($args[0])) {
                $n = $sender->getName();
                if ($this->isInSprintMode($n)) {
                    $this->sprint($n);
                    $sender->sendMessage("§cAutoSprint disabled");
                } else {
                    $this->sprint($n);
                    $sender->sendMessage("§aAutoSprint enabled");
                }
            } else {
                if ($this->getServer()->getPlayer($args[0]) !== null) {
                    $target = $this->getServer()->getPlayer($args[0])->getName();
                    if ($this->isInSprintMode($target)) {
                        $this->sprint($target);
                        $sender->sendMessage("§cAutoSprint disabled for $target");
                    } else {
                        $this->sprint($target);
                        $sender->sendMessage("§aAutoSprint enabled for $target");
                    }
                } else $sender->sendMessage("§cUser: §f$args[0] §cis not currently online");

            }
        }
        return true;
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $p = $event->getPlayer();
        if (isset($this->sprinting[$p->getName()])) {
            // Checking that the event isn't triggered by fall or head movement
            // Rounded to integer to smooth out buggy sprint toggle when player is no longer walking
            $f = $event->getFrom();
            $t = $event->getTo();
            $tx = (int)$t->getX();
            $tz = (int)$t->getZ();
            $fx = (int)$f->getX();
            $fz = (int)$f->getZ();
            if ($tx !== $fx || $tz !== $fz) {
                if (!$p->isSprinting()) {
                    $p->setSprinting(true);
                }
            }
        }

    }
}
