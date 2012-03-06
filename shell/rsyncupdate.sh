#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
export LANG=fr_FR.utf-8

########
# Mediboard SVN updater and rsyncer
########

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <action> [-r <revision>]"
  echo "  <action> The action to perform : info|real|noup"
  echo "     info: Shows the update log, no rsync"
  echo "     real: Performs the actual update and the rsync"
  echo "     noup: No update, only rsync"
  echo "  -r <revision> The revision number you want to update to, HEAD by default"
  exit 1
fi

action=$1

# Update
if [ "$action" != "noup" ]
then
  sh $BASH_PATH/update.sh $action $2 $3
  check_errs $? "Wrong paramaters" "Successfully updated"
fi

# Rsyncing -- Parsing rsyncupdate.conf
if [ "$action" != "info" ]
then

  while read line
  do
    first_character=`expr substr "$line" 1 1`
    # Skip comment lines and empty lines
    if [ "$first_character" != "#" ] && [ "$first_character" != "" ]
    then
      echo "-- Rsync $line --"
      rsync -avpz --stats $BASH_PATH/.. --delete $line \
        --exclude includes/config_overload.php \
        --exclude tmp \
        --exclude lib \
        --exclude includes/config.php \
        --exclude files \
        --exclude images/pictures/logo_custom.png
      check_errs $? "Failed to rsync $line" "Succesfully rsync-ed $line"
      scp $BASH_PATH/../tmp/svnlog.txt $line/tmp/svnlog.txt
      scp $BASH_PATH/../tmp/svnstatus.txt $line/tmp/svnstatus.txt
    fi
  done < $BASH_PATH/rsyncupdate.conf

fi
exit 1
