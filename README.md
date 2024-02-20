<p align="center">
    <a href="https://github.com/Terpz710/PiggyOutpost/blob/main/icon.png"><img src="https://github.com/Terpz710/PiggyOutpost/blob/main/icon.png"></img></a><br>
    <b>PiggyOutpost</b>

# Description

With this plugin you can now use [PiggyFactions](https://github.com/DaPigGuy/PiggyFactions) to add in outpost for players to capture, Players can go against eachother to get money and power!

# Whats an outpost plugin?

This plugin utilizes [PiggyFactions](https://github.com/DaPigGuy/PiggyFactions) to set up outpost in a specific world.

Factions can go against each other to claim the outpost and claim rewards!

In order to claim the outpost your faction must be inside the specifed coordinates and you must wait the duration specifed in the config.yml("time"). After the countdown ends there is another countdown to give the rewards called ("timeWin"). This helps other factions to intervene to cancel the claim and constest the opposing faction.

# How to set up

* First drop the PiggyOutpost.phar into the plugin folder
* Load the server so all the configurations gets loaded
* Stop the server, go into a folder called ("plugin_data") search for a folder called ("PiggyOutpost")
* Edit the config.yml to your prefrence, Coords, World ect
* Reload the server and battle on!

**config.yml**

```
# Created by Terpz710 :p

# Specify where the outpost will be set at
area:
  - [100, 90, 90] # Use a coordinates plugin for better results!
  - [90, 100, 100]

# Specify in which world this will occur
world: world

# Specify the time that will be required for the area to be captured
time: 100

# Specify how long it takes before that factions claims after the first intial time runs out
timeWin: 10

# Rewards section
money: 500
power: 100
```

# Features

* Easy to use config.yml
* Reward system

# Note this requires PiggyFactions to Work

https://github.com/DaPigGuy/PiggyFactions
