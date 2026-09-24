<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\block\tile;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

/**
 * Bees are not implemented yet, so occupant data is kept as-is to avoid losing it when the world is saved.
 */
class Beehive extends Tile{
	private const TAG_OCCUPANTS = "Occupants"; //TAG_List<TAG_Compound>
	private const TAG_SHOULD_SPAWN_BEES = "ShouldSpawnBees"; //TAG_Byte

	private ?ListTag $occupants = null;
	private bool $shouldSpawnBees = false;

	public function readSaveData(CompoundTag $nbt) : void{
		$occupants = $nbt->getTag(self::TAG_OCCUPANTS);
		$this->occupants = $occupants instanceof ListTag ? clone $occupants : null;
		$this->shouldSpawnBees = $nbt->getByte(self::TAG_SHOULD_SPAWN_BEES, 0) !== 0;
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		if($this->occupants !== null){
			$nbt->setTag(self::TAG_OCCUPANTS, clone $this->occupants);
		}
		$nbt->setTag(self::TAG_SHOULD_SPAWN_BEES, new ByteTag($this->shouldSpawnBees ? 1 : 0));
	}
}
