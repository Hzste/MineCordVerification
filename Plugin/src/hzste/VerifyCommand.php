<?php

declare(strict_types = 1);

namespace hzste;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class VerifyCommand extends Command {

    public function __construct(){
        parent::__construct("verify", "Verify yourself on discord.");
        $this->setPermission("verify.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof Player){
			$sender->sendMessage("You can only run this command in game.");
			return;
		}
        $name = $sender->getName();
        $stmt = Main::getInstance()->getMySQLProvider()->getDatabase()->prepare("SELECT discordCode FROM players WHERE username = ?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$stmt->bind_result($code);
		$stmt->fetch();
		$stmt->close();
        $sender->sendMessage("You're verification code is: " . $code);
    }
}