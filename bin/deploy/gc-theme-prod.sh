#!/bin/sh

GREEN='\033[0;32m'
NC='\033[0m' # No Color

deploypath="www/wp-content/themes/ictuwp-theme-gc2020"

ssh -T centraal@ictu-web-p02.sc.nines.nl << EOF

printf "\n\n"
printf "********************************"
printf "\n"
printf "\n ${GREEN}Deploy Gebruiker Centraal [PROD] ${NC}"
printf "\n ${GREEN}Logged in at Gebruiker Centraal PROD ${NC} \n"
printf "\n Servername:    ${GREEN}ictu-web-p02.sc.nines.nl ${NC}"
printf "\n Pad:           ${GREEN}$deploypath ${NC}"
printf "\n Branch:        ${GREEN}master ${NC} \n"
printf "\n"
printf "********************************"
printf "\n"

cd $deploypath

git checkout master

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

exit

EOF