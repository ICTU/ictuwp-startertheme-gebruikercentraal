#!/bin/sh

GREEN='\033[0;32m'
NC='\033[0m' # No Color

deploypath="www/wp-content/themes/ictuwp-theme-gc2020"

# set proper folder locations
WWWROOT="/home/acccentraal/www/wp-content"

#translations
LANGSOURCEDIR="${WWWROOT%%/}/themes/gebruiker-centraal/languages/"
LANGSOURCEFILES="${WWWROOT%%/}/themes/gebruiker-centraal/languages/**"

LANGTARGET="${WWWROOT%%/}/languages/themes/gebruiker-centraal"


PREFIX="gebruiker-centraal-"

ssh -T acccentraal@ictu-web-a02.sc.nines.nl << EOF

printf "\n\n"
printf "********************************"
printf "\n"
printf "\n ${GREEN}Deploy Gebruiker Centraal [ACC] ${NC}"
printf "\n ${GREEN}Logged in at Gebruiker Centraal ACC ${NC} \n"
printf "\n Servername:    ${GREEN}ictu-web-a02.sc.nines.nl ${NC}"
printf "\n Pad:           ${GREEN}$deploypath ${NC}"
printf "\n Branch:        ${GREEN}development ${NC} \n"
printf "\n"
printf "********************************"
printf "\n"

cd $deploypath

printf "\n${GREEN}Branch ${NC}\n\n"
git branch

printf "\n"
printf "********************************"
printf "\n\n"


printf "\n${GREEN}Git pull ${NC}\n\n"
git pull

printf "\n"
printf "********************************"
printf "\n\n"


EOF