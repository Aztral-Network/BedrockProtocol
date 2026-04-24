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

use pocketmine\network\mcpe\protocol\ProtocolInfo;

final class LegacyProtocolHelper{

	private function __construct(){
		//NOOP
	}

	public static function isLegacy(int $protocolId) : bool{
		return $protocolId < ProtocolInfo::PROTOCOL_1_20_0;
	}

	public static function isSupported(int $protocolId) : bool{
		return in_array($protocolId, ProtocolInfo::ACCEPTED_PROTOCOL, true);
	}

	public static function getVersionString(int $protocolId) : string{
		return match($protocolId){
			ProtocolInfo::PROTOCOL_1_16_100 => '1.16.100',
			ProtocolInfo::PROTOCOL_1_18_12 => '1.18.12',
			ProtocolInfo::PROTOCOL_1_20_0 => '1.20.0',
			ProtocolInfo::PROTOCOL_1_20_10 => '1.20.10',
			ProtocolInfo::PROTOCOL_1_20_30 => '1.20.30',
			ProtocolInfo::PROTOCOL_1_20_40 => '1.20.40',
			ProtocolInfo::PROTOCOL_1_20_50 => '1.20.50',
			ProtocolInfo::PROTOCOL_1_20_60 => '1.20.60',
			ProtocolInfo::PROTOCOL_1_20_70 => '1.20.70',
			ProtocolInfo::PROTOCOL_1_20_80 => '1.20.80',
			ProtocolInfo::PROTOCOL_1_21_0 => '1.21.0',
			ProtocolInfo::PROTOCOL_1_21_2 => '1.21.2',
			ProtocolInfo::PROTOCOL_1_21_20 => '1.21.20',
			ProtocolInfo::PROTOCOL_1_21_30 => '1.21.30',
			ProtocolInfo::PROTOCOL_1_21_40 => '1.21.40',
			ProtocolInfo::PROTOCOL_1_21_50 => '1.21.50',
			ProtocolInfo::PROTOCOL_1_21_60 => '1.21.60',
			ProtocolInfo::PROTOCOL_1_21_70 => '1.21.70',
			ProtocolInfo::PROTOCOL_1_21_80 => '1.21.80',
			ProtocolInfo::PROTOCOL_1_21_90 => '1.21.90',
			ProtocolInfo::PROTOCOL_1_21_93 => '1.21.93',
			ProtocolInfo::PROTOCOL_1_21_100 => '1.21.100',
			ProtocolInfo::PROTOCOL_1_21_111 => '1.21.111',
			ProtocolInfo::PROTOCOL_1_21_120 => '1.21.120',
			ProtocolInfo::PROTOCOL_1_21_124 => '1.21.124',
			ProtocolInfo::PROTOCOL_1_21_130 => '1.21.130',
			ProtocolInfo::PROTOCOL_1_26_0 => '1.26.0',
			ProtocolInfo::PROTOCOL_1_26_10 => '1.26.10',
			default => "unknown($protocolId)",
		};
	}

	public static function getMinimumSupportedProtocol() : int{
		return ProtocolInfo::PROTOCOL_1_16_100;
	}

	public static function supportsInventoryTransactions(int $protocolId) : bool{
		return $protocolId >= ProtocolInfo::PROTOCOL_1_20_0;
	}

	public static function supportsItemComponents(int $protocolId) : bool{
		return $protocolId >= ProtocolInfo::PROTOCOL_1_21_50;
	}

	public static function supportsTypedRefinements(int $protocolId) : bool{
		return $protocolId >= ProtocolInfo::PROTOCOL_1_21_20;
	}
}