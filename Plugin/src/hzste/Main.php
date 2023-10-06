<?php

declare(strict_types = 1);

namespace hzste;

use pocketmine\plugin\PluginBase;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

final class Main extends PluginBase {

    public static $instance;
    private $provider;
    private $commandManager;

    public function onEnable(): void {
        self::$instance = $this;
        $this->provider = new MySQLProvider();
        $this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
        DefaultPermissions::registerPermission(new Permission("verify.command"), [PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR)]);
        $this->getServer()->getCommandMap()->register("verify", new VerifyCommand());
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getMySQLProvider(): MySQLProvider {
        return $this->provider;
    }

}
