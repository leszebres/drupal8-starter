#!/usr/bin/env bash

#
# Local custom actions with lando
#
# @use "sh lando.sh <arg>"
# @example "sh lando.sh up"
#

exportFolder="./_sql"

# Export database with lando
function dump {
    [ -d $exportFolder ] || mkdir -v $exportFolder
    cd $exportFolder && lando db-export
    cd -
}

# Fully update Drupal
function up {
    dump
    lando composer update
    lando drush updatedb -y
    trans
    lando drush locale-update -y
    lando drush cr -y
    theme_dependencies
    dump
}

# Import custom themes translations
function trans {
    for filename in web/themes/custom/drup_theme/translations/*
    do
        echo "$filename"
        lando drush langimp themes/custom/drup_theme/translations/$(basename "$filename")
    done
    for filename in web/themes/custom/drup_admin/translations/*
    do
        echo "$filename"
        lando drush langimp themes/custom/drup_admin/translations/$(basename "$filename")
    done
}

# Update drup_theme's node dependencies with yarn
function theme_dependencies {
    cd ./web/themes/custom/drup_theme/ && lando yarn upgrade
}

# Compile dependencies for contrib theme Claro
function claro {
    cd ./web/themes/contrib/claro/
    lando yarn
    lando yarn install
    lando yarn build:js
    lando yarn build:css
    cd ./
}

# Shell prompt
function readUser {
    echo "----- Welcome :"
    echo "1) Database export in $exportFolder"
    echo "2) Drupal update (composer, db update, translations, cache rebuild)"
    echo "3) Custom themes translations import"
    echo "4) Update drup_theme's node dependencies with yarn"
    echo "----------------------------"

    read -p "Make your choice : " action
    echo "----------------------------"
    if [ "action" == "" ]
    then
        echo "Maybe another time ..."
        exit
    fi

    case $action in
        1)
            dump;;
        2)
            up;;
        3)
            trans;;
        4)
            theme_dependencies;;
        *)
            echo "Nothing matches :("
            exit 1
    esac
}



argAction=$1
if [ -z $argAction ];
then
    readUser
else
    # Launch command directly
    eval ${argAction}
fi
