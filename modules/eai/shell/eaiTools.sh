#!/bin/sh

BASH_PATH=$(dirname $0)
ROOT_PATH="$BASH_PATH/../../.."
. $ROOT_PATH/shell/utils.sh

username=$1
password=$2
host="localhost"
instance="mediboard"

args=$(getopt h:i: $*)

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args
for i; do
  case "$i" in
    -h) host=$2; shift 2;;
    -i) instance=$2; shift 2;;
    --) shift ; break ;;
  esac
done

auth="login=1&username=${username}&password=${password}"
log="/var/log/mediboard-eai/eai.tools.log"
doc="/var/log/mediboard-eai/eai.tools.html"

# Make eai path
force_dir /var/log/mediboard-eai/
force_file $log
force_file $doc

# Injection IPP/NDA dans les �changes H'XML
moda="m=eai&a=ajax_inject_master_idex_missing&exchange_class=CExchangeHL7v2"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

# Injection IPP/NDA dans les �changes HL7v2
moda="m=eai&a=ajax_inject_master_idex_missing&exchange_class=CExchangeHL7v2"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

# Envoi des �changes H'XML non envoy�s
moda="m=eai&a=ajax_send_messages&exchange_class=CEchangeHprim"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

# Envoi des �changes HL7v2 non envoy�s
moda="m=eai&a=ajax_send_messages&exchange_class=CExchangeHL7v2"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"
