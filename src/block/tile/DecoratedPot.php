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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\TypeConverter;

/**
 * Keeps the pot's sherds (and stored item data) so they are displayed to clients and not lost on save.
 */
class DecoratedPot extends Spawnable{
	private const TAG_SHERDS = "sherds"; //TAG_List<TAG_String>
	private const TAG_ITEM = "item"; //TAG_Compound

	/** @var string[] */
	private array $sherds = [];
	private ?CompoundTag $item = null;

	public function readSaveData(CompoundTag $nbt) : void{
		$this->sherds = [];
		$sherds = $nbt->getTag(self::TAG_SHERDS);
		if($sherds instanceof ListTag){
			foreach($sherds as $sherd){
				if($sherd instanceof StringTag){
					$this->sherds[] = $sherd->getValue();
				}
			}
		}
		$item = $nbt->getTag(self::TAG_ITEM);
		$this->item = $item instanceof CompoundTag ? clone $item : null;
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$this->writeSherds($nbt);
		if($this->item !== null){
			$nbt->setTag(self::TAG_ITEM, clone $this->item);
		}
	}

	protected function addAdditionalSpawnData(CompoundTag $nbt, TypeConverter $typeConverter) : void{
		$this->writeSherds($nbt);
	}

	private function writeSherds(CompoundTag $nbt) : void{
		if(count($this->sherds) > 0){
			$nbt->setTag(self::TAG_SHERDS, new ListTag(array_map(fn(string $sherd) => new StringTag($sherd), $this->sherds)));
		}
	}

	/**
	 * @return string[]
	 */
	public function getSherds() : array{ return $this->sherds; }

	/**
	 * @param string[] $sherds
	 */
	public function setSherds(array $sherds) : void{
		$this->sherds = $sherds;
		$this->clearSpawnCompoundCache();
	}
}
