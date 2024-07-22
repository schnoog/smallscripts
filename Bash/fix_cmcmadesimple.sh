#!/bin/bash
# Fast fix for the CMSMS bug #12660 I reported in October 2023 (V2.2.18 - unti current 2.2.21)
# http://dev.cmsmadesimple.org/bug/view/12660
# cd into you cmsmadesimple directory and execute the following command
# curl -sSL "https://raw.githubusercontent.com/schnoog/smallscripts/master/Bash/fix_cmcmadesimple.sh" | bash


TOFIND="case 'output'"
TOADD="case 'outputfilter'"
CF=$(find . -name 'class.Smarty_CMS.php')

echo "THIS REPLACES $TOFIND WITH $TOADD"
echo "IN THE FILE $CF"
grep "'outputfilter'" "$CF" >/dev/null && echo "--- file is already fixed --- exiting" && exit 0



echo "Applying fix"

sed -i "s/$TOFIND/$TOADD/g" "$CF"

echo "Fix applied, going now check it"
sleep 0.5
grep "'outputfilter'" "$CF" || echo "Something failed" && exit 1
exit 0
