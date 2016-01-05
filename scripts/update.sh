#!/bin/bash

NC='\033[0m' # No Color
Black='\033[0;30m'
Blue='\033[0;34m'
Green='\033[0;32m'
Cyan='\033[0;36m'
Red='\033[0;31m'
Purple='\033[0;35m'
Orange='\033[0;33m'
LightGray='\033[0;37m'
DarkGray='\033[1;30m'
LightBlue='\033[1;34m'
LightGreen='\033[1;32m'
LightCyan='\033[1;36m'
LightRed='\033[1;31m'
LightPurple='\033[1;35m'
Yellow='\033[1;33m'
White='\033[1;37m'

SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd '..'

clear

echo -e ""
echo -e "${LightBlue}### Base App Update ###${NC}"

git pull origin master

rm -rf build/
rm -rf deploy/


echo -e ""
echo -e "${LightBlue}### Plugins Update ###${NC}"

cd plugins/VR

for dir in *
do
    echo -e ""
    echo -e "-> $dir"

    cd $dir

    git pull

    cd ..
done

cd ../..


echo -e ""
echo -e "${LightBlue}### Composer ###${NC}"

echo -e -n "${Yellow}Do you want to install Composer dependencies? [y/n] ${NC}"
read answer
if [[ $answer = y ]] ; then
    echo -e -n "${Yellow}Do you want to install Composer development dependencies? [y/n] ${NC}"
    read answer
    if [[ $answer = y ]] ; then
        composer install
    else
        composer install --no-dev --optimize-autoloader
    fi
fi


echo -e ""
echo -e "${LightBlue}### Database update ###${NC}"

echo -e "SQL queries to execute by schema:update script:"
echo -e "${Orange}"
php app/console doctrine:schema:update --dump-sql
echo -e "${NC}"

echo -e -n "${Yellow}Do you want to execute above queries? [y/n] ${NC}"
read answer
if [[ $answer = y ]] ; then
  php app/console doctrine:schema:update --force
fi


echo -e ""
echo -e "${LightBlue}### Cache ###${NC}"

echo -e -n "${Yellow}Do you want to clear the cache? [y/n] ${NC}"
read answer
if [[ $answer = y ]] ; then
    php app/console cache:clear --env=prod
fi


echo -e ""
echo -e "${LightBlue}### Configuration tests ###${NC}"

echo -e -n "${Yellow}Do you want to run configuration tests? [y/n] ${NC}"
read answer
if [[ $answer = y ]] ; then
    php app/console sugarcrm:test-connections
fi

echo -e "${Green}Update finished.${NC}"