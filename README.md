# rpg-card-monster-generator
PHP script to convert a tab delimited file into JSON format needed by http://crobi.github.io/rpg-cards/ for D&amp;D 5th edition monster cards.

How to
=
1. Create a tab delimited file card_data.txt make sure it includes the field headers (see Required Fields below) in the first row. Example: see card_data.txt (Includes data for the jester's Deathlock (http://www.enworld.org/forum/5emonsters/showentry.php?e=22) and Huecuva (http://www.enworld.org/forum/5emonsters/showentry.php?e=38).)

2. run 'php JsonGenerator.php'

3. Save the output (JSON format) to file

4. From http://crobi.github.io/rpg-cards/generator/generate.html click 'Load from File' and select the file you just created above.

Configuration
=
The script can be configured by modifying the const values in JsonGenerator.php

Configuration Options

1. If you run this script on a MAC OS make sure to change const RUNNING_ON_MAC to true

2. Icons used on card based on monster type

3. The name of the field/column used in the tab delimited file

Required Fields
=
If a value does not exist for a required field, just it empty

Here is the list of fields/columns required by the script along with the default names expected (within single quotes). These name can be changed as described above in Configuration. 

* MONSTER_NAME = 'Monster'         
* TYPE = 'Type'
* SUBTYPE = 'Subtype'
* SIZE = 'Size'
* ALIGNMENT = 'Align'
* STR = 'Str'
* DEX = 'Dex'
* CON = 'Con'
* INT = 'Int'
* WIS = 'Wis'
* CHA = 'Cha'
* AC = 'AC'
* HP = 'HP'
* SPEED = 'Speed'
* OTHER_SPEED = 'Other Speed'

Note: changing the following value will also affect the labels on the card.

* SAVES = 'Saves'
* SKILLS = 'Skills'
* DAMAGE_VULNER = 'Damage Vulnerabilities'
* DAMAGE_RESIST = 'Damage Resistances'
* DAMAGE_IMMUNE = 'Damage Immunities'
* CONDITION_IMMUNE = 'Condition Immunities'
* SENSES = 'Senses'
* LANGUAGES = 'Languages'

The following fields/columns are required and cannot be changed. Again if monster does not have a value just leave empty

* Trait1
* Trait2
* Trait3
* Trait4
* Trait5
* Action1
* Action2
* Action3
* Action4
* Action5
* Reaction1
* Reaction2
* Reaction3

Card Size Notes
=
Except for simplest monsters, 2.5 x 3.5 just are not large enough for all the content. Even 3x5 can sometimes be too small for the more complex monsters. The script will not handle automatically splitting content among multiple cards. You'll have to do that manually.
