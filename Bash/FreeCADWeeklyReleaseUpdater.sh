#!/usr/bin/bash


#Small script which is places along the "FreeCAD_weekly-builds.AppImage"for which I created a startlet
#Running it looks for the most recent weekly release of FreeCAD and replaces the "FreeCAD_weekly-builds.AppImage" with it

BSP=$(dirname "$(realpath $0)")

cd "$BSP"

ps aux | grep "FreeCAD_weekly-builds.AppImage" | grep -v grep > /dev/null && echo "FreeCAD is running - I'm stopping right now" && exit 1

TARGETNAME="FreeCAD_weekly-builds.AppImage"


FCURL=$(curl https://api.github.com/repos/FreeCAD/FreeCAD-Bundle/releases  2>/dev/null | grep "Linux" | grep "x86_64" | grep 'weekly' | grep 'AppImage"'$ | cut -d '"' -f 4)


#echo "$FCURL"
FN=$(basename "$FCURL")

NEEDDOWNLOAD=1
ls "$FN" >/dev/null 2>&1 && NEEDDOWNLOAD=0

if [ "$NEEDDOWNLOAD" == "1" ]
then
    echo "going to download $FCURL"
    wget "$FCURL"
else
    echo "$FN available"
fi

MDN=$(md5sum "$FN" | cut -d " " -f 1)
MDO=$(md5sum "$TARGETNAME" | cut -d " " -f 1)

echo "New $MDN"
echo "Old $MDO"

if [ "$MDN" != "$MDO" ]
then
    echo "Downloaded version differs from $TARGETNAME - going to replace that one"
    cp "$FN" "$TARGETNAME"
else
    echo "Already up to date"
fi

exit 0
