# Debug World

This plugin implements a debug world generator like the [debug mode](https://minecraft.wiki/w/Debug_mode) generator from
Java edition.

## Features

- Barrier layer at `y=60` in the whole world
- Block grid at `y=70` starting at `x,z=0,0` forming a grid in the `+x` and `+z` directions
- Contains all the registered block states
- No block updates and (random) ticks in worlds generated using the `DebugWorldGenerator`
- The same world for all seeds
- Difficulty set to peaceful

## Usage

Put the plugin in the `plugins` folder of your server.

You can now create a debug world using any world management plugin, e.g. Worlds or MultiWorld.
When creating a world, set the world generator to `debug` and the debug world will be created.

To make sure that the debug world represents all block states correctly, create a new debug world after each PMMP
update.
See [Issues](#issues) for more info.

### Examples

- Using [Worlds](https://poggit.pmmp.io/p/worlds): `/worlds create <worldname> debug`
- Using [MultiWorld](https://poggit.pmmp.io/p/MultiWorld/2.1.1): `/mw create <worldname> 0 debug`
    - The world is the same for all seeds, therefore the seed `0` is used in this example.

## Issues

- **A debug world is outdated after each PocketMine-MP update**
    - Since there are constantly new blocks implemented in Minecraft and PocketMine, the block states will change on
      each update.
      When the block states change, this will result in an outdated debug world since the chunks containing all blocks
      are already generated.
    - The only way to fix this problem is to remove the old debug world and create a new debug world after each
      PocketMine-MP update.
    - This issue will most likely also occur when adding custom blocks using plugins like Customies.
- **Illegal blocks are invisible**
  Since all blocks are generated from their block states, blocks which consist out of multiple blocks (e.g., beds and
  doors) are split into their separate parts (e.g., door_top and door_bottom).
  But it seemed during testing that the Minecraft Bedrock client is not able to render these "illegal" blocks, causing
  the blocks to be invisible.