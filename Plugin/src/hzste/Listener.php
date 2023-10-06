<?php

declare(strict_types = 1);

namespace hzste;

use pocketmine\event\Listener as PMListener;
use pocketmine\event\player\PlayerPreLoginEvent;

class Listener implements PMListener {

    private $main;
	
    public function __construct(Main $main){
        $this->main = $main;
    }
	
	public function onPlayerPreLogin(PlayerPreLoginEvent $event): void {
		$username = $event->getPlayerInfo()->getUsername();
		$discordId = "";
		$discordCode = "";
		// Generate random 5 char discord code
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for ($i = 0; $i < 5; $i++) {
			$discordCode .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		$stmt = $this->main->getMySQLProvider()->getDatabase()->prepare("INSERT IGNORE INTO players (username, discordId, discordCode) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $username, $discordId, $discordCode);
		$stmt->execute();
		$stmt->close();
	}
	
}