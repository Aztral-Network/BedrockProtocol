<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\serializer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function strlen;
use function strrev;
use function substr;

class PacketSerializer extends LegacyBinaryStream{

	public function __construct(string $buffer = "", int $offset = 0){
		parent::__construct($buffer, $offset);
	}

	public static function encoder() : self{
		return new self();
	}

	public static function decoder(string $buffer, int $offset = 0) : self{
		return new self($buffer, $offset);
	}

	public function getString() : string{
		return $this->get($this->getUnsignedVarInt());
	}

	public function putString(string $v) : void{
		$this->putUnsignedVarInt(strlen($v));
		$this->put($v);
	}

	public function getUUID() : UuidInterface{
		$p1 = strrev($this->get(8));
		$p2 = strrev($this->get(8));
		return Uuid::fromBytes($p1 . $p2);
	}

	public function putUUID(UuidInterface $uuid) : void{
		$bytes = $uuid->getBytes();
		$this->put(strrev(substr($bytes, 0, 8)));
		$this->put(strrev(substr($bytes, 8, 8)));
	}

	public function getActorUniqueId() : int{
		return $this->getVarLong();
	}

	public function putActorUniqueId(int $eid) : void{
		$this->putVarLong($eid);
	}

	public function getEntityRuntimeId() : int{
		return $this->getUnsignedVarLong();
	}

	public function putEntityRuntimeId(int $eid) : void{
		$this->putUnsignedVarLong($eid);
	}

	public function getBlockPosition() : array{ // [x, y, z]
		$x = $this->getVarInt();
		$y = $this->getUnsignedVarInt();
		$z = $this->getVarInt();
		return [$x, $y, $z];
	}

	public function putBlockPosition(int $x, int $y, int $z) : void{
		$this->putVarInt($x);
		$this->putUnsignedVarInt($y);
		$this->putVarInt($z);
	}

	public function getSignedBlockPosition() : array{ // [x, y, z]
		$x = $this->getVarInt();
		$y = $this->getVarInt();
		$z = $this->getVarInt();
		return [$x, $y, $z];
	}

	public function putSignedBlockPosition(int $x, int $y, int $z) : void{
		$this->putVarInt($x);
		$this->putVarInt($y);
		$this->putVarInt($z);
	}

	public function getVector3() : array{ // [x, y, z]
		return [
			$this->getLFloat(),
			$this->getLFloat(),
			$this->getLFloat()
		];
	}

	public function putVector3(float $x, float $y, float $z) : void{
		$this->putLFloat($x);
		$this->putLFloat($y);
		$this->putLFloat($z);
	}

	public function getByteRotation() : float{
		return $this->getByte() * (360 / 256);
	}

	public function putByteRotation(float $rotation) : void{
		$this->putByte((int) (($rotation * 256 + 0.5) / 360) % 256);
	}

	public function getGameRules() : array{
		$count = $this->getUnsignedVarInt();
		$rules = [];
		for($i = 0; $i < $count; $i++){
			$name = $this->getString();
			$type = $this->getUnsignedVarInt();
			$rules[$name] = match($type){
				1 => $this->getBool(),
				2 => $this->getUnsignedVarInt(),
				3 => $this->getFloat(),
				default => $this->getString()
			};
		}
		return $rules;
	}

	public function putGameRules(array $rules) : void{
		$this->putUnsignedVarInt(count($rules));
		foreach($rules as $name => $value){
			$this->putString($name);
			$type = match(true){
				is_bool($value) => 1,
				is_int($value) => 2,
				is_float($value) => 3,
				default => 4
			};
			$this->putUnsignedVarInt($type);
			match($type){
				1 => $this->putBool($value),
				2 => $this->putUnsignedVarInt($value),
				3 => $this->putLFloat($value),
				4 => $this->putString($value)
			};
		}
	}
}