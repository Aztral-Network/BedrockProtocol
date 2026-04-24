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

use RuntimeException;

class BinaryDataException extends RuntimeException{
}

abstract class LegacyBinaryStream{
	protected string $buffer = "";
	protected int $offset = 0;

	public function __construct(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function getBuffer() : string{
		return $this->buffer;
	}

	public function getOffset() : int{
		return $this->offset;
	}

	public function getRemaining() : string{
		return substr($this->buffer, $this->offset);
	}

	protected function get(int $length) : string{
		if(($this->offset + $length) > strlen($this->buffer)){
			throw new BinaryDataException("Not enough bytes available");
		}
		$ret = substr($this->buffer, $this->offset, $length);
		$this->offset += $length;
		return $ret;
	}

	protected function put(string $data) : void{
		$this->buffer .= $data;
	}

	public function getByte() : int{
		return ord($this->get(1));
	}

	public function putByte(int $v) : void{
		$this->buffer .= chr($v);
	}

	public function getBool() : bool{
		return $this->getByte() !== 0;
	}

	public function putBool(bool $v) : void{
		$this->putByte($v ? 1 : 0);
	}

	public function getShort() : int{
		return unpack("s", $this->get(2))[1] ?? 0;
	}

	public function getUnsignedShort() : int{
		return unpack("v", $this->get(2))[1] ?? 0;
	}

	public function putShort(int $v) : void{
		$this->buffer .= pack("s", $v);
	}

	public function putUnsignedShort(int $v) : void{
		$this->buffer .= pack("v", $v);
	}

	public function getInt() : int{
		return unpack("l", $this->get(4))[1] ?? 0;
	}

	public function getUnsignedInt() : int{
		return unpack("V", $this->get(4))[1] ?? 0;
	}

	public function putInt(int $v) : void{
		$this->buffer .= pack("l", $v);
	}

	public function putUnsignedInt(int $v) : void{
		$this->buffer .= pack("V", $v);
	}

	public function getLong() : int{
		return unpack("q", $this->get(8))[1] ?? 0;
	}

	public function getUnsignedLong() : int{
		return unpack("Q", $this->get(8))[1] ?? 0;
	}

	public function putLong(int $v) : void{
		$this->buffer .= pack("q", $v);
	}

	public function putUnsignedLong(int $v) : void{
		$this->buffer .= pack("Q", $v);
	}

	public function getFloat() : float{
		return unpack("f", $this->get(4))[1] ?? 0.0;
	}

	public function putFloat(float $v) : void{
		$this->buffer .= pack("f", $v);
	}

	public function getLLong() : int{
		return unpack("q", $this->get(8))[1] ?? 0;
	}

	public function getLUnsignedLong() : int{
		return unpack("Q", $this->get(8))[1] ?? 0;
	}

	public function putLLong(int $v) : void{
		$this->buffer .= pack("q", $v);
	}

	public function putLUnsignedLong(int $v) : void{
		$this->buffer .= pack("Q", $v);
	}

	public function getLFloat() : float{
		return unpack("f", $this->get(4))[1] ?? 0.0;
	}

	public function putLFloat(float $v) : void{
		$this->buffer .= pack("f", $v);
	}

	public function getVarInt() : int{
		return $this->readVarInt();
	}

	public function putVarInt(int $v) : void{
		$this->writeVarInt($v);
	}

	public function getUnsignedVarInt() : int{
		return $this->readVarInt();
	}

	public function putUnsignedVarInt(int $v) : void{
		$this->writeVarInt($v);
	}

	public function getVarLong() : int{
		return $this->readVarLong();
	}

	public function putVarLong(int $v) : void{
		$this->writeVarLong($v);
	}

	public function getUnsignedVarLong() : int{
		return $this->readVarLong();
	}

	public function putUnsignedVarLong(int $v) : void{
		$this->writeVarLong($v);
	}

	private function readVarInt() : int{
		$result = 0;
		$numRead = 0;
		do{
			$byte = $this->getByte();
			$result |= ($byte & 0x7F) << (7 * $numRead);
			$numRead++;
			if($numRead > 5){
				throw new BinaryDataException("VarInt too large");
			}
		}while(($byte & 0x80) !== 0);
		return $result;
	}

	private function writeVarInt(int $v) : void{
		while(true){
			if(($v & ~0x7F) === 0){
				$this->putByte($v);
				return;
			}
			$this->putByte(($v & 0x7F) | 0x80);
			$v = (($v >> 7) & (PHP_INT_MAX >> 7));
		}
	}

	private function readVarLong() : int{
		$result = 0;
		$numRead = 0;
		do{
			$byte = $this->getByte();
			$result |= ($byte & 0x7F) << (7 * $numRead);
			$numRead++;
			if($numRead > 10){
				throw new BinaryDataException("VarLong too large");
			}
		}while(($byte & 0x80) !== 0);
		return $result;
	}

	private function writeVarLong(int $v) : void{
		while(true){
			if(($v & ~0x7F) === 0){
				$this->putByte($v);
				return;
			}
			$this->putByte(($v & 0x7F) | 0x80);
			$v = (($v >> 7) & (PHP_INT_MAX >> 7));
		}
	}
}