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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer116;
use pocketmine\network\mcpe\protocol\serializer\LegacyBinaryStream;

final class SerializerRegistry{

	private function __construct(){
		//NOOP
	}

	public static function getSerializer(int $protocolId, string $buffer, int $offset = 0) : LegacyBinaryStream{
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_0){
			throw new \InvalidArgumentException("Use ByteBufferReader for modern protocols (1.20+)");
		}
		
		return self::getSerializerInstance($protocolId, $buffer, $offset);
	}

	public static function getEncoder(int $protocolId) : LegacyBinaryStream{
		if($protocolId >= ProtocolInfo::PROTOCOL_1_20_0){
			throw new \InvalidArgumentException("Use ByteBufferWriter for modern protocols (1.20+)");
		}
		
		return self::getSerializerInstance($protocolId);
	}

	private static function getSerializerInstance(int $protocolId, string $buffer = "", int $offset = 0) : LegacyBinaryStream{
		if($protocolId < ProtocolInfo::PROTOCOL_1_18_12){
			return PacketSerializer116::decoder($buffer, $offset);
		}
		return PacketSerializer::decoder($buffer, $offset);
	}

	public static function isLegacyProtocol(int $protocolId) : bool{
		return $protocolId < ProtocolInfo::PROTOCOL_1_20_0;
	}

	public static function isVersion118(int $protocolId) : bool{
		return $protocolId >= ProtocolInfo::PROTOCOL_1_18_12 && $protocolId < ProtocolInfo::PROTOCOL_1_20_0;
	}

	public static function isVersion116(int $protocolId) : bool{
		return $protocolId < ProtocolInfo::PROTOCOL_1_18_12;
	}
}