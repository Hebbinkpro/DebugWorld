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

use InvalidArgumentException;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

class DebugWorldGenerator extends Generator
{
    public const BARRIER_FLOOR_HEIGHT = 60;
    public const GRID_HEIGHT = 70;

    private int $gridSize;


    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);

        // get the number of registered block states
        $states = sizeof(RuntimeBlockStateRegistry::getInstance()->getAllKnownStates());

        // all blocks should fit in a size x size grid
        $this->gridSize = (int)ceil(sqrt($states));
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $barrier = VanillaBlocks::BARRIER()->getStateId();

        // get the chunk
        $chunk = $world->getChunk($chunkX, $chunkZ) ?? throw new InvalidArgumentException("Chunk $chunkX $chunkZ does not yet exist");

        $baseX = $chunkX * Chunk::EDGE_LENGTH;
        $baseZ = $chunkZ * Chunk::EDGE_LENGTH;
        for ($x = 0; $x < Chunk::EDGE_LENGTH; ++$x) {
            $absoluteX = $baseX + $x;
            for ($z = 0; $z < Chunk::EDGE_LENGTH; ++$z) {
                $absoluteZ = $baseZ + $z;
                // set the barrier block
                $chunk->setBlockStateId($x, self::BARRIER_FLOOR_HEIGHT, $z, $barrier);

                // get the grid block that should be placed
                $block = $this->getGridBlock($absoluteX, $absoluteZ);
                // set the grid block
                $chunk->setBlockStateId($x, self::GRID_HEIGHT, $z, $block);
            }
        }
    }

    /**
     * Get the grid block that should be on the x,z position in the world
     * @param int $x
     * @param int $z
     * @return int the block state ID of the grid block, returns Air if it is an invalid position
     */
    public function getGridBlock(int $x, int $z): int
    {
        $air = VanillaBlocks::AIR()->getStateId();

        // x,z should be a positive even position
        if ($x < 0 || $z < 0 || $x % 2 != 0 || $z % 2 != 0) return $air;

        // determine the grid position
        $gridX = (int)($x / 2);
        $gridZ = (int)($z / 2);

        // position is outside the grid
        if ($gridX > $this->gridSize || $gridZ > $this->gridSize) return $air;

        // get the block index
        $index = ($gridZ * $this->gridSize) + $gridX;

        // get the block by its index
        $blocks = array_values(RuntimeBlockStateRegistry::getInstance()->getAllKnownStates());
        $block = $blocks[$index] ?? null;
        if ($block === null) return $air;

        // return the state id of the requested block
        return $block->getStateId();
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        // no population
    }
}