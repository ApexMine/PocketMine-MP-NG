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

namespace pocketmine\block;

use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\block\utils\HorizontalFacing;
use pocketmine\block\utils\HorizontalFacingTrait;
use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\GlassBottle;
use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Beehive extends Opaque implements HorizontalFacing{
	use FacesOppositePlacingPlayerTrait {
		HorizontalFacingTrait::describeBlockOnlyState as describeFacing;
	}

	public const MIN_HONEY_LEVEL = 0;
	public const MAX_HONEY_LEVEL = 5;

	protected int $honeyLevel = self::MIN_HONEY_LEVEL;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$this->describeFacing($w);
		$w->boundedIntAuto(self::MIN_HONEY_LEVEL, self::MAX_HONEY_LEVEL, $this->honeyLevel);
	}

	public function getHoneyLevel() : int{ return $this->honeyLevel; }

	/** @return $this */
	public function setHoneyLevel(int $honeyLevel) : self{
		if($honeyLevel < self::MIN_HONEY_LEVEL || $honeyLevel > self::MAX_HONEY_LEVEL){
			throw new \InvalidArgumentException("Honey level must be in range " . self::MIN_HONEY_LEVEL . " ... " . self::MAX_HONEY_LEVEL);
		}
		$this->honeyLevel = $honeyLevel;
		return $this;
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		if($this->honeyLevel < self::MAX_HONEY_LEVEL){
			return false;
		}
		if($item instanceof Shears){
			$item->applyDamage(1);
			$this->position->getWorld()->dropItem($this->position->getSide($this->facing)->add(0.5, 0.5, 0.5), VanillaItems::HONEYCOMB()->setCount(3));
		}elseif($item instanceof GlassBottle){
			$item->pop();
			$returnedItems[] = VanillaItems::HONEY_BOTTLE();
		}else{
			return false;
		}
		//TODO: angry bees
		$this->honeyLevel = self::MIN_HONEY_LEVEL;
		$this->position->getWorld()->setBlock($this->position, $this);
		return true;
	}

	public function getFlameEncouragement() : int{
		return 5;
	}

	public function getFlammability() : int{
		return 20;
	}
}
