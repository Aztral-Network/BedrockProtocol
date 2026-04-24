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

/**
 * PacketSerializer for protocol 1.16.x (protocol version 419)
 * This version uses different UUID format than 1.18+
 */
class PacketSerializer116 extends LegacyBinaryStream{

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
		//1.16 format: Most, Most, Least, Least
		$part1 = $this->getLInt();
		$part0 = $this->getLInt();
		$part3 = $this->getLInt();
		$part2 = $this->getLInt();
		return new Uuid($part0, $part1, $part2, $part3);
	}

	public function putUUID(UuidInterface $uuid) : void{
		$this->putLInt($uuid->getPart(1));
		$this->putLInt($uuid->getPart(0));
		$this->putLInt($uuid->getPart(3));
		$this->putLInt($uuid->getPart(2));
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

	public function getBlockPosition() : array{
		$x = $this->getVarInt();
		$y = $this->getVarInt();
		$z = $this->getVarInt();
		return [$x, $y, $z];
	}

	public function putBlockPosition(int $x, int $y, int $z) : void{
		$this->putVarInt($x);
		$this->putVarInt($y);
		$this->putVarInt($z);
	}

	public function getSignedBlockPosition() : array{
		return $this->getBlockPosition();
	}

	public function putSignedBlockPosition(int $x, int $y, int $z) : void{
		$this->putBlockPosition($x, $y, $z);
	}

	public function getVector3() : array{
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
}