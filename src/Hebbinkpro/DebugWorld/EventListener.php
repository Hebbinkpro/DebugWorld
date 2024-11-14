<?php
/*
 *   _____       _              __          __        _     _
 *  |  __ \     | |             \ \        / /       | |   | |
 *  | |  | | ___| |__  _   _  __ \ \  /\  / /__  _ __| | __| |
 *  | |  | |/ _ \ '_ \| | | |/ _` \ \/  \/ / _ \| '__| |/ _` |
 *  | |__| |  __/ |_) | |_| | (_| |\  /\  / (_) | |  | | (_| |
 *  |_____/ \___|_.__/ \__,_|\__, | \/  \/ \___/|_|  |_|\__,_|
 *                            __/ |
 *                           |___/
 *
 * Copyright (c) 2024 Hebbinkpro
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

namespace Hebbinkpro\DebugWorld;

use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\Listener;
use pocketmine\event\world\WorldLoadEvent;

class EventListener implements Listener
{
    public function __construct(private DebugWorld $debugWorld)
    {
    }

    public function onWorldLoad(WorldLoadEvent $e): void
    {
        // apply the debug world behavior to the loading world
        $world = $e->getWorld();
        if ($this->debugWorld->isDebugWorld($world)) $this->debugWorld->applyDebugWorldBehavior($world);
    }

    public function onBlockUpdate(BlockUpdateEvent $e): void
    {
        // disable debug world block updates, this disables most block changes
        $world = $e->getBlock()->getPosition()->getWorld();
        if ($this->debugWorld->isDebugWorld($world)) $e->cancel();
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $e): void
    {
        // disable trampling of debug world blocks, not included in block updates
        $world = $e->getBlock()->getPosition()->getWorld();
        if ($this->debugWorld->isDebugWorld($world)) $e->cancel();
    }
}