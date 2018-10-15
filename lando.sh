#!/usr/bin/env bash

# sh lando.sh <arg>
# ex : sh lando.sh maj

argAction=$1
exportFolder="./_sql"

# Export database with lando
function dump {
    [ -d $exportFolder ] || mkdir -v $exportFolder
    cd $exportFolder && lando db-export
    cd -
}

# Maj Drupal with composer
function maj {
    dump
    lando composer update
    lando drush updatedb -y
    trans
    lando drush locale-update -y
    lando drush cr -y
}

function trans {
    lando drush langimp themes/custom/drup_theme/translations/drup_theme-8.x-1.0.fr.po
    lando drush langimp themes/custom/drup_admin/translations/drup_admin-8.x-1.0.fr.po
}

function fra {
    lando drush fra
    lando drush cr -y
}

function readUser {
    echo "----- COMMANDS"
    echo "1) Export de la BDD dans $exportFolder"
    echo "2) Mise à jour Drupal 8 via composer"
    echo "3) Mise à jour des traductions des thèmes custom"
    echo "4) Mise à jour des features"

    read -p "Que voulez-vous faire ? : " action
    if [ "action" == "" ]
    then
        echo "Une autre fois peut-être ..."
        exit
    fi

    case $action in
        1)
            dump;;
        2)
            maj;;
        3)
            trans;;
        4)
            fra;;
        *)
            echo "Rien ne correspond"
            exit 1
    esac
}


if [ -z $argAction ];
then
    # Utilisation du prompt pour lister les fonctions
    readUser
else
    # Launch command directly
    eval ${argAction}
fi
