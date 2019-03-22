#!/bin/bash

#
#   Einfaches Datenbank und Rollback System
#   
#




done=""
STEP=""
WorkDir="/c/phpDEV/mysymfony/public/WorkDir/"
BackupName="Momentaufnahme"
Database="mysymfony"
DBUser="mysymfony"
DBPass=$(cat /pk/mysymfone.txt)

CUTL=${#BackupName}

function GetBackupList {
    BSS="$BackupName*.sql"
    MYIFS=$IFS
    IFS="
    "
    LISTE=""
    ALL=$(find "$WorkDir" -name "$BSS" -exec basename "{}" ".sql" \; )
    for BACKUP in $ALL
    do
        num=${BACKUP:CUTL}
        if [ "$num" != "" ]
        then
            BD=$(/usr/bin/stat "$BACKUP"".sql" | grep Birth | awk '{print $2 " " $3}' | cut -d '.' -f 1)
            LISTE="$LISTE""$num "$(basename $BACKUP)"           vom $BD""\n"
        fi 
    done

    echo -e $LISTE | sort -n
    echo "Einspielen eines Backup: $0 <BackupID>"
    echo $MAX
    IFS=$MYIFS
}



function GetMax {
    MAX=0
    MYIFS=$IFS
    IFS="
    "
    ALL=$(find "$WorkDir" -name "$BSS" -exec basename "{}" ".sql" \; )
    for BACKUP in $ALL
    do
        num=${BACKUP:CUTL}
        if [ "$num" != "" ]
        then
            if [ $num -gt $MAX ]
            then
                MAX=$num
                #echo "New Max: $MAX"
            fi 
        fi 
    done
    echo $MAX
    IFS=$MYIFS
}













if [ "$1" != "" ]
then
        re='^[0-9]+$'

        if ! [[ $1 =~ $re ]] ; then
            if [ "$1" == "backup" ]
            then
                set -f
                BSS="$BackupName*.sql"
                lastid=$(GetMax)
                let nextid=$lastid+1
                bash -c  "mysqldump -u""$DBUser"" -p""$DBPass"" $Database > ""$WorkDir""$BackupName""$nextid"".sql"
                done=1
                set +f
            fi
            if [ "$1" == "liste" ]
            then
                set -f
                GetBackupList
                set +f
                done=1
            fi

        
        
        else
            STEP="$1"
            bash -c "mysql -u""$DBUser"" -p""$DBPass"" $Database < ""$WorkDir""$BackupName""$STEP"".sql"
            done=1
        fi

fi

if [ "$done" == "" ]
then
    echo "Verwendung:"
    echo "  $0 backup           - erzeugt ein neues backup"
    echo "  $0 liste            - zeigt die vorhandenen Backups an"
    echo "  $0 <nummer>         - Spielt das Backup mit der angegebenen Nummer ein"
fi


