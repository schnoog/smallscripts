#!/bin/bash
# A simple mysqldump script which is used for my developments
# cronned each minute on a small database, only real changed backups are 
saved
#


BASEPATH="/var/www/html/OWN_DB_Backups/"
DBN="ownboilerplate"


LASTBU="$BASEPATH""last.sql"
CURRBU="$BASEPATH""current.sql"
BUFN="$BASEPATH""BACKUP-"$(date "+%Y%m%d-%H%M%S")"_""$DBN"".sql"

touch "$CURRBU"
cp "$CURRBU" "$LASTBU"

#echo "$LASTBU --- $CURRBU --  $BUFN"
mysqldump "$DBN" > "$CURRBU"

DC=$(diff "$CURRBU" "$LASTBU" | wc -l)
if [ $DC -gt 4 ]
then
echo "CHANGED"
cp "$CURRBU" "$BUFN"
gzip "$BUFN"
else
echo "UNCHANGED"
fi

