#!/bin/bash
IPADD=$1
TV="http://$IPADD:8060/keypress/"
H='Home'
R='Rev'
F='Fwd'
P='Play'
S='Select'
LT='Left'
RT='Right'
D='Down'
U='Up'
B='Back'
IR='InstantReplay'
I='Info'
BS='Backspace'
SH='Search'
E='Enter'
 
echo "Change script where necessary in case menu items and order change"
echo "This should be used only on TVs that have been newly setup with no changes made."
echo "You can 10secs to cancel before execution"
sleep 7
echo "Beginning in 3"
sleep 1
echo "...2"
sleep 1
echo "...1"
sleep 1
echo "...0"
 
#Disable Screen Saver
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
 
#Disable Speakers
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
 
#Disable Auto Power Savings
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
 
#Disable Standby LED
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
 
#Disable Fast TV Start
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
 
#Disable USB Media Auto Launch
curl -d '' $TV$H
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$D
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$S
sleep .5
curl -d '' $TV$U
sleep .5
curl -d '' $TV$S
sleep .5
 
#Return Home
curl -d '' $TV$H
