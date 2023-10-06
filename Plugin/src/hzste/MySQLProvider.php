<?php

declare(strict_types = 1);

namespace hzste;

use mysqli;

final class MySQLProvider {

	const DATABASE = "DB_NAME";
	private $database;

	public function __construct() {
		$this->database = new mysqli("127.0.0.1", "root", "root", self::DATABASE);
		$this->init();
	}

	public function init(): void {
		$this->database->query("CREATE TABLE IF NOT EXISTS players (username VARCHAR(32) PRIMARY KEY NOT NULL, discordId VARCHAR(32), discordCode VARCHAR(5));");
    }

	public function getMainDatabaseName(): string {
		return self::DATABASE;
	}

	public function getDatabase(): mysqli {
		return $this->database;
	}
}